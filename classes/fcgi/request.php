<?php

namespace mageekguy\atoum\fcgi;

use
	mageekguy\atoum\fcgi\records,
	mageekguy\atoum\fcgi\records\requests
;

class request
{
	protected $params = array();
	protected $stdin = '';

	public function __set($name, $value)
	{
		return (self::cleanName($name) === 'STDIN' ? $this->setStdin($value) : $this->setParam($name, $value));
	}

	public function __get($name)
	{
		return (strtoupper($name) === 'STDIN' ? $this->getStdin() : $this->getParam($name));
	}

	public function __isset($name)
	{
		return (self::cleanName($name) === 'STDIN' ? $this->stdinIsSet() : $this->paramIsSet($name));
	}

	public function __unset($name)
	{
		return (self::cleanName($name) === 'STDIN' ? $this->unsetStdin() : $this->unsetParam($name));
	}

	public function setStdin($stdin)
	{
		$this->stdin = (string) $stdin;

		return $this;
	}

	public function unsetStdin()
	{
		return $this->stdin = '';
	}

	public function stdinIsSet()
	{
		return ($this->stdin != '');
	}

	public function getStdin()
	{
		return $this->stdin;
	}

	public function getParam($name)
	{
		return (isset($this->params[$name]) === false ? null : $this->params[$name]);
	}

	public function setParam($name, $value)
	{
		$this->params[$name] = $value;
	}

	public function unsetParam($name)
	{
		if (isset($this->params[$name]) === true)
		{
			unset($this->params[$name]);
		}

		return $this;
	}

	public function paramIsSet($name)
	{
		return isset($this->params[$name]);
	}

	public function getParams()
	{
		return $this->params;
	}

	public function getRecords(stream $stream)
	{
		$records = new records\collection();

		$requestId = $stream->generateRequestId();

		$records[] = new requests\begin(requests\begin::responder, $stream->isPersistent(), $requestId);

		if (sizeof($this->params) > 0)
		{
			$records[] = new requests\params($this->params, $requestId);
			$records[] = new requests\params(array(), $requestId);
		}

		$records[] = new requests\stdin($this->stdin, $requestId);
		$records[] = new requests\stdin('', $requestId);

		return $records;
	}

	private static function cleanName($name)
	{
		return strtoupper(trim($name));
	}
}
