<?php

namespace mageekguy\atoum\fcgi;

use
	mageekguy\atoum,
	mageekguy\atoum\fcgi\record,
	mageekguy\atoum\fcgi\records\responses
;

class stream
{
	const version = 1;

	protected $input = '';
	protected $output = '';
	protected $address = '';
	protected $timeout = 30;
	protected $socket = null;
	protected $records = array();
	protected $requests = array();

	public function __construct($address = 'tcp://127.0.0.1:9000', $timeout = 30, atoum\adapter $adapter = null)
	{
		$this->address = (string) $address;
		$this->timeout = (int) $timeout;

		$this->setAdapter($adapter ?: new atoum\adapter());
	}

	public function __destruct()
	{
		$this->close();
	}

	public function __toString()
	{
		return $this->address;
	}

	public function __invoke(request $request = null)
	{
		return ($request === null ? $this->read() : $this->write($request));
	}

	public function getAddress()
	{
		return $this->address;
	}

	public function getTimeout()
	{
		return $this->timeout;
	}

	public function isPersistent()
	{
		return true;
	}

	public function setAdapter(atoum\adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function isOpen()
	{
		return ($this->socket !== null);
	}

	public function open()
	{
		if ($this->isOpen() === false)
		{
			$socket = @$this->adapter->invoke('stream_socket_client', array($this->address, & $errorCode, & $errorMessage, $this->timeout, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT));

			if ($socket === false)
			{
				throw new stream\exception('Unable to connect to \'' . $this . '\': ' . $errorMessage, $errorCode);
			}

			$this->socket = $socket;

			$this->adapter->socket_set_blocking($this->socket, 0);
		}

		return $this;
	}

	public function close()
	{
		if ($this->isOpen() === true)
		{
			$this->adapter->fclose($this->socket);

			$this->socket = null;
			$this->records = array();
			$this->requests = array();
		}

		return $this;
	}

	public function write(request $request)
	{
		$this->open();

		$records = $request->getRecords($this);
		$this->output .= $records->getStreamData();

		$requestId = $records->getRequestId();
		$this->requests[$requestId] = clone $request;
		$this->records[$requestId] = array();

		$read = null;
		$write = array($this->socket);
		$except = null;

		if ($this->adapter->invoke('stream_select', array(& $read, & $write, & $except, 0)) > 0)
		{
			$output = @$this->adapter->fwrite($this->socket, $this->output);

			if ($output === false)
			{
				throw new stream\exception('Unable to write data \'' . $this->output . '\' in stream \'' . $this . '\'');
			}

			if ($output > 0)
			{
				$this->output = substr($this->output, $output);
			}
		}

		return $this;
	}

	public function read()
	{
		if ($this->isOpen() === false)
		{
			throw new stream\exception('Stream \'' . $this . '\' is not open');
		}

		$responses = array();

		$read = array($this->socket);
		$write = null;
		$except = null;

		if ($this->adapter->invoke('stream_select', array(& $read, & $write, & $except, 0)) > 0)
		{
			$input = @$this->adapter->stream_get_contents($this->socket);

			if ($input === false)
			{
				throw new stream\exception('Unable to read from stream \'' . $this . '\'');
			}

			$this->input .= $input;
		}

		while (strlen($this->input) >= 8)
		{
			list($type, $requestId, $contentLength, $padding) = self::getValues(substr($this->input, 0, 8));

			if (strlen($this->input) >= 8 + $contentLength + $padding)
			{
				$contentData = substr($this->input, 8, $contentLength);

				$this->input = (string) substr($this->input, 8 + $contentLength + $padding);

				switch ($type)
				{
					case responses\stdout::type:
						$this->records[$requestId][] = new responses\stdout($requestId, $contentData);
						break;

					case responses\stderr::type:
						$this->records[$requestId][] = new responses\stderr($requestId, $contentData);
						break;

					case responses\end::type:
						$response = new response($this->requests[$requestId]);
						unset($this->requests[$requestId]);

						foreach ($this->records[$requestId] as $record)
						{
							$record->addToResponse($response);
						}

						unset($this->records[$requestId]);

						$record = new responses\end($requestId, $contentData);
						$record->addToResponse($response);

						$responses[] = $response;
						break;

					default:
						throw new record\exception('Type \'' . $type . '\' is unknown');
				}
			}
		}

		return $responses;
	}

	public function generateRequestId()
	{
		$id = 1;

		while (isset($this->requests[$id]) === true)
		{
			$id++;
		}

		return (string) $id;
	}

	public function waitResponses()
	{
		return (sizeof($this->requests) > 0);
	}

	public function getPendingRequests()
	{
		return array_values($this->requests);
	}

	private static function getValues($streamData)
	{
		$values = null;

		if (strlen($streamData) >= 7)
		{
			if (ord($streamData[0]) == self::version)
			{
				$values = array(
					ord($streamData[1]),
					self::getValue($streamData[2], $streamData[3])
				);

				$contentLength = self::getValue($streamData[4], $streamData[5]);

				if ($contentLength > 0)
				{
					$values[] = $contentLength;
					$values[] = ord($streamData[6]);
				}
			}
		}

		return $values;
	}

	private static function getValue($valueB0, $valueB1)
	{
		return (ord($valueB0) << 8) + ord($valueB1);
	}
}
