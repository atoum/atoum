<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\includer
;

class includer
{
	protected $adapter = null;
	protected $errors = array();

	public function __construct(atoum\adapter $adapter = null)
	{
		$this->setAdapter($adapter);
	}

	public function resetErrors()
	{
		$this->errors = array();

		return $this;
	}

	public function setAdapter(atoum\adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new atoum\adapter();

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function includePath($path, \closure $closure = null)
	{
		$this->resetErrors();

		$path = (string) $path;

		$errorHandler = $this->adapter->set_error_handler(array($this, 'errorHandler'));

		$closure = $closure ?: function($path) { include_once($path); };

		$closure($path);

		$this->adapter->restore_error_handler();

		if (sizeof($this->errors) > 0)
		{
			$realpath = parse_url($path, PHP_URL_SCHEME) !== null ? $path : realpath($path) ?: $path;

			if (in_array($realpath, $this->adapter->get_included_files(), true) === false)
			{
				throw new includer\exception('Unable to include \'' . $path . '\'');
			}

			if ($errorHandler !== null)
			{
				foreach ($this->errors as $error)
				{
					call_user_func_array($errorHandler, $error);
				}
			}
		}

		return $this;
	}

	public function errorHandler($error, $message, $file, $line, $context)
	{
		$errorReporting = $this->adapter->error_reporting();

		if ($errorReporting !== 0 && $errorReporting & $error)
		{
			$this->errors[] = array($error, $message, $file, $line, $context);
		}

		return true;
	}
}
