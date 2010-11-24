<?php

namespace mageekguy\atoum\score;

use \mageekguy\atoum;
use \mageekguy\atoum\score;
use \mageekguy\atoum\exceptions;

class coverage
{
	protected $lines = array();
	protected $reflectionClassInjector = null;

	public function __construct() {}

	public function getLines()
	{
		return $this->lines;
	}

	public function getReflectionClass($class)
	{
		$reflectionClass = null;

		if ($this->reflectionClassInjector === null)
		{
			$reflectionClass = new \reflectionClass($class);
		}
		else
		{
			$reflectionClass = $this->reflectionClassInjector->__invoke($class);

			if ($reflectionClass instanceof \reflectionClass === false)
			{
				throw new exceptions\runtime\unexpectedValue('Reflection class injector must return a \reflectionClass instance');
			}
		}

		return $reflectionClass;
	}

	public function setReflectionClassInjector(\closure $reflectionClassInjector)
	{
		$closure = new \reflectionMethod($reflectionClassInjector, '__invoke');

		if ($closure->getNumberOfParameters() !== 1)
		{
			throw new exceptions\logic\invalidArgument('Reflection class injector must take one argument');
		}

		$this->reflectionClassInjector = $reflectionClassInjector;

		return $this;
	}

	public function getTestedClassName(atoum\test $test, $testsSubNamespace = '\tests\units\\')
	{
		$testsSubNamespace = '\\' . trim($testsSubNamespace, '\\') . '\\';

		$class = null;

		$testClass = $this->getReflectionClass($test)->getName();

		$position = strpos($testClass, $testsSubNamespace);

		if ($position !== false)
		{
			$class = substr($testClass, 0, $position) . '\\' . substr($testClass, $position + strlen($testsSubNamespace));
		}

		return $class;
	}

	public function addXdebugData(atoum\test $test, array $data)
	{
		$testedClassName = $this->getTestedClassName($test);
		$testedClassFile = $this->getReflectionClass($testedClassName)->getFileName();

		foreach ($data as $file => $lines)
		{
			if ($file === $testedClassFile)
			{
				foreach ($lines as $line => $number)
				{
					$this->lines[$testedClassFile][$line] = (isset($this->lines[$testedClassFile][$line]) === false ? $number : $this->lines[$testedClassFile][$line] + $number);
				}
			}
		}

		return $this;
	}

	public function merge(score\coverage $coverage)
	{
		foreach ($coverage->getLines() as $file => $lines)
		{
			foreach ($lines as $line => $number)
			{
				$this->lines[$file][$line] = (isset($this->lines[$file][$line]) === false ? $number : $this->lines[$file][$line] + $number);
			}
		}

		return $this;
	}
}

?>
