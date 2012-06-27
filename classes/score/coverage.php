<?php

namespace mageekguy\atoum\score;

use
	mageekguy\atoum,
	mageekguy\atoum\score,
	mageekguy\atoum\exceptions
;

class coverage implements \countable
{
	protected $factory = null;
	protected $classes = array();
	protected $methods = array();
	protected $excludedClasses = array();
	protected $excludedNamespaces = array();
	protected $excludedDirectories = array();

	public function __construct(atoum\factory $factory = null)
	{
		$this->setFactory($factory ?: new atoum\factory());
	}

	public function setFactory(atoum\factory $factory)
	{
		$this->factory = $factory;

		return $this;
	}

	public function getFactory()
	{
		return $this->factory;
	}

	public function reset()
	{
		$this->classes = array();
		$this->methods = array();
		$this->excludedClasses = array();
		$this->excludedNamespaces = array();
		$this->excludedDirectories = array();

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
				$reflectedClass = $this->factory['reflectionClass']($class);

				if ($this->isExcluded($reflectedClass) === false)
				{
					$reflectedClassName = $reflectedClass->getName();

					$this->classes[$reflectedClassName] = $reflectedClass->getFileName();
					$this->methods[$reflectedClassName] = array();

					foreach ($reflectedClass->getMethods() as $method)
					{
						if ($method->isAbstract() === false)
						{
							$declaringClass = $method->getDeclaringClass();

							if ($this->isExcluded($declaringClass) === false)
							{
								$declaringClassName = $declaringClass->getName();
								$declaringClassFile = $declaringClass->getFilename();

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

	public function excludeClass($class)
	{
		$class = (string) $class;

		if (in_array($class, $this->excludedClasses) === false)
		{
			$this->excludedClasses[] = $class;
		}

		return $this;
	}

	public function getExcludedClasses()
	{
		return $this->excludedClasses;
	}

	public function excludeNamespace($namespace)
	{
		$namespace = trim((string) $namespace, '\\');

		if (in_array($namespace, $this->excludedNamespaces) === false)
		{
			$this->excludedNamespaces[] = $namespace;
		}

		return $this;
	}

	public function getExcludedNamespaces()
	{
		return $this->excludedNamespaces;
	}

	public function excludeDirectory($directory)
	{
		$directory = rtrim((string) $directory, DIRECTORY_SEPARATOR);

		if (in_array($directory, $this->excludedDirectories) === false)
		{
			$this->excludedDirectories[] = $directory;
		}

		return $this;
	}

	public function getExcludedDirectories()
	{
		return $this->excludedDirectories;
	}

	public function count()
	{
		return sizeof($this->methods);
	}

	public function isInExcludedClasses($class)
	{
		return (in_array($class, $this->excludedClasses) === true);
	}

	public function isInExcludedNamespaces($class)
	{
		return self::itemIsExcluded($this->excludedNamespaces, $class, '\\');
	}

	public function isInExcludedDirectories($file)
	{
		return self::itemIsExcluded($this->excludedDirectories, $file, DIRECTORY_SEPARATOR);
	}

	protected function isExcluded(\reflectionClass $class)
	{
		$className = $class->getName();

		if ($this->isInExcludedClasses($className) === true || $this->isInExcludedNamespaces($className) === true)
		{
			return true;
		}
		else
		{
			$fileName = $class->getFileName();

			return ($fileName === false || $this->isInExcludedDirectories($fileName) === true);
		}
	}

	protected static function itemIsExcluded(array $excludedItems, $item, $delimiter)
	{
		foreach ($excludedItems as $excludedItem)
		{
			$excludedItem .= $delimiter;

			if (substr($item, 0, strlen($excludedItem)) === $excludedItem)
			{
				return true;
			}
		}

		return false;
	}
}
