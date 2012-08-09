<?php

namespace mageekguy\atoum\fcgi;

class response
{
	protected $request = null;
	protected $stdout = '';
	protected $stderr = '';

	public function __construct(request $request)
	{
		$this->request = $request;
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function getStdout()
	{
		return $this->stdout;
	}

	public function addToStdout(records\response $record)
	{
		return $this->addToStream($this->stdout, $record);
	}

	public function getStderr()
	{
		return $this->stderr;
	}

	public function addToStderr(records\response $record)
	{
		return $this->addToStream($this->stderr, $record);
	}

	public function getHttpBody()
	{
		$endOfHttpHeaders = strpos($this->stdout, "\r\n\r\n");

		return ($endOfHttpHeaders === false ? '' : substr($this->stdout, $endOfHttpHeaders + 4));
	}

	private function addToStream(& $stream, records\response $record)
	{
		$stream .= $record->getContentData();

		return $this;
	}
}
