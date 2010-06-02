<?php

namespace mageekguy\tests\unit;

class score
{
	protected $assertions = array();
	protected $exceptions = array();
	protected $errors = array();

	public function addPass($file, $line, $class, $method, $asserter)
	{
		$this->assertions[] = array(
			'class' => $class,
			'method' => $method,
			'file' => $file,
			'line' => $line,
			'asserter' => $asserter,
			'fail' => null
		);

		return $this;
	}

	public function addFail($file, $line, $class, $method, $asserter, $reason)
	{
		$this->assertions[] = array(
			'class' => $class,
			'method' => $method,
			'file' => $file,
			'line' => $line,
			'asserter' => $asserter,
			'fail' => $reason
		);

		return $this;
	}

	public function addException($file, $line, $class, $method, \exception $exception)
	{
		$this->exceptions[] = array(
			'class' => $class,
			'method' => $method,
			'file' => $file,
			'line' => $line,
			'exception' => $exception
		);

		return $this;
	}

	public function addError($file, $line, $class, $method, $type, $message)
	{
		$this->errors[] = array(
			'class' => $class,
			'method' => $method,
			'file' => $file,
			'line' => $line,
			'type' => $type,
			'message' => $message
		);

		return $this;
	}

	public function merge(\mageekguy\tests\unit\score $score)
	{
		foreach ($score->assertions as $assertion)
		{
			$this->assertions[] = $assertion;
		}

		foreach ($score->exceptions as $exception)
		{
			$this->exceptions[] = $exception;
		}

		foreach ($score->errors as $error)
		{
			$this->errors[] = $error;
		}

		return $this;
	}

	public function getFailAssertions()
	{
		$assertions = array();

		foreach ($this->assertions as $assertion)
		{
			if ($assertion['fail'] !== null)
			{
				$assertions[] = $assertion;
			}
		}

		return $assertions;
	}

	public function getFailNumber()
	{
		return sizeof($this->getFailAssertions());
	}

	public function getErrorNumber()
	{
		return sizeof($this->errors);
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function getExceptionNumber()
	{
		return sizeof($this->exceptions);
	}

	public function getExceptions()
	{
		return $this->exceptions;
	}
}

?>
