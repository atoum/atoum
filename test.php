<?php

namespace mageekguy\tests\unit;

use mageekguy\tests\unit;
use mageekguy\tests\unit\asserter;

abstract class test
{
	const version = '$Rev$';
	const author = 'Frédéric Hardy';
	const testMethodPrefix = 'test';

	protected $score = null;
	protected $assert = null;

	private $class = '';
	private $path = '';
	private $testMethods = array();
	private $currentMethod = null;

	public function __construct()
	{
		$this->score = new unit\score();
		$this->assert = new unit\asserter($this->score);

		$class = new \reflectionClass($this);

		$this->class = $class->getName();

		$this->path = $class->getFilename();

		foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $publicMethod)
		{
			$methodName = $publicMethod->getName();

			if (strpos($methodName, self::testMethodPrefix) === 0)
			{
				$this->testMethods[] = $methodName;
			}
		}
	}

	public function getClass()
	{
		return $this->class;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getScore()
	{
		return $this->score;
	}

	public function getVersion()
	{
		return substr(self::version, 6, -2);
	}

	public function getTestMethods()
	{
		return $this->testMethods;
	}

	public function run(array $testMethods = array())
	{
		if (sizeof($testMethods) <= 0)
		{
			$testMethods = $this->testMethods;
		}

		try
		{
			$this->setUp();

			set_error_handler(array($this, 'errorHandler'));

			foreach ($testMethods as $testMethod)
			{
				if (in_array($testMethod, $this->testMethods) === false)
				{
					throw new \runtimeException('Test method ' . $this->getClass() . '::' . $testMethod . '() is undefined');
				}

				$this->currentMethod = $testMethod;

				try
				{
					$this->{$testMethod}();
				}
				catch (asserter\exception $exception)
				{
					# Do nothing, just break execution of current method because an assertion failed.
				}
				catch (\exception $exception)
				{
					list($file, $line, $class, $method) = $this->getBacktrace();
					$this->score->addException($file, $line, $class, $method, $exception);
				}
			}

			restore_error_handler();

			$this->currentMethod = null;

			$this->tearDown();
		}
		catch (\exception $exception)
		{
			$this->tearDown();
			throw $exception;
		}

		return $this;
	}

	public function errorHandler($errno, $errstr, $errfile, $errline, $context)
	{
		if (error_reporting() !== 0)
		{
			list($file, $line, $class, $method) = $this->getBacktrace();
			$this->score->addError($file, $line, $class, $method, $errno, $errstr);
		}

		return true;
	}

	protected function setUp()
	{
		return $this;
	}

	protected function tearDown()
	{
		return $this;
	}

	protected function getBacktrace()
	{
		$debugBacktrace = debug_backtrace();

		foreach ($debugBacktrace as $key => $value)
		{
			if (isset($value['object']) === true && isset($value['function']) === true && $value['object'] === $this && $value['function'] === $this->currentMethod)
			{
				return array(
					$debugBacktrace[$key - 1]['file'],
					$debugBacktrace[$key - 1]['line'],
					$value['class'],
					$value['function']
				);
			}
		}

		return null;
	}
}

?>
