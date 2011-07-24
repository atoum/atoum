<?php

namespace mageekguy\atoum\tests\functional\selenium;

use
	mageekguy\atoum\exceptions
;

class html
{
	protected $url;

	protected $webDriver = null;

	public function __construct($url)
	{
		$this->url = $url;
	}

	public function with(webDriver $webDriver)
	{
		$this->webDriver = $webDriver;

		$this->webDriver->get($this->url);

		return $this;
	}

	public function getTitle()
	{
		if ($this->webDriver == null)
		{
			throw new exceptions\logic\invalidArgument('webDriver must be set');
		}

		return $this->webDriver->getTitle();
	}
}

?>
