<?php

namespace mageekguy\tests\unit;

use mageekguy\tests\unit;
use mageekguy\tests\unit\asserter;

abstract class test
{
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

		$this->setUp();

		foreach ($testMethods as $testMethod)
		{
			try
			{
				$this->{$testMethod}();
			}
			catch (asserter\exception $exception) {}
			catch (\exception $exception)
			{
				var_dump($exception);
			}
		}

		$this->tearDown();

		return $this;
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
