<?php

namespace mageekguy\tests\unit;

use mageekguy\tests\unit;
use mageekguy\tests\unit\asserter;

abstract class test
{
	const name = __CLASS__;
	const version = '$Rev$';
	const author = 'Frédéric Hardy';
	const directory = __DIR__;
	const testMethodPrefix = 'test';

	protected $score = null;
	protected $assert = null;

	private $class = '';
	private $path = '';
	private $testMethods = array();

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
					$this->score->addException($exception);
				}
			}

			restore_error_handler();

			$this->tearDown();
		}
		catch (\exception $exception)
		{
			$this->tearDown();
			throw $exception;
		}

		return $this;
	}

	public function errorHandler($errno, $errstr, $file, $line, $context)
	{
		if (error_reporting() !== 0)
		{
			$this->score->addError($file, $line, $errno, $errstr);
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
}

?>
