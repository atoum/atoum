<?php

namespace mageekguy\atoum\fcgi;

use
	mageekguy\atoum\fcgi\records\requests
;

class request
{
	protected $params = null;
	protected $stdin = null;
	protected $requestId = null;

	public function __construct()
	{
		$this->stdin = new requests\stdin();
		$this->params = new requests\params();
	}

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
		$this->stdin->setContentData($stdin);

		return $this;
	}

	public function unsetStdin()
	{
		return $this->setStdin('');
	}

	public function stdinIsSet()
	{
		return ($this->stdin->getContentData() != '');
	}

	public function getStdin()
	{
		return $this->stdin->getContentData();
	}

	public function getParam($name)
	{
		return $this->params->{$name};
	}

	public function setParam($name, $value)
	{
		$this->params->{$name} = $value;
	}

	public function unsetParam($name)
	{
		unset($this->params->{$name});

		return $this;
	}

	public function paramIsSet($name)
	{
		return isset($this->params->{$name});
	}

	public function getParams()
	{
		return $this->params->getValues();
	}

	public function getRequestId()
	{
		return $this->requestId;
	}

	public function getStreamData(stream $stream)
	{
		$streamData = '';

		$this->requestId = $stream->generateRequestId();

		$begin =  new requests\begin(requests\begin::responder, $stream->isPersistent(), $this->requestId);
		$streamData .=  $begin->getStreamData();

		if (sizeof($this->params) > 0)
		{
			$streamData .= $this->params->setRequestId($this->requestId)->getStreamData();

			$endOfParams = new requests\params(array(), $this->requestId);
			$streamData .= $endOfParams->getStreamData();
		}

		$streamData .= $this->stdin->setRequestId($this->requestId)->getStreamData();

		$endOfStdin = new requests\stdin('', $this->requestId);
		$streamData .= $endOfStdin->getStreamData();

		return $streamData;
	}

	private static function cleanName($name)
	{
		return strtoupper(trim($name));
	}
}
