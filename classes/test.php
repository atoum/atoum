<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum\mock,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

abstract class test implements observable, adapter\aggregator, \countable
{
	const testMethodPrefix = 'test';
	const runStart = 'testRunStart';
	const beforeSetUp = 'beforeTestSetUp';
	const afterSetUp = 'afterTestSetUp';
	const beforeTestMethod = 'beforeTestMethod';
	const fail = 'testAssertionFail';
	const error = 'testError';
	const uncompleted = 'testUncompleted';
	const exception = 'testException';
	const success = 'testAssertionSuccess';
	const afterTestMethod = 'afterTestMethod';
	const beforeTearDown = 'beforeTestTearDown';
	const afterTearDown = 'afterTestTearDown';
	const runStop = 'testRunStop';
	const defaultNamespace = 'tests\units';

	private $phpPath = null;
	private $path = '';
	private $class = '';
	private $classNamespace = '';
	private $adapter = null;
	private $asserterGenerator = null;
	private $score = null;
	private $observers = array();
	private $tags = array();
	private $ignore = false;
	private $testMethods = array();
	private $runTestMethods = array();
	private $currentMethod = null;
	private $testNamespace = null;
	private $mockGenerator = null;
	private $size = 0;
	private $phpCode = '';
	private $children = array();
	private $maxChildrenNumber = null;
	private $codeCoverage = false;
	private $assertHasCase = false;

	private static $namespace = null;

	public function __construct(score $score = null, locale $locale = null, adapter $adapter = null, superglobals $superglobals = null)
	{
		$this
			->setScore($score ?: new score())
			->setLocale($locale ?: new locale())
			->setAdapter($adapter ?: new adapter())
			->setSuperglobals($superglobals ?: new superglobals())
			->enableCodeCoverage()
		;

		$class = new \reflectionClass($this);

		$this->path = $class->getFilename();
		$this->class = $class->getName();
		$this->classNamespace = $class->getNamespaceName();

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

		$this->runTestMethods($this->getTestMethods());
	}

	public function __toString()
	{
		return $this->getClass();
	}

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

	public function __call($method, $arguments)
	{
		switch ($method)
		{
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

	public function codeCoverageIsEnabled()
	{
		return $this->codeCoverage;
	}

	public function enableCodeCoverage()
	{
		$this->codeCoverage = $this->adapter->extension_loaded('xdebug');

		return $this;
	}

	public function disableCodeCoverage()
	{
		$this->codeCoverage = false;

		return $this;
	}

	public function setMaxChildrenNumber($number)
	{
		if ($number < 1)
		{
			throw new exceptions\logic\invalidArgument('Maximum number of children must be greater or equal to 1');
		}

		$this->maxChildrenNumber = $number;

		return $this;
	}

	public function setSuperglobals(superglobals $superglobals)
	{
		$this->superglobals = $superglobals;

		return $this;
	}

	public function getSuperglobals()
	{
		return $this->superglobals;
	}

	public function setMockGenerator(mock\generator $generator)
	{
		$this->mockGenerator = $generator;

		return $this;
	}

	public function getMockGenerator()
	{
		return $this->mockGenerator ?: $this->setMockGenerator(new mock\generator())->mockGenerator;
	}

	public function setAsserterGenerator(asserter\generator $generator)
	{
		$this->asserterGenerator = $generator->setTest($this);

		return $this;
	}

	public function getAsserterGenerator()
	{
		test\adapter::resetCallsForAllInstances();

		return $this->asserterGenerator ?: $this->setAsserterGenerator(new asserter\generator($this, $this->locale))->asserterGenerator;
	}

	public function setTestNamespace($testNamespace)
	{
		$this->testNamespace = self::cleanNamespace($testNamespace);

		if ($this->testNamespace === '')
		{
			throw new exceptions\logic\invalidArgument('Test namespace must not be empty');
		}

		return $this;
	}

	public function getTestNamespace()
	{
		return $this->testNamespace ?: self::getNamespace();
	}

	public function setPhpPath($path)
	{
		$this->phpPath = (string) $path;

		return $this;
	}

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

	public function getTags()
	{
		$tags = $this->getClassTags();

		foreach ($this->getMethodTags() as $methodTags)
		{
			$tags = array_merge($tags, $methodTags);
		}

		return array_values(array_unique($tags));
	}

	public function getClassTags()
	{
		return $this->tags;
	}

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
				throw new exceptions\logic\invalidargument('Test method ' . $this->class . '::' . $testMethodName . '() is unknown');
			}

			$tags = isset($this->testMethods[$testMethodName]['tags']) === false ? $classTags : $this->testMethods[$testMethodName]['tags'];
		}

		return $tags;
	}

	public function setAdapter(adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setScore(score $score)
	{
		$this->score = $score;

		return $this;
	}

	public function getScore()
	{
		return $this->score;
	}

	public function setLocale(locale $locale)
	{
		$this->locale = $locale;

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

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

	public function getClass()
	{
		return $this->class;
	}

	public function getClassNamespace()
	{
		return $this->classNamespace;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getTaggedTestMethods(array $methods, array $tags = array())
	{
		return array_values(array_uintersect($methods, $this->getTestMethods($tags), 'strcasecmp'));
	}

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

	public function getCurrentMethod()
	{
		return $this->currentMethod;
	}

	public function getMaxChildrenNumber()
	{
		return $this->maxChildrenNumber;
	}

	public function count()
	{
		return $this->size;
	}

	public function addObserver(observers\test $observer)
	{
		$this->observers[] = $observer;

		return $this;
	}

	public function callObservers($method)
	{
		foreach ($this->observers as $observer)
		{
			$observer->{$method}($this);
		}

		return $this;
	}

	public function ignore($boolean)
	{
		$this->ignore = ($boolean == true);

		$this->runTestMethods($this->getTestMethods());

		return $this;
	}

	public function isIgnored()
	{
		return ($this->ignore === true);
	}

	public function methodIsIgnored($methodName, array $tags = array())
	{
		if (isset($this->testMethods[$methodName]) === false)
		{
			throw new exceptions\logic\invalidArgument('Test method ' . $this->class . '::' . $methodName . '() is unknown');
		}

		$isIgnored = (isset($this->testMethods[$methodName]['ignore']) === true ? $this->testMethods[$methodName]['ignore'] : $this->ignore);

		if ($isIgnored === false && $tags)
		{
			$isIgnored = sizeof($methodTags = $this->getMethodTags($methodName)) <= 0 || sizeof(array_intersect($tags, $methodTags)) <= 0;
		}

		return $isIgnored;
	}

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

					$this->beforeTestMethod($this->currentMethod);

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

					$this->afterTestMethod($testMethod);

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
					'require \'' . $this->path . '\';' .
					'$test = new ' . $this->class . '();' .
					'$test->setLocale(new ' . get_class($this->locale) . '(' . $this->locale->get() . '));' .
					'$test->setPhpPath(\'' . $this->getPhpPath() . '\');' .
					($this->codeCoverageIsEnabled() === true ? '' : '$test->disableCodeCoverage();') .
					'$test->runTestMethod($method = \'%s\');' .
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

	public function errorHandler($errno, $errstr, $errfile, $errline, $context)
	{
		if (error_reporting() !== 0)
		{
			list($file, $line) = $this->getBacktrace();

			$this->score->addError($file, $line, $this->class, $this->currentMethod, $errno, $errstr, $errfile, $errline);
		}

		return true;
	}

	public function mock($class, $mockNamespace = null, $mockClass = null)
	{
		$this->getMockGenerator()->generate($class, $mockNamespace, $mockClass);

		return $this;
	}

	public function mockTestedClass($mockNamespace = null, $mockClass = null)
	{
		$this->getMockGenerator()->generate($this->getTestedClassName(), $mockNamespace, $mockClass);

		return $this;
	}

	public function setTestsSubNamespace($testsSubNamespace)
	{
		#DEPRECATED
		die(__METHOD__ . ' is deprecated, please use ' . __CLASS__ . '::setTestNamespace() instead');
	}

	public function getTestsSubNamespace()
	{
		#DEPRECATED
		die(__METHOD__ . ' is deprecated, please use ' . __CLASS__ . '::getTestNamespace() instead');
	}

	public static function setNamespace($namespace)
	{
		self::$namespace = self::cleanNamespace($namespace);

		if (self::$namespace === '')
		{
			throw new exceptions\logic\invalidArgument('Namespace must not be empty');
		}
	}

	public static function getNamespace()
	{
		return self::$namespace ?: self::defaultNamespace;
	}

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
			self::uncompleted,
			self::success,
			self::afterTestMethod,
			self::beforeTearDown,
			self::afterTearDown,
			self::runStop
		);
	}

	protected function setUp()
	{
		return $this;
	}

	protected function startCase($case)
	{
		$this->score->setCase($case);

		return $this;
	}

	protected function beforeTestMethod($testMethod)
	{
		return $this;
	}

	protected function afterTestMethod($testMethod)
	{
		return $this;
	}

	protected function tearDown()
	{
		return $this;
	}

	protected function addExceptionToScore(\exception $exception)
	{
		list($file, $line) = $this->getBacktrace($exception->getTrace());

		$this->score->addException($file, $line, $this->class, $this->currentMethod, $exception);

		return $this;
	}

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

	protected function runTestMethods(array $methods)
	{
		$this->runTestMethods = $methods;
		$this->size = sizeof($this->runTestMethods);

		return $this;
	}

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
		}

		return $this;
	}

	private function canRunChild()
	{
		return ($this->runTestMethods && ($this->maxChildrenNumber === null || sizeof($this->children) < $this->maxChildrenNumber));
	}

	private function setCaseOnAssert($case)
	{
		$this->startCase($case)->assertHasCase = true;

		return $this;
	}

	private function unsetCaseOnAssert()
	{
		if ($this->assertHasCase === true)
		{
			$this->score->unsetCase();
		}

		return $this;
	}

	private static function cleanNamespace($namespace)
	{
		return trim((string) $namespace, '\\');
	}
}

?>
