<?php

namespace mageekguy\tests\unit;

class score
{
	protected $failNumber = 0;
	protected $passNumber = 0;
	protected $assertions = array();
	protected $exceptionNumber = 0;
	protected $exceptions = array();
	protected $errorNumber = 0;
	protected $errors = array();

	public function addPass($file, $line, $class, $method, $asserter)
	{
		$this->passNumber++;

		$this->assertions[$class][$method][] = array(
			'file' => $file,
			'line' => $line,
			'asserter' => $asserter,
			'fail' => null
		);

		return $this;
	}

	public function addFail($file, $line, $class, $method, $asserter, $reason)
	{
		$this->failNumber++;

		$this->assertions[$class][$method][] = array(
			'file' => $file,
			'line' => $line,
			'asserter' => $asserter,
			'fail' => $reason
		);

		return $this;
	}

	public function addException($file, $line, $class, $method, \exception $exception)
	{
		$this->exceptionNumber++;

		$this->exceptions[$class][$method][] = array(
			'file' => $file,
			'line' => $line,
			'exception' => $exception
		);

		return $this;
	}

	public function addError($file, $line, $class, $method, $type, $message)
	{
		$this->errorNumber++;

		$this->errors[$class][$method][] = array(
			'file' => $file,
			'line' => $line,
			'type' => $type,
			'message' => $message
		);

		return $this;
	}

	public function merge(\mageekguy\tests\unit\score $score)
	{
		$this->passNumber += $score->passNumber;
		$this->failNumber += $score->failNumber;

		foreach ($score->assertions as $class => $methods)
		{
			foreach ($methods as $method => $assertions)
			{
				foreach ($assertions as $assertion)
				{
					$this->assertions[$class][$method][] = $assertion;
				}
			}
		}

		$this->exceptionNumber += $score->exceptionNumber;

		foreach ($score->exceptions as $exception)
		{
			$this->exceptions[] = $exception;
		}

		$this->errorNumber += $score->errorNumber;

		foreach ($score->errors as $file => $lines)
		{
			foreach ($lines as $line => $errors)
			{
				foreach ($errors as $error)
				{
					$this->errors[$file][$line][] = $error;
				}
			}
		}

		return $this;
	}

	public function getFailNumber()
	{
		return $this->failNumber;
	}

	public function getAssertions()
	{
		return $this->assertions;
	}

	public function getErrorNumber()
	{
		return $this->errorNumber;
	}

	public function getErrors()
	{
		return $this->errors;
	}
}

?>
