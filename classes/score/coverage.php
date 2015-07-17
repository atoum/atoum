<?php

namespace mageekguy\atoum\score;

use
	mageekguy\atoum,
	mageekguy\atoum\score,
	mageekguy\atoum\exceptions
;

class coverage implements \countable, \serializable
{
	protected $adapter = null;
	protected $reflectionClassFactory = null;
	protected $classes = array();
	protected $methods = array();
	protected $paths = array();
	protected $branches = array();
	protected $excludedMethods = array();
	protected $excludedClasses = array();
	protected $excludedNamespaces = array();
	protected $excludedDirectories = array();

	public function __construct(atoum\adapter $adapter = null, \closure $reflectionClassFactory = null)
	{
		$this
			->setAdapter($adapter)
			->setReflectionClassFactory($reflectionClassFactory)
		;
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

	public function setReflectionClassFactory(\closure $factory = null)
	{
		$this->reflectionClassFactory = $factory ?: function($class) { return new \reflectionClass($class); };

		return $this;
	}

	public function getReflectionClassFactory()
	{
		return $this->reflectionClassFactory;
	}

	public function serialize()
	{
		return serialize(array(
				$this->classes,
				$this->methods,
				$this->paths,
				$this->branches,
				$this->excludedClasses,
				$this->excludedNamespaces,
				$this->excludedDirectories
			)
		);
	}

	public function unserialize($string, \closure $reflectionClassFactory = null)
	{
		$this->setReflectionClassFactory($reflectionClassFactory);

		list(
			$this->classes,
			$this->methods,
			$this->paths,
			$this->branches,
			$this->excludedClasses,
			$this->excludedNamespaces,
			$this->excludedDirectories
		) = unserialize($string);

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

	public function getPaths()
	{
		return $this->paths;
	}

	public function getBranches()
	{
		return $this->branches;
	}

	public function reset()
	{
		$this->classes = array();
		$this->methods = array();
		$this->paths = array();
		$this->branches = array();

		return $this;
	}

	public function resetExcludedMethods()
	{
		$this->excludedMethods = array();

		return $this;
	}

	public function resetExcludedClasses()
	{
		$this->excludedClasses = array();

		return $this;
	}

	public function resetExcludedNamespaces()
	{
		$this->excludedNamespaces = array();

		return $this;
	}

	public function resetExcludedDirectories()
	{
		$this->excludedDirectories = array();

		return $this;
	}

	public function addXdebugDataForTest(atoum\test $test, array $data)
	{
		return $this->addXdebugDataForClass($test->getTestedClassName(), $data);
	}

	public function addXdebugDataForClass($class, array $data)
	{
		try
		{
			$reflectedClass = call_user_func($this->reflectionClassFactory, $class);

			if ($this->isExcluded($reflectedClass) === false)
			{
				$reflectedClassName = $reflectedClass->getName();

				if (isset($this->classes[$reflectedClassName]) === false)
				{
					$this->classes[$reflectedClassName] = $reflectedClass->getFileName();
					$this->methods[$reflectedClassName] = array();

					foreach ($reflectedClass->getMethods() as $method)
					{
						if ($method->isAbstract() === false && $this->isInExcludedMethods($method->getName()) === false)
						{
							$declaringClass = $this->getDeclaringClass($method);

							if ($this->isExcluded($declaringClass) === false)
							{
								$declaringClassName = $declaringClass->getName();
								$declaringClassFile = $declaringClass->getFilename();

								if (isset($data[$declaringClassFile]['functions'][$declaringClassName . '->' . $method->getName()]))
								{
									$this->paths[$declaringClassName][$method->getName()] = $data[$declaringClassFile]['functions'][$declaringClassName . '->' . $method->getName()]['paths'];
									$this->branches[$declaringClassName][$method->getName()] = $data[$declaringClassFile]['functions'][$declaringClassName . '->' . $method->getName()]['branches'];
								}

								if (isset($this->classes[$declaringClassName]) === false)
								{
									$this->classes[$declaringClassName] = $declaringClassFile;
									$this->methods[$declaringClassName] = array();
								}

								if (isset($data[$declaringClassFile]) === true)
								{
									for ($line = $method->getStartLine(), $endLine = $method->getEndLine(); $line <= $endLine; $line++)
									{
										if (isset($data[$declaringClassFile]['lines'][$line]) === true && (isset($this->methods[$declaringClassName][$method->getName()][$line]) === false || $this->methods[$declaringClassName][$method->getName()][$line] < $data[$declaringClassFile][$line]))
										{
											$this->methods[$declaringClassName][$method->getName()][$line] = $data[$declaringClassFile]['lines'][$line];
										}

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
		}
		catch (\exception $exception) {}

		return $this;
	}

	public function merge(score\coverage $coverage)
	{
		$paths = $coverage->getPaths();
		$branches = $coverage->getBranches();
		$classes = $coverage->getClasses();
		$methods = $coverage->getMethods();

		foreach ($methods as $class => $methods)
		{
			$reflectedClass = call_user_func($this->reflectionClassFactory, $class);

			if ($this->isExcluded($reflectedClass) === false)
			{
				if (isset($this->classes[$class]) === false)
				{
					$this->classes[$class] = $classes[$class];
				}

				if (isset($paths[$class]) && isset($this->paths[$class]) === false)
				{
					$this->paths[$class] = $paths[$class];
				}

				if (isset($branches[$class]) && isset($this->branches[$class]) === false)
				{
					$this->branches[$class] = $branches[$class];
				}

				foreach ($methods as $method => $lines)
				{
					if (isset($paths[$class]) === true)
					{
						if (isset($this->paths[$class][$method]) === false)
						{
							$this->paths[$class][$method] = $paths[$class][$method];
						}

						foreach ($paths[$class][$method] as $index => $path)
						{
							if ($this->paths[$class][$method][$index]['hit'] < $path['hit'])
							{
								$this->paths[$class][$method][$index]['hit'] = $path['hit'];
							}
						}
					}

					if (isset($branches[$class]) === true)
					{
						if (isset($this->branches[$class][$method]) === false)
						{
							$this->branches[$class][$method] = $branches[$class][$method];
						}

						foreach ($branches[$class][$method] as $index => $branch)
						{
							if ($this->branches[$class][$method][$index]['hit'] < $branch['hit'])
							{
								$this->branches[$class][$method][$index]['hit'] = $branch['hit'];
							}

							foreach ($branch['out'] as $outIndex => $outOp)
							{
								if (isset($this->branches[$class][$method][$index]['out'][$outIndex]) === false)
								{
									$this->branches[$class][$method][$index]['out'][$outIndex] = $outOp;
								}
							}

							foreach ($branch['out_hit'] as $outIndex => $hit)
							{
								if (isset($this->branches[$class][$method][$index]['out_hit'][$outIndex]) === false)
								{
									$this->branches[$class][$method][$index]['out_hit'][$outIndex] = $hit;
								}
								else
								{
									if ($this->branches[$class][$method][$index]['out_hit'][$outIndex] < $hit)
									{
										$this->branches[$class][$method][$index]['out_hit'][$outIndex] = $hit;
									}
								}
							}
						}
					}

					if (isset($this->methods[$class][$method]) === true || $this->isExcluded($this->getDeclaringClass($reflectedClass->getMethod($method))) === false)
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

	public function getPathsCoverageValue()
	{
		$value = null;

		if (sizeof($this->getPaths()) > 0)
		{
			$totalPaths = 0;
			$coveredPaths = 0;

			foreach ($this->paths as $methods)
			{
				foreach ($methods as $method)
				{
					foreach ($method as $path)
					{
						$totalPaths++;

						if ($path['hit'] === 1)
						{
							$coveredPaths++;
						}
					}
				}
			}

			if ($totalPaths > 0)
			{
				$value = (float) $coveredPaths / $totalPaths;
			}
		}

		return $value;
	}

	public function getBranchesCoverageValue()
	{
		$value = null;

		if (sizeof($this->getBranches()) > 0)
		{
			$totalBranches = 0;
			$coveredBranches = 0;

			foreach ($this->branches as $methods)
			{
				foreach ($methods as $method)
				{
					foreach ($method as $node)
					{
						foreach ($node['out'] as $index => $out)
						{
							$totalBranches++;

							if ($node['out_hit'][$index] === 1)
							{
								$coveredBranches++;
							}
						}
					}
				}
			}

			if ($totalBranches > 0)
			{
				$value = (float) $coveredBranches / $totalBranches;
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

	public function getPathsCoverageValueForClass($class)
	{
		$value = null;

		if (isset($this->paths[$class]) === true)
		{
			$totalPaths = 0;
			$coveredPaths = 0;

			foreach ($this->paths[$class] as $method)
			{
				foreach ($method as $path)
				{
					$totalPaths++;

					if ($path['hit'] === 1)
					{
						$coveredPaths++;
					}
				}
			}

			if ($totalPaths > 0)
			{
				$value = (float) $coveredPaths / $totalPaths;
			}
		}

		return $value;
	}

	public function getBranchesCoverageValueForClass($class)
	{
		$value = null;

		if (isset($this->branches[$class]) === true)
		{
			$totalPaths = 0;
			$coveredPaths = 0;

			foreach ($this->branches[$class] as $method)
			{
				foreach ($method as $path)
				{
					$totalPaths++;

					if ($path['hit'] === 1)
					{
						$coveredPaths++;
					}
				}
			}

			if ($totalPaths > 0)
			{
				$value = (float) $coveredPaths / $totalPaths;
			}
		}

		return $value;
	}

	public function getCoverageForClass($class)
	{
		$coverage = array();

		$class = (string) $class;

		if (isset($this->methods[$class]) === true && $this->isInExcludedClasses($class) === false)
		{
			$coverage = $this->methods[$class];
		}

		return $coverage;
	}

	public function getBranchesCoverageForClass($class)
	{
		$coverage = array();

		$class = (string) $class;

		if (isset($this->branches[$class]) === true && $this->isInExcludedClasses($class) === false)
		{
			$coverage = $this->branches[$class];
		}

		return $coverage;
	}

	public function getPathsCoverageForClass($class)
	{
		$coverage = array();

		$class = (string) $class;

		if (isset($this->paths[$class]) === true && $this->isInExcludedClasses($class) === false)
		{
			$coverage = $this->paths[$class];
		}

		return $coverage;
	}

	public function getNumberOfCoverableLinesInClass($class)
	{
		$coverableLines = 0;

		$class = (string) $class;

		if (isset($this->methods[$class]) === true && $this->isInExcludedClasses($class) === false)
		{
			foreach ($this->methods[$class] as $lines)
			{
				foreach ($lines as $call)
				{
					if ($call >= -1)
					{
						$coverableLines++;
					}
				}
			}
		}

		return $coverableLines;
	}

	public function getNumberOfCoveredLinesInClass($class)
	{
		$coveredLines = 0;

		$class = (string) $class;

		if (isset($this->methods[$class]) === true && $this->isInExcludedClasses($class) === false)
		{
			foreach ($this->methods[$class] as $lines)
			{
				foreach ($lines as $call)
				{
					if ($call === 1)
					{
						$coveredLines++;
					}
				}
			}
		}

		return $coveredLines;
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

	public function getPathsCoverageValueForMethod($class, $method)
	{
		$value = null;

		if (isset($this->paths[$class][$method]) === true)
		{
			$totalPaths = 0;
			$coveredPaths = 0;

			foreach ($this->paths[$class][$method] as $path)
			{
				$totalPaths++;

				if ($path['hit'] === 1)
				{
					$coveredPaths++;
				}
			}

			if ($totalPaths > 0)
			{
				$value = (float) $coveredPaths / $totalPaths;
			}
		}

		return $value;
	}

	public function getBranchesCoverageValueForMethod($class, $method)
	{
		$value = null;

		if (isset($this->branches[$class][$method]) === true)
		{
			$totalBranches = 0;
			$coveredBranches = 0;

			foreach ($this->branches[$class][$method] as $node)
			{
				foreach ($node['out'] as $index => $out)
				{
					$totalBranches++;

					if ($node['out_hit'][$index] === 1)
					{
						$coveredBranches++;
					}
				}
			}

			if ($totalBranches > 0)
			{
				$value = (float) $coveredBranches / $totalBranches;
			}
		}

		return $value;
	}


	public function getCoverageForMethod($class, $method)
	{
		$class = $this->getCoverageForClass($class);

		return (isset($class[$method]) === false ? array() : $class[$method]);
	}

	public function excludeMethod($method)
	{
		$method = (string) $method;

		if (in_array($method, $this->excludedMethods) === false)
		{
			$this->excludedMethods[] = $method;
		}

		return $this;
	}

	public function getExcludedMethods()
	{
		return $this->excludedMethods;
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

	public function isInExcludedMethods($method)
	{
		foreach ($this->excludedMethods as $pattern)
		{
			$matches = @preg_match($pattern, $method);

			if (false === $matches && $pattern === $method)
			{
				return true;
			}

			if ($matches > 0)
			{
				return true;
			}
		}

		return false;
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

	protected function getDeclaringClass(\reflectionMethod $method)
	{
		$declaringClass = $method->getDeclaringClass();

		$traits = ($this->adapter->method_exists($declaringClass, 'getTraits') === false ? array() : $declaringClass->getTraits());

		if (sizeof($traits) > 0)
		{
			$methodFileName = $method->getFileName();

			if ($methodFileName !== $declaringClass->getFileName() || $method->getStartLine() < $declaringClass->getStartLine() || $method->getEndLine() > $declaringClass->getEndLine())
			{
				if (sizeof($traits) > 0)
				{
					$methodName = $method->getName();

					foreach ($traits as $trait)
					{
						if ($methodFileName === $trait->getFileName() && $trait->hasMethod($methodName) === true)
						{
							return $trait;
						}
					}
				}
			}
		}

		return $declaringClass;
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
