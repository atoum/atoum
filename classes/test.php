<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

/**
 * @property    asserter\generator  assert
 * @property    asserter\generator  define
 * @property    mock\generator      mockGenerator
 *
 * @method      asserter\generator  assert()
 * @method      test                mock()
 */
abstract class test implements observable, adapter\aggregator, \countable
{
	const testMethodPrefix = 'test';
	const runStart = 'testRunStart';
	const beforeSetUp = 'beforeTestSetUp';
	const afterSetUp = 'afterTestSetUp';
	const beforeTestMethod = 'beforeTestMethod';
	const fail = 'testAssertionFail';
	const error = 'testError';
	const exception = 'testException';
	const success = 'testAssertionSuccess';
	const afterTestMethod = 'afterTestMethod';
	const beforeTearDown = 'beforeTestTearDown';
	const afterTearDown = 'afterTestTearDown';
	const runStop = 'testRunStop';
	const defaultNamespace = 'tests\units';

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
     * @var atoum\adapter
     */
	private $adapter = null;

    /**
     * @var asserter\generator
     */
	private $asserterGenerator = null;

    /**
     * @var score
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
     * @var mock\generator
     */
	private $mockGenerator = null;

    /**
     * To delete ?
     */
	private $child = null;

    /**
     * @var type
     */
	private $testsToRun = 0;

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
     * @var boolean
     */
	private $assertHasCase = false;


    /**
     * @var atoum\superglobals
     */
    public $superglobals = null;

    /**
     * @var locale
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
     * @param score $score
     * @param locale $locale
     * @param adapter $adapter
     *
     * @throws exceptions\runtime
     */
	public function __construct(score $score = null, locale $locale = null, adapter $adapter = null)
	{
		$this
			->setScore($score ?: new score())
			->setLocale($locale ?: new locale())
			->setAdapter($adapter ?: new adapter())
			->setSuperglobals(new atoum\superglobals())
			->enableCodeCoverage()
		;

		$class = new \reflectionClass($this);

		$this->class = $class->getName();

		$this->path = $class->getFilename();

		$testedClassName = $this->getTestedClassName();

		if ($testedClassName === null)
		{
			throw new exceptions\runtime('Test class \'' . $this->getClass() . '\' is not in a namespace which contains \'' . $this->getTestNamespace() . '\'');
		}

		if ($this->adapter->class_exists($testedClassName) === false)
		{
			throw new exceptions\runtime('Tested class \'' . $testedClassName . '\' does not exist for test class \'' . $this->getClass() . '\'');
		}

		$this->getAsserterGenerator()
			->setAlias('array', 'phpArray')
			->setAlias('class', 'phpClass')
		;

		foreach (new annotations\extractor($class->getDocComment()) as $annotation => $value)
		{
			switch ($annotation)
			{
				case 'ignore':
					$this->ignore = $value == 'on';
					break;

				case 'tags':
					$this->tags = array_values(array_unique(preg_split('/\s+/', $value)));
					break;
			}
		}

		foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $publicMethod)
		{
			$methodName = $publicMethod->getName();

			if (strpos($methodName, self::testMethodPrefix) === 0)
			{
				$annotations = array();

				foreach (new annotations\extractor($publicMethod->getDocComment()) as $annotation => $value)
				{
					switch ($annotation)
					{
						case 'ignore':
							$annotations['ignore'] = $value == 'on';
							break;

						case 'tags':
							$annotations['tags'] = array_values(array_unique(preg_split('/\s+/', $value)));
							break;
					}
				}

				$this->testMethods[$methodName] = $annotations;
			}
		}

		$this->runTestMethods = $this->getTestMethods();
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
     * @return asserter\generator|mock\generator
     *
     * @throws exceptions\logic\invalidArgument
     */
	public function __get($property)
	{
		switch ($property)
		{
			case 'define':
				return $this->getAsserterGenerator();

			case 'assert':
				return $this->unsetCaseOnAssert()->getAsserterGenerator();

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
     * @return test|asserter\generator
     *
     * @throws exceptions\logic\invalidArgument
     */
	public function __call($method, $arguments)
	{
		switch ($method)
		{
			case 'mock':
				$this->getMockGenerator()->generate(isset($arguments[0]) === false ? null : $arguments[0], isset($arguments[1]) === false ? null : $arguments[1], isset($arguments[2]) === false ? null : $arguments[2]);
				return $this;

			case 'assert':
				$this->unsetCaseOnAssert();

				$case = isset($arguments[0]) === false ? null : $arguments[0];

				if ($case !== null)
				{
					$this->setCaseOnAssert($case);
				}

				return $this->getAsserterGenerator();

			default:
				throw new exceptions\logic\invalidArgument('Method ' . get_class($this) . '::' . $method . '() is undefined');
		}
	}


    /**
     * @return type
     */
	public function codeCoverageIsEnabled()
	{
		return $this->codeCoverage;
	}


    /**
     * @return test
     */
	public function enableCodeCoverage()
	{
		$this->codeCoverage = $this->adapter->extension_loaded('xdebug');

		return $this;
	}


    /**
     * @return test
     */
	public function disableCodeCoverage()
	{
		$this->codeCoverage = false;

		return $this;
	}


    /**
     * @param integer $number
     *
     * @return test
     *
     * @throws exceptions\logic\invalidArgument
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
     * @param atoum\superglobals $superglobals
     *
     * @return test
     */
	public function setSuperglobals(atoum\superglobals $superglobals)
	{
		$this->superglobals = $superglobals;

		return $this;
	}


    /**
     * @return atoum\superglobals
     */
	public function getSuperglobals()
	{
		return $this->superglobals;
	}


    /**
     * @param mock\generator $generator
     *
     * @return test
     */
	public function setMockGenerator(mock\generator $generator)
	{
		$this->mockGenerator = $generator;

		return $this;
	}


    /**
     * @return mock\generator
     */
	public function getMockGenerator()
	{
		return $this->mockGenerator ?: $this->setMockGenerator(new mock\generator())->mockGenerator;
	}


    /**
     * @param asserter\generator $generator
     *
     * @return test
     */
	public function setAsserterGenerator(asserter\generator $generator)
	{
		$this->asserterGenerator = $generator->setTest($this);

		return $this;
	}


    /**
     * @return asserter\generator
     */
	public function getAsserterGenerator()
	{
		atoum\test\adapter::resetCallsForAllInstances();

		return $this->asserterGenerator ?: $this->setAsserterGenerator(new asserter\generator($this, $this->locale))->asserterGenerator;
	}


    /**
     * @param string $testNamespace
     *
     * @return test
     *
     * @throws atoum\exceptions\logic\invalidArgument
     */
	public function setTestNamespace($testNamespace)
	{
		$this->testNamespace = self::cleanNamespace($testNamespace);

		if ($this->testNamespace === '')
		{
			throw new atoum\exceptions\logic\invalidArgument('Test namespace must not be empty');
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
     * @return test
     */
	public function setPhpPath($path)
	{
		$this->phpPath = (string) $path;

		return $this;
	}


    /**
     * @return string
     *
     * @throws exceptions\runtime
     */
	public function getPhpPath()
	{
		if ($this->phpPath === null)
		{
			if (isset($this->superglobals->_SERVER['_']) === false)
			{
				throw new exceptions\runtime('Unable to find PHP executable');
			}

			$this->setPhpPath($this->superglobals->_SERVER['_']);
		}

		return $this->phpPath;
	}


    /**
     * @return array
     */
	public function getTags()
	{
		$tags = $this->getClassTags();

		foreach ($this->getMethodTags() as $methodTags)
		{
			$tags = array_merge($tags, $methodTags);
		}

		return array_values(array_unique($tags));
	}


    /**
     * @return array
     */
	public function getClassTags()
	{
		return $this->tags;
	}


    /**
     * @param string $testMethodName
     *
     * @return array
     *
     * @throws exceptions\logic\invalidargument
     */
	public function getMethodTags($testMethodName = null)
	{
		$tags = array();

		$classTags = $this->getClassTags();

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
				throw new exceptions\logic\invalidargument('test method ' . $this->class . '::' . $testMethodName . '() is unknown');
			}

			$tags = isset($this->testMethods[$testMethodName]['tags']) === false ? $classTags : $this->testMethods[$testMethodName]['tags'];
		}

		return $tags;
	}


    /**
     * @param atoum\adapter $adapter
     *
     * @return test
     */
	public function setAdapter(atoum\adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}


    /**
     * @return atoum\adapter
     */
	public function getAdapter()
	{
		return $this->adapter;
	}


    /**
     * @param score $score
     *
     * @return test
     */
	public function setScore(score $score)
	{
		$this->score = $score;

		return $this;
	}


    /**
     * @return score
     */
	public function getScore()
	{
		return $this->score;
	}


    /**
     * @param locale $locale
     *
     * @return test
     */
	public function setLocale(locale $locale)
	{
		$this->locale = $locale;

		return $this;
	}


    /**
     * @return locale
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
		$class = null;

		$testClass = $this->getClass();
		$testNamespace = $this->getTestNamespace();

		$position = strpos($testClass, $testNamespace);

		if ($position !== false)
		{
			$class = trim(substr($testClass, 0, $position) . substr($testClass, $position + strlen($testNamespace) + 1), '\\');
		}

		return $class;
	}


    /**
     * @return string
     */
	public function getClass()
	{
		return $this->class;
	}


    /**
     * @return string
     */
	public function getPath()
	{
		return $this->path;
	}


    /**
     * @return array
     */
	public function getTestMethods()
	{
		$testMethods = array();

		foreach ($this->testMethods as $methodName => $annotations)
		{
			if (isset($annotations['ignore']) === true ? $annotations['ignore'] === false : $this->ignore === false)
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
	public function count()
	{
		return sizeof($this->runTestMethods);
	}


    /**
     * @param atoum\observers\test $observer
     *
     * @return test
     */
	public function addObserver(atoum\observers\test $observer)
	{
		$this->observers[] = $observer;

		return $this;
	}


    /**
     * @param string $method
     *
     * @return test
     */
	public function callObservers($method)
	{
		foreach ($this->observers as $observer)
		{
			$observer->{$method}($this);
		}

		return $this;
	}


    /**
     * @param boolean $boolean
     *
     * @return test
     */
	public function ignore($boolean)
	{
		$this->ignore = ($boolean == true);

		$this->runTestMethods = $this->getTestMethods();

		return $this;
	}


    /**
     * @return boolean
     */
	public function isIgnored()
	{
		return ($this->ignore === true);
	}


    /**
     * @param string $testMethodName
     * @param array  $tags
     *
     * @return boolean
     *
     * @throws exceptions\logic\invalidArgument
     */
	public function methodIsIgnored($testMethodName, array $tags = array())
	{
		if (isset($this->testMethods[$testMethodName]) === false)
		{
			throw new exceptions\logic\invalidArgument('Test method ' . $this->class . '::' . $testMethodName . '() is unknown');
		}

		$isIgnored = (isset($this->testMethods[$testMethodName]['ignore']) === true ? $this->testMethods[$testMethodName]['ignore'] : $this->ignore);

		if ($isIgnored === false && sizeof($tags) > 0)
		{
			$isIgnored = sizeof($methodTags = $this->getMethodTags($testMethodName)) <= 0 || sizeof(array_intersect($tags, $methodTags)) <= 0;
		}

		return $isIgnored;
	}


    /**
     * @param string $testMethod
     * @param array  $tags
     *
     * @return test
     *
     * @throws exception
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
					$this->beforeTestMethod($this->currentMethod);

					ob_start();

					if ($this->codeCoverageIsEnabled() === true)
					{
						xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
					}

					$time = microtime(true);
					$memory = memory_get_usage(true);

					$this->{$testMethod}();

					$memoryUsage = memory_get_usage(true) - $memory;
					$duration = microtime(true) - $time;

					if ($this->codeCoverageIsEnabled() === true)
					{
						$this->score->getCoverage()->addXdebugDataForTest($this, xdebug_get_code_coverage());
						xdebug_stop_code_coverage();
					}

					$this->score
						->addMemoryUsage($this->class, $this->currentMethod, $memoryUsage)
						->addDuration($this->class, $this->currentMethod, $duration)
						->addOutput($this->class, $this->currentMethod, ob_get_clean())
					;

					$this->afterTestMethod($testMethod);
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
     * @return test
     *
     * @throws exceptions\logic\invalidArgument
     * @throws exception
     * @throws exceptions\runtime
     */
	public function run(array $runTestMethods = array(), array $tags = array())
	{
		if ($this->isIgnored() === false)
		{
			if (sizeof($runTestMethods) > 0)
			{
				$unknownTestMethods = array_diff($runTestMethods, $this->getTestMethods());

				if (sizeof($unknownTestMethods) > 0)
				{
					throw new exceptions\logic\invalidArgument('Test method ' . $this->class . '::' . current($unknownTestMethods) . '() is unknown or ignored');
				}

				$this->runTestMethods = $runTestMethods;
			}

			if (sizeof($tags) > 0)
			{
				$runTestMethods = array();

				foreach ($this->runTestMethods as $runTestMethod)
				{
					if ($this->methodIsIgnored($runTestMethod, $tags) === false)
					{
						$runTestMethods[] = $runTestMethod;
					}
				}

				$this->runTestMethods = $runTestMethods;
			}

			$this->testsToRun = sizeof($this->runTestMethods);

			$this->callObservers(self::runStart);

			if ($this->testsToRun > 0)
			{
				$this->phpCode =
					'<?php ' .
					'ob_start();' .
					'define(\'' . __NAMESPACE__ . '\autorun\', false);' .
					'require \'' . $this->path . '\';' .
					'$test = new ' . $this->class . '();' .
					'$test->setLocale(new ' . get_class($this->locale) . '(' . $this->locale->get() . '));' .
					'$test->setPhpPath(\'' . $this->getPhpPath() . '\');' .
					($this->codeCoverageIsEnabled() === true ? '' : '$test->disableCodeCoverage();') .
					'$test->runTestMethod($method = \'%s\');' .
					'echo serialize($test->getScore()->addOutput(\'' . $this->class . '\', $method, ob_get_clean()));' .
					'?>'
				;

				$null = null;

				try
				{
					$this->callObservers(self::beforeSetUp);
					$this->setUp();
					$this->callObservers(self::afterSetUp);

					while (sizeof($this->runChild()->children) > 0)
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

						if ($pipesUpdated !== false && $pipesUpdated > 0)
						{
							$children = $this->children;
							$this->children = array();

							foreach ($children as $testMethod => $child)
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
									$this->children[] = $child;
								}
								else
								{
									$phpStatus = proc_get_status($child[0]);

									while ($phpStatus['running'] == true)
									{
										$phpStatus = proc_get_status($child[0]);
									}

									proc_close($child[0]);

									$this->currentMethod = $testMethod;
									$this->callObservers(self::afterTestMethod);
									$this->currentMethod = null;

									switch ($phpStatus['exitcode'])
									{
										case 126:
										case 127:
											throw new exceptions\runtime('Unable to execute test with \'' . $this->getPhpPath() . '\'');
									}

									$score = new score();

									if ($child[2] !== '')
									{
										$score = @unserialize($child[2]);

										if ($score instanceof score === false)
										{
											$score = new score();
										}
									}

									if ($child[3] !== '')
									{
										if (preg_match_all('/([^:]+): (.+) in (.+) on line ([0-9]+)/', trim($child[3]), $errors, PREG_SET_ORDER) === 0)
										{
											$score->addError($this->path, null, $this->class, $testMethod, 'UNKNOWN', $child[3]);
										}
										else foreach ($errors as $error)
										{
											$score->addError($this->path, null, $this->class, $testMethod, $error[1], $error[2], $error[3], $error[4]);
										}
									}

									if ($score->getFailNumber() > 0)
									{
										$this->callObservers(self::fail);
									}

									if ($score->getErrorNumber() > 0)
									{
										$this->callObservers(self::error);
									}

									if ($score->getExceptionNumber() > 0)
									{
										$this->callObservers(self::exception);
									}

									if ($score->getPassNumber() > 0)
									{
										$this->callObservers(self::success);
									}

									$this->score->merge($score);
								}
							}
						}
					}

					$this
						->callObservers(self::beforeTearDown)
						->tearDown()
						->callObservers(self::afterTearDown)
					;
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
     * @return test
     */
	public function mock($class, $mockNamespace = null, $mockClass = null)
	{
		$this->getMockGenerator()->generate($class, $mockNamespace, $mockClass);

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
     * @param string $namespace
     *
     * @throws atoum\exceptions\logic\invalidArgument
     */
	public static function setNamespace($namespace)
	{
		self::$namespace = self::cleanNamespace($namespace);

		if (self::$namespace === '')
		{
			throw new atoum\exceptions\logic\invalidArgument('Namespace must not be empty');
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
     * @return array
     */
	public static function getObserverEvents()
	{
		return array(
			self::runStart,
			self::beforeSetUp,
			self::afterSetUp,
			self::beforeTestMethod,
			self::fail,
			self::error,
			self::exception,
			self::success,
			self::afterTestMethod,
			self::beforeTearDown,
			self::afterTearDown,
			self::runStop
		);
	}


    /**
     * @return test
     */
	protected function setUp()
	{
		return $this;
	}


    /**
     * @param string $case
     *
     * @return test
     */
	protected function startCase($case)
	{
		$this->score->setCase($case);

		return $this;
	}


    /**
     * @param string $testMethod current test method
     *
     * @return test
     */
	protected function beforeTestMethod($testMethod)
	{
		return $this;
	}


    /**
     * @param string $testMethod current test method
     *
     * @return test
     */
	protected function afterTestMethod($testMethod)
	{
		return $this;
	}


    /**
     * @return test
     */
	protected function tearDown()
	{
		return $this;
	}


    /**
     * @param \exception $exception
     *
     * @return test
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
     * @return test
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

			$this->callObservers(self::beforeTestMethod);

			fwrite($pipes[0], sprintf($this->phpCode, $currentMethod));
			fclose($pipes[0]);
			unset($pipes[0]);

			$this->children[$currentMethod] = array(
				$php,
				$pipes,
				'',
				''
			);

			$this->testsToRun--;
		}

		return $this;
	}


    /**
     * @return boolean
     */
	private function canRunChild()
	{
		return ($this->testsToRun > 0 && ($this->maxChildrenNumber === null || sizeof($this->children) < $this->maxChildrenNumber));
	}


    /**
     * @param string $case
     *
     * @return test
     */
	private function setCaseOnAssert($case)
	{
		$this->startCase($case)->assertHasCase = true;

		return $this;
	}


    /**
     * @return test
     */
	private function unsetCaseOnAssert()
	{
		if ($this->assertHasCase === true)
		{
			$this->score->unsetCase();
		}

		return $this;
	}


    /**
     * @param string $namespace
     *
     * @return string
     */
	private static function cleanNamespace($namespace)
	{
		return trim((string) $namespace, '\\');
	}
}

?>
