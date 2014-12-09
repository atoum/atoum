<?php

namespace mageekguy\atoum\writers;

use
	mageekguy\atoum,
	mageekguy\atoum\reports,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\report\writers
;

class http extends atoum\writer implements writers\asynchronous
{
	protected $url = null;
	protected $method = null;
	protected $parameter = null;
	protected $headers = array();

	public function __construct(atoum\adapter $adapter = null)
	{
		parent::__construct($adapter);

		$this->setMethod();
	}

	public function writeAsynchronousReport(reports\asynchronous $report)
	{
		return $this->write((string) $report);
	}

	public function clear()
	{
		return $this;
	}

	public function addHeader($name, $value)
	{
		$this->headers[$name] = $value;

		return $this;
	}

	public function getHeaders()
	{
		return $this->headers;
	}

	public function setMethod($method = null)
	{
		$this->method = $method ?: 'GET';

		return $this;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function setParameter($parameter = null)
	{
		$this->parameter = $parameter;

		return $this;
	}

	public function getParameter()
	{
		return $this->parameter;
	}

	public function setUrl($url)
	{
		$this->url = $url;

		return $this;
	}

	public function getUrl()
	{
		return $this->url;
	}

	protected function doWrite($string)
	{
		if ($this->url === null)
		{
			throw new exceptions\runtime('No URL set for HTTP writer');
		}

		$headers = array();

		foreach ($this->headers as $name => $value)
		{
			$headers[] = sprintf('%s: %s', $name, $value);
		}

		$context = $this->adapter->stream_context_create(array(
			'http' => array(
				'method' => $this->method,
				'header' => join("\r\n", $headers),
				'content' => $this->parameter ? http_build_query(array($this->parameter => $string)) : $string
			)
		));

		if (@$this->adapter->file_get_contents($this->url, false, $context) === false)
		{
			throw new atoum\writers\http\exception('Unable to write coverage report to ' . $this->url);
		}

		return $this;
	}
}
