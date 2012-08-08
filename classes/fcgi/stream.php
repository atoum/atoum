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

	protected $address = '';
	protected $timeout = 30;
	protected $socket = null;
	protected $records = array();
	protected $requests = array();
	protected $responses = array();

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
			$this->responses = array();
		}

		return $this;
	}

	public function write(request $request)
	{
		$this->open();

		$request = clone $request;

		while ($this->requestWasSent($request) === false)
		{
			$write = array($this->socket);
			$read = array($this->socket);
			$except = null;

			if ($this->adapter->invoke('stream_select', array(& $read, & $write, & $except, 0)) > 0)
			{
				if ($read)
				{
					$this->readRecords();
				}

				if ($write)
				{
					$this->writeRecords($request);
				}
			}
		}

		return $this;
	}

	public function read()
	{
		$responses = $this->readRecords()->responses;

		$this->responses = array();

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

	private function readRecords()
	{
		if ($this->isOpen() === false)
		{
			throw new stream\exception('Stream \'' . $this . '\' is not open');
		}

		list($type, $requestId, $contentLength, $padding) = self::getValues($this->readSocket(8));

		if ($type !== null)
		{
			$contentData = '';

			if ($contentLength > 0)
			{
				$contentData = $this->readSocket($contentLength + $padding);

				if ($padding > 0)
				{
					$contentData = substr($contentData, 0, - $padding);
				}
			}

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

					$this->responses[] = $response;
					break;

				default:
					throw new record\exception('Type \'' . $type . '\' is unknown');
			}
		}

		return $this;
	}

	private function readSocket($length)
	{
		$data = @$this->adapter->fread($this->socket, $length);

		if ($data === false)
		{
			throw new stream\exception('Unable to read record from stream \'' . $this . '\'');
		}

		return $data;
	}

	private function writeRecords(request $request)
	{
		$records = $request->getRecords($this);
		$requestId = $records->getRequestId();

		if (isset($this->writeSocket($records->getStreamData())->requests[$requestId]) === false)
		{
			$this->requests[$requestId] = $request;
			$this->records[$requestId] = array();
		}

		return $this;
	}

	private function writeSocket($data)
	{
		while ($data != '')
		{
			$dataWrited = @$this->adapter->fwrite($this->open()->socket, $data, strlen($data));

			if ($dataWrited === false)
			{
				throw new stream\exception('Unable to write data \'' . $data . '\' in stream \'' . $this . '\'');
			}

			$data = substr($data, $dataWrited);
		}

		return $this;
	}

	private function requestWasSent(request $request)
	{
		return in_array($request, $this->requests, true);
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
