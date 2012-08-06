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
	protected $persistent = false;
	protected $socket = null;
	protected $responses = array();

	public function __construct($address = 'tcp://127.0.0.1:9000', $timeout = 30, $persistent = false, atoum\adapter $adapter = null)
	{
		$this->address = (string) $address;
		$this->timeout = (int) $timeout;
		$this->persistent = (bool) $persistent;

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
		return $this->persistent;
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
			$flags = STREAM_CLIENT_CONNECT;

			if ($this->isPersistent() === true)
			{
				$flags |= STREAM_CLIENT_PERSISTENT;
			}

			$socket = $this->adapter->invoke('stream_socket_client', array($this->address, & $errorCode, & $errorMessage, $this->timeout, $flags));

			if ($socket === false)
			{
				throw new stream\exception($errorMessage, $errorCode);
			}

			$this->socket = $socket;
		}

		return $this;
	}

	public function close()
	{
		if ($this->isOpen() === true)
		{
			$this->adapter->fclose($this->socket);

			$this->socket = null;
		}

		return $this;
	}

	public function write(request $request)
	{
		return $this->writeToSocket($request->getStreamData($this));
	}

	public function read()
	{
		if ($this->isOpen() === false)
		{
			throw new stream\exception('Stream \'' . $this . '\' is not open');
		}

		$responses = array();

		list($type, $requestId, $contentLength, $padding) = self::getValues($this->readFromSocket(8));

		if ($type !== null)
		{
			if ($contentLength > 0)
			{
				$contentData = $this->readFromSocket($contentLength + $padding);

				if ($padding > 0)
				{
					$contentData = substr($contentData, 0, - $padding);
				}
			}

			switch ($type)
			{
				case responses\stdout::type:
					$record = new responses\stdout($requestId, $contentData);
					break;

				case responses\stderr::type:
					$record = new responses\stderr($requestId, $contentData);
					break;

				case responses\end::type:
					$record = new responses\end($requestId, $contentData);
					break;

				default:
					throw new record\exception('Type \'' . $type . '\' is unknown');
			}

			if (isset($this->responses[$requestId]) === false)
			{
				$this->responses[$requestId] = new response($requestId);
			}

			if ($record->addToResponse($this->responses[$requestId])->getType() == responses\end::type)
			{
				$responses[$requestId] = $this->responses[$requestId];

				unset($this->responses[$requestId]);
			}
		}

		return $responses;
	}

	public function generateRequestId()
	{
		$id = 1;

		while (isset($this->responses[$id]) === true)
		{
			$id++;
		}

		return (string) $id;
	}

	protected function writeToSocket($data)
	{
		if ($this->adapter->fwrite($this->open()->socket, $data, strlen($data)) === false)
		{
			throw new stream\exception('Unable to write data \'' . $data . '\' in stream \'' . $this . '\'');
		}

		return $this;
	}

	protected function readFromSocket($length)
	{
		$data = $this->adapter->fread($this->socket, $length);

		if ($data === false)
		{
			throw new stream\exception('Unable to read record from stream \'' . $this . '\'');
		}

		return $data;
	}

	protected static function getValues($streamData)
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

	protected static function getValue($valueB0, $valueB1)
	{
		return (ord($valueB0) << 8) + ord($valueB1);
	}
}
