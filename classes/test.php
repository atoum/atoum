<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum\test,
	mageekguy\atoum\mock,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

/**
 * @property    \mageekguy\atoum\asserter           assert
 *
 * @method      \mageekguy\atoum\asserter\generator assert()
 */
abstract class test implements observable, adapter\aggregator, \countable
{
	const testMethodPrefix = 'test';
	const defaultNamespace = 'tests\units';

	const runStart = 'testRunStart';
	const beforeSetUp = 'beforeTestSetUp';
	const afterSetUp = 'afterTestSetUp';
	const beforeTestMethod = 'beforeTestMethod';
	const fail = 'testAssertionFail';
	const error = 'testError';
	const uncompleted = 'testUncompleted';
	const exception = 'testException';
	const runtimeException = 'testRuntimeException';
	const success = 'testAssertionSuccess';
	const afterTestMethod = 'afterTestMethod';
	const beforeTearDown = 'beforeTestTearDown';
	const afterTearDown = 'afterTestTearDown';
	const runStop = 'testRunStop';

    /**
     * Path to php binary
     *
     * @var string
     */
	private $phpPath = null;

    /**
     * Current test file path
     *
     * @var string
     */
	private $path = '';

    /**
     * Current test class name
     *
     * @var string
     */
	private $class = '';

    /**
     * @var string
     */
	private $testedClass = null;

    /**
     * @var string
     */
	private $classNamespace = '';

    /**
     * @var \mageekguy\atoum\adapter
     */
	private $adapter = null;

    /**
     * @var \mageekguy\atoum\asserter\generator
     */
	private $asserterGenerator = null;

    /**
     * @var \mageekguy\atoum\score
     */
	private $score = null;

    /**
     * @var array
     */
	private $observers = array();

    /**
     * @var array
     */
	private $tags = array();

    /**
     * @var boolean
     */
	private $ignore = false;

    /**
     * @var array
     */
	private $dataProviders = array();

    /**
     * @var array
     */
	private $testMethods = array();

    /**
     * @var array
     */
	private $runTestMethods = array();

    /**
     * @var string
     */
	private $currentMethod = null;

    /**
     * @var string
     */
	private $testNamespace = null;

    /**
     * @var \mageekguy\atoum\mock\generator
     */
	private $mockGenerator = null;

    /**
     * @var integer
     */
	private $size = 0;

    /**
     * @var string
     */
	private $phpCode = '';

    /**
     * @var array
     */
	private $children = array();

    /**
     * @var integer
     */
	private $maxChildrenNumber = null;

    /**
     * Whether or not to generate code coverage
     *
     * @var boolean
     */
	private $codeCoverage = false;

    /**
     * @var \mageekguy\atoum\includer
     */
	private $includer = null;

    /**
     * @var string
     */
	private $bootstrapFile = null;

    /**
     * @var \mageekguy\atoum\superglobals
     */
    public $superglobals = null;

    /**
     * @var \mageekguy\atoum\locale
     */
    public $locale = null;

    /**
     * Current test namespace
     *
     * @var string
     */
	private static $namespace = null;

    /**
     * Constructor
     *
     * @param \mageekguy\atoum\score        $score
     * @param \mageekguy\atoum\locale       $locale
     * @param \mageekguy\atoum\adapter      $adapter
     * @param \mageekguy\atoum\superglobals $superglobals
     * @param \mageekguy\atoum\includer     $includer
     *
     * @throws \mageekguy\atoum\exceptions\runtime
     */
	public function __construct(score $score = null, locale $locale = null, adapter $adapter = null, superglobals $superglobals = null, includer $includer = null)
	{
		$this
			->setScore($score ?: new score())
			->setLocale($locale ?: new locale())
			->setAdapter($adapter ?: new adapter())
			->setSuperglobals($superglobals ?: new superglobals())
			->setIncluder($includer ?: new includer())
			->enableCodeCoverage()
		;

		$class = new \reflectionClass($this);

		$this->path = $class->getFilename();
		$this->class = $class->getName();
		$this->classNamespace = $class->getNamespaceName();

		$annotationExtractor = new test\annotations\extractor();

		$annotationExtractor->setTest($this, $class->getDocComment());

		foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $publicMethod)
		{
			if (strpos($methodName = $publicMethod->getName(), self::testMethodPrefix) === 0)
			{
				$this->testMethods[$methodName] = array();

				$annotationExtractor->setTestMethod($this, $methodName, $publicMethod->getDocComment());
			}
		}

		$this->runTestMethods($this->getTestMethods());

		$this->getAsserterGenerator()
			->setAlias('array', 'phpArray')
			->setAlias('class', 'phpClass')
		;
	}


    /**
     * @return string
     */
	public function __toString()
	{
		return $this->getClass();
	}


    /**
     * Magic getter
     *
     * @param string $property
     *
     * @return \mageekguy\atoum\asserter\generator|\mageekguy\atoum\mock\generator
     *
     * @throws \mageekguy\atoum\exceptions\logic\invalidArgument
     */
	public function __get($property)
	{
		switch ($property)
		{
			case 'define':
				return $this->getAsserterGenerator();

			case 'assert':
				return $this->stopCase()->getAsserterGenerator();

			case 'mockGenerator':
				return $this->getMockGenerator();

			default:
				throw new exceptions\logic\invalidArgument('Property \'' . $property . '\' is undefined in class \'' . get_class($this) . '\'');
		}
	}


    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return \mageekguy\atoum\test|\mageekguy\atoum\asserter\generator
     *
     * @throws \mageekguy\atoum\exceptions\logic\invalidArgument
     */
	public function __call($method, $arguments)
	{
		switch ($method)
		{
			case 'assert':
				$this->stopCase();

				$case = isset($arguments[0]) === false ? null : $arguments[0];

				if ($case !== null)
				{
					$this->startCase($case);
				}

				return $this->getAsserterGenerator();

			default:
				throw new exceptions\logic\invalidArgument('Method ' . get_class($this) . '::' . $method . '() is undefined');
		}
	}


    /**
     * @return boolean
     */
	public function codeCoverageIsEnabled()
	{
		return $this->codeCoverage;
	}


    /**
     * @return \mageekguy\atoum\test
     */
	public function enableCodeCoverage()
	{
		$this->codeCoverage = $this->adapter->extension_loaded('xdebug');

		return $this;
	}


    /**
     * @return \mageekguy\atoum\test
     */
	public function disableCodeCoverage()
	{
		$this->codeCoverage = false;

		return $this;
	}


    /**
     * @param integer $number
     *
     * @return \mageekguy\atoum\test
     *
     * @throws \mageekguy\atoum\exceptions\logic\invalidArgument
     */
	public function setMaxChildrenNumber($number)
	{
		if ($number < 1)
		{
			throw new exceptions\logic\invalidArgument('Maximum number of children must be greater or equal to 1');
		}

		$this->maxChildrenNumber = $number;

		return $this;
	}

    /**
     * @param \mageekguy\atoum\superglobals $superglobals
     *
     * @return \mageekguy\atoum\test
     */
	public function setSuperglobals(superglobals $superglobals)
	{
		$this->superglobals = $superglobals;

		return $this;
	}


    /**
     * @return \mageekguy\atoum\superglobals
     */
	public function getSuperglobals()
	{
		return $this->superglobals;
	}


	public function setIncluder(includer $includer)
	{
		$this->includer = $includer;

		return $this;
	}

	public function getIncluder()
	{
		return $this->includer;
	}

	public function setBootstrapFile($path)
	{
		$this->bootstrapFile = $path;

		return $this;
	}

	public function getBootstrapFile()
	{
		return $this->bootstrapFile;
	}


    /**
     * @param \mageekguy\atoum\mock\generator $generator
     *
     * @return \mageekguy\atoum\test
     */
	public function setMockGenerator(mock\generator $generator)
	{
		$this->mockGenerator = $generator;

		return $this;
	}


    /**
     * @return \mageekguy\atoum\mock\generator
     */
	public function getMockGenerator()
	{
		return $this->mockGenerator ?: $this->setMockGenerator(new mock\generator())->mockGenerator;
	}


    /**
     * @param \mageekguy\atoum\asserter\generator $generator
     *
     * @return \mageekguy\atoum\test
     */
	public function setAsserterGenerator(asserter\generator $generator)
	{
		$this->asserterGenerator = $generator->setTest($this);

		return $this;
	}


    /**
     * @return \mageekguy\atoum\asserter\generator
     */
	public function getAsserterGenerator()
	{
		test\adapter::resetCallsForAllInstances();

		return $this->asserterGenerator ?: $this->setAsserterGenerator(new asserter\generator($this))->asserterGenerator;
	}


    /**
     * @param string $testNamespace
     *
     * @return \mageekguy\atoum\test
     *
     * @throws \mageekguy\atoum\exceptions\logic\invalidArgument
     */
	public function setTestNamespace($testNamespace)
	{
		$this->testNamespace = self::cleanNamespace($testNamespace);

		if ($this->testNamespace === '')
		{
			throw new exceptions\logic\invalidArgument('Test namespace must not be empty');
		}

		return $this;
	}


    /**
     * @return string
     */
	public function getTestNamespace()
	{
		return $this->testNamespace ?: self::getNamespace();
	}


    /**
     * @param string$path
     *
     * @return \mageekguy\atoum\test
     */
	public function setPhpPath($path)
	{
		$this->phpPath = (string) $path;

		return $this;
	}


    /**
     * @return string
     *
     * @throws \mageekguy\atoum\exceptions\runtime
     */
	public function getPhpPath()
	{
		return $this->phpPath;
	}


    /**
     * @return array
     */
	public function getAllTags()
	{
		$tags = $this->getTags();

		foreach ($this->testMethods as $annotations)
		{
			if (isset($annotations['tags']) === true)
			{
				$tags = array_merge($tags, array_diff($annotations['tags'], $tags));
			}
		}

		return array_values($tags);
	}


    /**
     * @param array $tags
     *
     * @return \mageekguy\atoum\test
     */
	public function setTags(array $tags)
	{
		$this->tags = $tags;

		return $this;
	}


    /**
     * @return array
     */
	public function getTags()
	{
		return $this->tags;
	}


    /**
     * @param string$testMethodName
     * @param array $tags
     *
     * @return \mageekguy\atoum\test
     *
     * @throws \mageekguy\atoum\exceptions\logic\invalidArgument
     */
    public function setMethodTags($testMethodName, array $tags)
	{
		if (isset($this->testMethods[$testMethodName]) === false)
		{
			throw new exceptions\logic\invalidArgument('Test method ' . $this->class . '::' . $testMethodName . '() is unknown');
		}

		$this->testMethods[$testMethodName]['tags'] = $tags;

		return $this;
	}


    /**
     * @param string $testMethodName
     *
     * @return array
     *
     * @throws \mageekguy\atoum\exceptions\logic\invalidargument
     */
	public function getMethodTags($testMethodName = null)
	{
		$tags = array();

		$classTags = $this->getTags();

		if ($testMethodName === null)
		{
			foreach ($this->testMethods as $testMethodName => $annotations)
			{
				$tags[$testMethodName] = isset($annotations['tags']) === false ? $classTags : $annotations['tags'];
			}
		}
		else
		{
			if (isset($this->testMethods[$testMethodName]) === false)
			{
				throw new exceptions\logic\invalidArgument('Test method ' . $this->class . '::' . $testMethodName . '() is unknown');
			}

			$tags = isset($this->testMethods[$testMethodName]['tags']) === false ? $classTags : $this->testMethods[$testMethodName]['tags'];
		}

		return $tags;
	}


    /**
     * @return array
     */
	public function getDataProviders()
	{
		return $this->dataProviders;
	}


    /**
     * @param \mageekguy\atoum\adapter $adapter
     *
     * @return \mageekguy\atoum\test
     */
	public function setAdapter(adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}


    /**
     * @return \mageekguy\atoum\adapter
     */
	public function getAdapter()
	{
		return $this->adapter;
	}


    /**
     * @param \mageekguy\atoum\score $score
     *
     * @return \mageekguy\atoum\test
     */
	public function setScore(score $score)
	{
		$this->score = $score;

		return $this;
	}


    /**
     * @return \mageekguy\atoum\score
     */
	public function getScore()
	{
		return $this->score;
	}


    /**
     * @param \mageekguy\atoum\locale $locale
     *
     * @return \mageekguy\atoum\test
     */
	public function setLocale(locale $locale)
	{
		$this->locale = $locale;

		return $this;
	}


    /**
     * @return \mageekguy\atoum\locale
     */
	public function getLocale()
	{
		return $this->locale;
	}


    /**
     * @return string
     */
	public function getTestedClassName()
	{
		if ($this->testedClass === null)
		{
			$class = null;

			$testClass = $this->getClass();
			$testNamespace = $this->getTestNamespace();

			$position = strpos($testClass, $testNamespace);

			if ($position === false)
			{
				throw new exceptions\runtime('Test class \'' . $this->getClass() . '\' is not in a namespace which contains \'' . $this->getTestNamespace() . '\'');
			}
			else
			{
				$this->setTestedClassName(trim(substr($testClass, 0, $position) . substr($testClass, $position + strlen($testNamespace) + 1), '\\'));
			}
		}

		return $this->testedClass;
	}

	public function setTestedClassName($className)
	{
		if ($this->testedClass !== null)
		{
			throw new exceptions\runtime('Tested class name is already defined');
		}

		$this->testedClass = $className;

		return $this;
	}


    /**
     * @return string
     */
	public function getClass()
	{
		return $this->class;
	}

	public function getClassNamespace()
	{
		return $this->classNamespace;
	}

    /**
     * @return string
     */
	public function getPath()
	{
		return $this->path;
	}

	public function getTaggedTestMethods(array $methods, array $tags = array())
	{
		return array_values(array_uintersect($methods, $this->getTestMethods($tags), 'strcasecmp'));
	}

    /**
     * @param array $tags
     *
     * @return array
     */
	public function getTestMethods(array $tags = array())
	{
		$testMethods = array();

		foreach (array_keys($this->testMethods) as $methodName)
		{
			if ($this->methodIsIgnored($methodName, $tags) === false)
			{
				$testMethods[] = $methodName;
			}
		}

		return $testMethods;
	}


    /**
     * @return string
     */
	public function getCurrentMethod()
	{
		return $this->currentMethod;
	}


    /**
     * @return integer
     */
    public function getMaxChildrenNumber()
	{
		return $this->maxChildrenNumber;
	}


    /**
     * @return integer
     */
	public function count()
	{
		return $this->size;
	}


    /**
     * @param \mageekguy\atoum\observer $observer
     *
     * @return \mageekguy\atoum\test
     */
	public function addObserver(observer $observer)
	{
		$this->observers[] = $observer;

		return $this;
	}


    /**
     * @param string $event
     *
     * @return \mageekguy\atoum\test
     */
	public function callObservers($event)
	{
		foreach ($this->observers as $observer)
		{
			$observer->handleEvent($event, $this);
		}

		return $this;
	}


    /**
     * @param boolean $boolean
     *
     * @return \mageekguy\atoum\test
     */
	public function ignore($boolean)
	{
		$this->ignore = ($boolean == true);

		return $this->runTestMethods($this->getTestMethods());
	}


    /**
     * @return boolean
     */
	public function isIgnored()
	{
		return ($this->ignore === true);
	}


	public function ignoreMethod($methodName, $boolean)
	{
		$this->checkMethod($methodName)->testMethods[$methodName]['ignore'] = $boolean == true;

		return $this->runTestMethods($this->getTestMethods());
	}

    /**
     * @param string $methodName
     * @param array  $tags
     *
     * @return boolean
     *
     * @throws \mageekguy\atoum\exceptions\logic\invalidArgument
     */
	public function methodIsIgnored($methodName, array $tags = array())
	{
		$isIgnored = $this->checkMethod($methodName)->ignore;

		if ($isIgnored === false)
		{
			if (isset($this->testMethods[$methodName]['ignore']) === true)
			{
				$isIgnored = $this->testMethods[$methodName]['ignore'];
			}

			if ($isIgnored === false && $tags)
			{
				$isIgnored = sizeof($methodTags = $this->getMethodTags($methodName)) <= 0 || sizeof(array_intersect($tags, $methodTags)) <= 0;
			}
		}

		return $isIgnored;
	}


    /**
     * @param string $testMethod
     * @param array  $tags
     *
     * @return \mageekguy\atoum\test
     *
     * @throws \mageekguy\atoum\exception
     */
	public function runTestMethod($testMethod, array $tags = array())
	{
		if ($this->methodIsIgnored($testMethod, $tags) === false)
		{
			set_error_handler(array($this, 'errorHandler'));

			ini_set('display_errors', 'stderr');
			ini_set('log_errors', 'Off');
			ini_set('log_errors_max_len', '0');

			$this->currentMethod = $testMethod;

			try
			{
				try
				{
					ob_start();

					if ($this->bootstrapFile != null)
					{
						$this->includer->includePath($this->bootstrapFile);
					}

					if ($this->adapter->class_exists($testedClassName = $this->getTestedClassName()) === false)
					{
						throw new exceptions\runtime('Tested class \'' . $testedClassName . '\' does not exist for test class \'' . $this->getClass() . '\'');
					}

					$this->beforeTestMethod($this->currentMethod);

					if ($this->codeCoverageIsEnabled() === true)
					{
						xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
					}

					$time = microtime(true);
					$memory = memory_get_usage(true);

					if (isset($this->dataProviders[$testMethod]) === false)
					{
						$this->{$testMethod}();
					}
					else
					{
						$data = $this->{$this->dataProviders[$testMethod]}();

						if (is_array($data) === false && $data instanceof \traversable === false)
						{
							throw new test\exceptions\runtime('Data provider ' . $this->getClass() . '::' . $this->dataProviders[$testMethod] . '() must return an array or an iterator');
						}

						$reflectedTestMethod = new \reflectionMethod($this, $testMethod);
						$numberOfArguments = $reflectedTestMethod->getNumberOfRequiredParameters();

						foreach ($data as $key => $arguments)
						{
							if (is_array($arguments) === false)
							{
								$arguments = array($arguments);
							}

							if (sizeof($arguments) != $numberOfArguments)
							{
								throw new test\exceptions\runtime('Data provider ' . $this->getClass() . '::' . $this->dataProviders[$testMethod] . '() not provide enough arguments at key ' . $key . ' for test method ' . $this->getClass() . '::' . $testMethod . '()');
							}

							$this->score->setDataSet($key, $this->dataProviders[$testMethod]);

							$reflectedTestMethod->invokeArgs($this, $arguments);

							$this->score->unsetDataSet();
						}
					}

					$memoryUsage = memory_get_usage(true) - $memory;
					$duration = microtime(true) - $time;

					if ($this->codeCoverageIsEnabled() === true)
					{
						$this->score->getCoverage()->addXdebugDataForTest($this, xdebug_get_code_coverage());
						xdebug_stop_code_coverage();
					}

					$this->afterTestMethod($this->currentMethod);

					$this->score
						->addMemoryUsage($this->class, $this->currentMethod, $memoryUsage)
						->addDuration($this->class, $this->currentMethod, $duration)
						->addOutput($this->class, $this->currentMethod, ob_get_clean())
					;
				}
				catch (\exception $exception)
				{
					$this->score->addOutput($this->class, $this->currentMethod, ob_get_clean());

					throw $exception;
				}
			}
			catch (asserter\exception $exception)
			{
				if ($this->score->failExists($exception) === false)
				{
					$this->addExceptionToScore($exception);
				}
			}
			catch (test\exceptions\runtime $exception)
			{
				$this->score->addRuntimeException($exception);
			}
			catch (\exception $exception)
			{
				$this->addExceptionToScore($exception);
			}

			$this->currentMethod = null;

			restore_error_handler();

			ini_restore('display_errors');
			ini_restore('log_errors');
			ini_restore('log_errors_max_len');
		}

		return $this;
	}


    /**
     * @param array $runTestMethods
     * @param array $tags
     *
     * @return \mageekguy\atoum\test
     *
     * @throws \mageekguy\atoum\exceptions\logic\invalidArgument
     * @throws \mageekguy\atoum\exception
     * @throws \mageekguy\atoum\exceptions\runtime
     */
	public function run(array $runTestMethods = array(), array $tags = array())
	{
		if ($this->isIgnored() === false)
		{
			if ($runTestMethods)
			{
				$this->runTestMethods(array_intersect($runTestMethods, $this->getTestMethods($tags)));
			}

			$this->callObservers(self::runStart);

			if (sizeof($this))
			{
				$this->phpCode =
					'<?php ' .
					'define(\'mageekguy\atoum\autorun\', false);' .
					'require_once \'' . directory . '/scripts/runner.php\';' .
					'require \'' . $this->path . '\';' .
					'$test = new ' . $this->class . '();' .
					'$test->setLocale(new ' . get_class($this->locale) . '(' . $this->locale->get() . '));' .
					'$test->setPhpPath(\'' . $this->getPhpPath() . '\');'
				;

				if ($this->bootstrapFile !== null)
				{
					$this->phpCode .= '$test->setBootstrapFile(\'' . $this->getBootstrapFile() . '\');';
				}

				$this->phpCode .=
					($this->codeCoverageIsEnabled() === true ? '' : '$test->disableCodeCoverage();') .
					'$test->runTestMethod(\'%s\');' .
					'echo serialize($test->getScore());' .
					'?>'
				;

				$null = null;

				try
				{
					$this->callObservers(self::beforeSetUp);
					$this->setUp();
					$this->callObservers(self::afterSetUp);

					while ($this->runChild()->children)
					{
						$pipes = array();

						foreach ($this->children as $child)
						{
							if (isset($child[1][1]) === true)
							{
								$pipes[] = $child[1][1];
							}

							if (isset($child[1][2]) === true)
							{
								$pipes[] = $child[1][2];
							}
						}

						$pipesUpdated = stream_select($pipes, $null, $null, $this->canRunChild() === true ? 0 : null);

						if ($pipesUpdated)
						{
							$children = $this->children;
							$this->children = array();

							foreach ($children as $this->currentMethod => $child)
							{
								if (isset($child[1][2]) && in_array($child[1][2], $pipes) === true)
								{
									$child[3] .= stream_get_contents($child[1][2]);

									if (feof($child[1][2]) === true)
									{
										fclose($child[1][2]);
										unset($child[1][2]);
									}
								}

								if (isset($child[1][1]) && in_array($child[1][1], $pipes) === true)
								{
									$child[2] .= stream_get_contents($child[1][1]);

									if (feof($child[1][1]) === true)
									{
										fclose($child[1][1]);
										unset($child[1][1]);
									}
								}

								if (isset($child[1][1]) === true || isset($child[1][2]) === true)
								{
									$this->children[$this->currentMethod] = $child;
								}
								else
								{
									$phpStatus = proc_get_status($child[0]);

									while ($phpStatus['running'] == true)
									{
										$phpStatus = proc_get_status($child[0]);
									}

									proc_close($child[0]);

									$score = new score();

									$testScore = @unserialize($child[2]);

									if ($testScore instanceof score)
									{
										$score = $testScore;
									}
									else
									{
										$score->addUncompletedTest($this->class, $this->currentMethod, $phpStatus['exitcode'], $child[2]);
									}

									if ($child[3] !== '')
									{
										if (preg_match_all('/([^:]+): (.+) in (.+) on line ([0-9]+)/', trim($child[3]), $errors, PREG_SET_ORDER) === 0)
										{
											$score->addError($this->path, null, $this->class, $this->currentMethod, 'UNKNOWN', $child[3]);
										}
										else foreach ($errors as $error)
										{
											$score->addError($this->path, null, $this->class, $this->currentMethod, $error[1], $error[2], $error[3], $error[4]);
										}
									}

									$this->callObservers(self::afterTestMethod);

									switch (true)
									{
										case $score->getRuntimeExceptionNumber() > 0:
											$this->callObservers(self::runtimeException);
											throw current($score->getRuntimeExceptions());

										case $score->getUncompletedTestNumber():
											$this->callObservers(self::uncompleted);
											break;

										case $score->getFailNumber():
											$this->callObservers(self::fail);
											break;

										case $score->getErrorNumber():
											$this->callObservers(self::error);
											break;

										case $score->getExceptionNumber():
											$this->callObservers(self::exception);
											break;

										case $score->getPassNumber():
											$this->callObservers(self::success);
											break;
									}

									$this->score->merge($score);
								}
							}

							$this->currentMethod = null;
						}
					}

					$this->callObservers(self::beforeTearDown);
					$this->tearDown();
					$this->callObservers(self::afterTearDown);
				}
				catch (\exception $exception)
				{
					$this
						->callObservers(self::beforeTearDown)
						->tearDown()
						->callObservers(self::afterTearDown)
					;

					throw $exception;
				}
			}

			$this->callObservers(self::runStop);
		}

		return $this;
	}


    /**
     * @param integer $errno
     * @param string  $errstr
     * @param string  $errfile
     * @param integer $errline
     * @param array   $context
     *
     * @return boolean
     */
	public function errorHandler($errno, $errstr, $errfile, $errline, $context)
	{
		if (error_reporting() !== 0)
		{
			list($file, $line) = $this->getBacktrace();

			$this->score->addError($file, $line, $this->class, $this->currentMethod, $errno, $errstr, $errfile, $errline);
		}

		return true;
	}


    /**
     * Generate a mock
     *
     * @param string $class
     * @param string $mockNamespace
     * @param string $mockClass
     *
     * @return \mageekguy\atoum\test
     */
	public function mock($class, $mockNamespace = null, $mockClass = null)
	{
		$this->getMockGenerator()->generate($class, $mockNamespace, $mockClass);

		return $this;
	}


    /**
     * @param string $mockNamespace
     * @param string $mockClass
     *
     * @return \mageekguy\atoum\test
     */
	public function mockTestedClass($mockNamespace = null, $mockClass = null)
	{
		$this->getMockGenerator()->generate($this->getTestedClassName(), $mockNamespace, $mockClass);

		return $this;
	}


    /**
     * @deprecated
     *
     * @param string
     */
	public function setTestsSubNamespace($testsSubNamespace)
	{
		#DEPRECATED
		die(__METHOD__ . ' is deprecated, please use ' . __CLASS__ . '::setTestNamespace() instead');
	}


    /**
     * @deprecated
     */
	public function getTestsSubNamespace()
	{
		#DEPRECATED
		die(__METHOD__ . ' is deprecated, please use ' . __CLASS__ . '::getTestNamespace() instead');
	}


    /**
     * @return string
     */
	public function getOutput()
	{
		return ob_get_clean() ?: '';
	}


    /**
     * @param string $namespace
     *
     * @throws exceptions\logic\invalidArgument
     */
	public static function setNamespace($namespace)
	{
		self::$namespace = self::cleanNamespace($namespace);

		if (self::$namespace === '')
		{
			throw new exceptions\logic\invalidArgument('Namespace must not be empty');
		}
	}


    /**
     * @return string
     */
	public static function getNamespace()
	{
		return self::$namespace ?: self::defaultNamespace;
	}


    /**
     * @param string $case
     *
     * @return \mageekguy\atoum\test
     */
	public function startCase($case)
	{
		test\adapter::resetCallsForAllInstances();

		$this->score->setCase($case);

		return $this;
	}


    /**
     * @return \mageekguy\atoum\test
     */
	public function stopCase()
	{
		test\adapter::resetCallsForAllInstances();

		$this->score->unsetCase();

		return $this;
	}


    /**
     * @param string $testMethodName
     * @param string $dataProvider
     *
     * @return \mageekguy\atoum\test
     *
     * @throws exceptions\logic\invalidArgument
     */
	public function setDataProvider($testMethodName, $dataProvider)
	{
		if (isset($this->testMethods[$testMethodName]) === false)
		{
			throw new exceptions\logic\invalidArgument('Test method ' . $this->class . '::' . $testMethodName . '() is unknown');
		}

		if (method_exists($this, $dataProvider) === false)
		{
			throw new exceptions\logic\invalidArgument('Data provider ' . $this->class . '::' . $dataProvider . '() is unknown');
		}

		$this->dataProviders[$testMethodName] = $dataProvider;

		return $this;
	}


    /**
     * @return \mageekguy\atoum\test
     */
	protected function setUp()
	{
		return $this;
	}


    /**
     * @param string $testMethod current test method
     *
     * @return \mageekguy\atoum\test
     */
	protected function beforeTestMethod($testMethod)
	{
		return $this;
	}


    /**
     * @param string $testMethod current test method
     *
     * @return \mageekguy\atoum\test
     */
	protected function afterTestMethod($testMethod)
	{
		return $this;
	}


    /**
     * @return \mageekguy\atoum\test
     */
	protected function tearDown()
	{
		return $this;
	}


    /**
     * @param \exception $exception
     *
     * @return \mageekguy\atoum\test
     */
	protected function addExceptionToScore(\exception $exception)
	{
		list($file, $line) = $this->getBacktrace($exception->getTrace());

		$this->score->addException($file, $line, $this->class, $this->currentMethod, $exception);

		return $this;
	}


    /**
     * @param array $trace
     *
     * @return array|null
     */
	protected function getBacktrace(array $trace = null)
	{
		$debugBacktrace = $trace === null ? debug_backtrace(false) : $trace;

		foreach ($debugBacktrace as $key => $value)
		{
			if (isset($value['class']) === true && isset($value['function']) === true && $value['class'] === $this->class && $value['function'] === $this->currentMethod)
			{
				if (isset($debugBacktrace[$key - 1]) === true)
				{
					$key -= 1;
				}

				return array(
					$debugBacktrace[$key]['file'],
					$debugBacktrace[$key]['line']
				);
			}
		}

		return null;
	}


    /**
     * @param array $methods
     *
     * @return \mageekguy\atoum\test
     */
	protected function runTestMethods(array $methods)
	{
		$this->runTestMethods = $methods;
		$this->size = sizeof($this->runTestMethods);

		return $this;
	}


    /**
     * @return \mageekguy\atoum\test
     */
	protected function checkMethod($methodName)
	{
		if (isset($this->testMethods[$methodName]) === false)
		{
			throw new exceptions\logic\invalidArgument('Test method ' . $this->class . '::' . $methodName . '() is unknown');
		}

		return $this;
	}


    /**
     * @return \mageekguy\atoum\test
     */
	private function runChild()
	{
		if ($this->canRunChild() === true)
		{
			$php = @proc_open(
				escapeshellarg($this->getPhpPath()),
				array(
					0 => array('pipe', 'r'),
					1 => array('pipe', 'w'),
					2 => array('pipe', 'w')
				),
				$pipes
			);

			stream_set_blocking($pipes[1], 0);
			stream_set_blocking($pipes[2], 0);

			$currentMethod = array_shift($this->runTestMethods);

			fwrite($pipes[0], sprintf($this->phpCode, $currentMethod));
			fclose($pipes[0]);
			unset($pipes[0]);

			$this->children[$currentMethod] = array(
				$php,
				$pipes,
				'',
				''
			);
		}

		return $this;
	}


    /**
     * @return boolean
     */
	private function canRunChild()
	{
		return ($this->runTestMethods && ($this->maxChildrenNumber === null || sizeof($this->children) < $this->maxChildrenNumber));
	}


	private static function cleanNamespace($namespace)
	{
		return trim((string) $namespace, '\\');
	}
}

?>
