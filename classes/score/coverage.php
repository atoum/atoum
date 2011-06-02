<?php

namespace mageekguy\atoum\score;

use
	\mageekguy\atoum,
	\mageekguy\atoum\score,
	\mageekguy\atoum\exceptions
;

class coverage implements \countable
{
	protected $classes = array();
	protected $lines = array();
	protected $methods = array();
	protected $reflectionClassInjector = null;

	public function __construct() {}

	public function reset()
	{
		$this->classes = $this->methods = array();

		return $this;
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

	public function getClasses()
	{
		return $this->classes;
	}

	public function getMethods()
	{
		return $this->methods;
	}

	public function addXdebugDataForTest(atoum\test $test, array $data)
	{
		return $this->addXdebugDataForClass($test->getTestedClassName(), $data);
	}

	public function addXdebugDataForClass($class, array $data)
	{
		if (sizeof($data) > 0)
		{
			try
			{
				$reflectedClass = $this->getReflectionClass($class);

				$reflectedClassName = $reflectedClass->getName();
				$reflectedClassFile = $reflectedClass->getFileName();

				$this->classes[$reflectedClassName] = $reflectedClassFile;
				$this->methods[$reflectedClassName] = array();

				foreach ($reflectedClass->getMethods() as $method)
				{
					if ($method->isAbstract() === false)
					{
						$declaringClass = $method->getDeclaringClass();

						$declaringClassName = $declaringClass->getName();
						$declaringClassFile = $declaringClass->getFilename();

						if ($declaringClassFile !== false)
						{
							if (isset($this->classes[$declaringClassName]) === false)
							{
								$this->classes[$declaringClassName] = $declaringClassFile;
								$this->methods[$declaringClassName] = array();
							}

 							if (isset($data[$declaringClassFile]) === true)
							{
								for ($line = $method->getStartLine(), $endLine = $method->getEndLine(); $line <= $endLine; $line++)
								{
									if (isset($data[$declaringClassFile][$line]) === true && (isset($this->methods[$declaringClassName][$method->getName()][$line]) === false || $this->methods[$declaringClassName][$method->getName()][$line] < $data[$declaringClassFile][$line]))
									{
										$this->methods[$declaringClassName][$method->getName()][$line] = $data[$declaringClassFile][$line];
									}
								}
							}
						}
					}
				}

			}
			catch (\exception $exception) {}
		}

		return $this;
	}

	public function merge(score\coverage $coverage)
	{
		foreach ($coverage->methods as $class => $methods)
		{
			if (isset($this->classes[$class]) === false)
			{
				$this->classes[$class] = $coverage->classes[$class];
			}

			foreach ($methods as $method => $lines)
			{
				foreach ($lines as $line => $call)
				{
					if (isset($this->methods[$class][$method][$line]) === false || $this->methods[$class][$method][$line] < $call)
					{
						$this->methods[$class][$method][$line] = $call;
					}
				}
			}
		}

		return $this;
	}

	public function getValue()
	{
		$value = null;

		if (sizeof($this) > 0)
		{
			$totalLines = 0;
			$coveredLines = 0;

			foreach ($this->methods as $methods)
			{
				foreach ($methods as $lines)
				{
					foreach ($lines as $call)
					{
						if ($call >= -1)
						{
							$totalLines++;
						}

						if ($call === 1)
						{
							$coveredLines++;
						}
					}
				}
			}

			if ($totalLines > 0)
			{
				$value = (float) $coveredLines / $totalLines;
			}
		}

		return $value;
	}

	public function getValueForClass($class)
	{
		$value = null;

		if (isset($this->methods[$class]) === true)
		{
			$totalLines = 0;
			$coveredLines = 0;

			foreach ($this->methods[$class] as $lines)
			{
				foreach ($lines as $call)
				{
					if ($call >= -1)
					{
						$totalLines++;
					}

					if ($call === 1)
					{
						$coveredLines++;
					}
				}
			}

			if ($totalLines > 0)
			{
				$value = (float) $coveredLines / $totalLines;
			}
		}

		return $value;
	}

	public function getValueForMethod($class, $method)
	{
		$value = null;

		if (isset($this->methods[$class][$method]) === true)
		{
			$totalLines = 0;
			$coveredLines = 0;

			foreach ($this->methods[$class][$method] as $call)
			{
				if ($call >= -1)
				{
					$totalLines++;
				}

				if ($call === 1)
				{
					$coveredLines++;
				}
			}

			if ($totalLines > 0)
			{
				$value = (float) $coveredLines / $totalLines;
			}
		}

		return $value;
	}

	public function count()
	{
		return sizeof($this->methods);
	}
}

?>
