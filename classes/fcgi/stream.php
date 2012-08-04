<?php

namespace mageekguy\atoum\fcgi;

use
	mageekguy\atoum
;

class stream
{
	protected $address = '';
	protected $timeout = 30;
	protected $persistent = false;
	protected $socket = null;

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

	public function write($data)
	{
		if ($data != '')
		{
			if ($this->adapter->fwrite($this->open()->socket, $data, strlen($data)) === false)
			{
				throw new stream\exception('Unable to write \'' . $data . '\' in stream \'' . $this . '\'');
			}
		}

		return $this;
	}

	public function read($length = 8)
	{
		if ($this->isOpen() === false)
		{
			throw new stream\exception('Stream \'' . $this . '\' is not open');
		}

		if (($data = $this->adapter->fread($this->socket, $length)) === false)
		{
			throw new stream\exception('Unable to read in stream \'' . $this . '\'');
		}

		return $data;
	}
}
