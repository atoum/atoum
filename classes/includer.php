<?php

namespace mageekguy\atoum;

class includer
{
	protected $errorHandler = null;

	public function __construct(\closure $errorHandler = null)
	{
		if ($errorHandler !== null)
		{
			$this->setErrorHandler($errorHandler);
		}
	}

	public function getErrorHandler()
	{
		return $this->errorHandler;
	}

	public function setErrorHandler(\closure $errorHandler)
	{
		$this->errorHandler = $errorHandler;

		return $this;
	}

	public function includeOnce($path)
	{
		$oldErrorHandler = null;

		if ($this->errorHandler !== null)
		{
			$oldErrorHandler = set_error_handler($this->errorHandler);
		}

		include_once $path;

		if ($oldErrorHandler !== null)
		{
			restore_error_handler();
		}

		return $this;
	}
}

?>
