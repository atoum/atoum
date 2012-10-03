<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum\test,
	mageekguy\atoum\mock,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\annotations
;

abstract class test implements observable, adapter\aggregator, \countable
{
	const testMethodPrefix = 'test';
	const defaultNamespace = '#(?:^|\\\)tests?\\\units?\\\#i';
	const runStart = 'testRunStart';
	const beforeSetUp = 'beforeTestSetUp';
	const afterSetUp = 'afterTestSetUp';
	const beforeTestMethod = 'beforeTestMethod';
	const fail = 'testAssertionFail';
	const error = 'testError';
	const void = 'testVoid';
	const uncompleted = 'testUncompleted';
	const exception = 'testException';
	const runtimeException = 'testRuntimeException';
	const success = 'testAssertionSuccess';
	const afterTestMethod = 'afterTestMethod';
	const beforeTearDown = 'beforeTestTearDown';
	const afterTearDown = 'afterTestTearDown';
	const runStop = 'testRunStop';
	const defaultEngine = 'concurrent';
	const enginesNamespace = '\mageekguy\atoum\test\engines';

	private $phpPath = null;
	private $path = '';
	private $class = '';
	private $classNamespace = '';
	private $testedClass = null;
	private $factory = null;
	private $adapter = null;
	private $assertionManager = null;
	private $asserterGenerator = null;
	private $score = null;
	private $observers = array();
	private $tags = array();
	private $ignore = false;
	private $dataProviders = array();
	private $testMethods = array();
	private $runTestMethods = array();
	private $currentMethod = null;
	private $testNamespace = null;
	private $mockGenerator = null;
	private $size = 0;
	private $engines = array();
	private $classEngine = null;
	private $methodEngines = array();
	private $classHasNotVoidMethods = false;
	private $methodsAreNotVoid = array();
	private $asynchronousEngines = 0;
	private $maxAsynchronousEngines = null;
	private $codeCoverage = false;
	private $includer = null;
	private $bootstrapFile = null;
	private $executeOnFailure = array();
	private $debugMode = false;

	private static $namespace = null;
	private static $defaultEngine = self::defaultEngine;

	public function __construct(factory $factory = null)
	{
		$this
			->setFactory($factory ?: new factory())
			->setScore($this->factory['mageekguy\atoum\test\score']())
			->setLocale($this->factory['mageekguy\atoum\locale']())
			->setAdapter($this->factory['mageekguy\atoum\adapter']())
			->setSuperglobals($this->factory['mageekguy\atoum\superglobals']())
			->setIncluder($this->factory['mageekguy\atoum\includer']())
			->enableCodeCoverage()
		;

		$class = $this->factory['reflectionClass']($this);

		$this->path = $class->getFilename();
		$this->class = $class->getName();
		$this->classNamespace = $class->getNamespaceName();

		$annotationExtractor = $this->factory['mageekguy\atoum\annotations\extractor']();

		$test = $this;

		$annotationExtractor
			->setHandler('ignore', function($value) use ($test) { $test->ignore(annotations\extractor::toBoolean($value)); })
			->setHandler('tags', function($value) use ($test) { $test->setTags(annotations\extractor::toArray($value)); })
			->setHandler('namespace', function($value) use ($test) { $test->setTestNamespace($value); })
			->setHandler('maxChildrenNumber', function($value) use ($test) { $test->setMaxChildrenNumber($value); })
			->setHandler('engine', function($value) use ($test) { $test->setClassEngine($value); })
			->setHandler('hasVoidMethods', function($value) use ($test) { $test->classHasVoidMethods(); })
			->setHandler('hasNotVoidMethods', function($value) use ($test) { $test->classHasNotVoidMethods(); })
			->extract($class->getDocComment())
		;

		if ($this->testNamespace === null)
		{
			$annotationExtractor
				->unsetHandler('ignore')
				->unsetHandler('tags')
				->unsetHandler('maxChildrenNumber')
			;

			$parentClass = $class;

			while ($this->testNamespace === null && ($parentClass = $parentClass->getParentClass()) !== false)
			{
				$annotationExtractor->extract($parentClass->getDocComment());
			}
		}

		$annotationExtractor
			->resetHandlers()
			->setHandler('ignore', function($value) use ($test, & $methodName) { $test->ignoreMethod($methodName, annotations\extractor::toBoolean($value)); })
			->setHandler('tags', function($value) use ($test, & $methodName) { $test->setMethodTags($methodName, annotations\extractor::toArray($value)); })
			->setHandler('dataProvider', function($value) use ($test, & $methodName) { $test->setDataProvider($methodName, $value); })
			->setHandler('engine', function($value) use ($test, & $methodName) { $test->setMethodEngine($methodName, $value); })
			->setHandler('isVoid', function($value) use ($test, & $methodName) { $test->setMethodVoid($methodName); })
			->setHandler('isNotVoid', function($value) use ($test, & $methodName) { $test->setMethodNotVoid($methodName); })
		;

		foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $publicMethod)
		{
			if (strpos($methodName = $publicMethod->getName(), self::testMethodPrefix) === 0)
			{
				$this->testMethods[$methodName] = array();

				$annotationExtractor->extract($publicMethod->getDocComment());
			}
		}

		$this
			->runTestMethods($this->getTestMethods())
			->getAsserterGenerator()
				->setAlias('array', 'phpArray')
				->setAlias('class', 'phpClass')
		;

		$this->setAssertionManager($this->factory['mageekguy\atoum\test\assertion\manager']());
	}

	public function __toString()
	{
		return $this->getClass();
	}

	public function __get($property)
	{
		return $this->assertionManager->invoke($property);
	}

	public function __call($method, array $arguments)
	{
		return $this->assertionManager->invoke($method, $arguments);
	}

	public function setFactory(factory $factory)
	{
		$this->factory = $factory;

		return $this;
	}

	public function getFactory()
	{
		return $this->factory;
	}

	public function setClassEngine($engine)
	{
		$this->classEngine = (string) $engine;

		return $this;
	}

	public function getClassEngine()
	{
		return $this->classEngine;
	}

	public function classHasVoidMethods()
	{
		$this->classHasNotVoidMethods = false;
	}

	public function classHasNotVoidMethods()
	{
		$this->classHasNotVoidMethods = true;
	}

	public function setMethodVoid($method)
	{
		$this->methodsAreNotVoid[$method] = false;
	}

	public function setMethodNotVoid($method)
	{
		$this->methodsAreNotVoid[$method] = true;
	}

	public function methodIsNotVoid($method)
	{
		return (isset($this->methodsAreNotVoid[$method]) === false ? $this->classHasNotVoidMethods : $this->methodsAreNotVoid[$method]);
	}

	public function setMethodEngine($method, $engine)
	{
		$this->methodEngines[(string) $method] = (string) $engine;

		return $this;
	}

	public function getMethodEngine($method)
	{
		$method = (string) $method;

		return (isset($this->methodEngines[$method]) === false ? null : $this->methodEngines[$method]);
	}

	public function enableDebugMode()
	{
		$this->debugMode = true;

		return $this;
	}

	public function disableDebugMode()
	{
		$this->debugMode = false;

		return $this;
	}

	public function debugModeIsEnabled()
	{
		return $this->debugMode;
	}

	public function executeOnFailure(\closure $closure)
	{
		$this->executeOnFailure[] = $closure;

		return $this;
	}

	public function setAssertionManager(test\assertion\manager $assertionManager)
	{
		$this->assertionManager = $assertionManager;

		$test = $this;

		$this->assertionManager
			->setHandler('when', function($mixed) use ($test) { if ($mixed instanceof \closure) { $mixed(); } return $test; })
			->setHandler('assert', function($case = null) use ($test) { $test->stopCase(); if ($case !== null) { $test->startCase($case); } return $test; })
			->setHandler('mockGenerator', function() use ($test) { return $test->getMockGenerator(); })
			->setHandler('mockClass', function($class, $mockNamespace = null, $mockClass = null) use ($test) { $test->getMockGenerator()->generate($class, $mockNamespace, $mockClass); return $test; })
			->setHandler('mockTestedClass', function($mockNamespace = null, $mockClass = null) use ($test) { $test->getMockGenerator()->generate($test->getTestedClassName(), $mockNamespace, $mockClass); return $test; })
			->setHandler('dump', function() use ($test) { if ($test->debugModeIsEnabled() === true) { call_user_func_array('var_dump', func_get_args()); } return $test; })
			->setHandler('stop', function() use ($test) { if ($test->debugModeIsEnabled() === true) { throw new test\exceptions\stop(); } return $test; })
			->setHandler('executeOnFailure', function($callback) use ($test) { if ($test->debugModeIsEnabled() === true) { $test->executeOnFailure($callback); } return $test; })
			->setHandler('dumpOnFailure', function($variable) use ($test) { if ($test->debugModeIsEnabled() === true) { $test->executeOnFailure(function() use ($variable) { var_dump($variable); }); } return $test; })
		;

		$returnAssertionManager = function() use ($test) { return $test; };

		$this->assertionManager
			->setHandler('if', $returnAssertionManager)
			->setHandler('and', $returnAssertionManager)
			->setHandler('then', $returnAssertionManager)
			->setHandler('given', $returnAssertionManager)
		;

		$mockControllerExtractor = function(mock\aggregator $mock) { return $mock->getMockController(); };

		$this->assertionManager
			->setHandler('calling', $mockControllerExtractor)
			->setHandler('ƒ', $mockControllerExtractor)
		;

		$asserterGenerator = $this->asserterGenerator;

		$this->assertionManager
			->setHandler('define', function() use ($asserterGenerator) { return $asserterGenerator; })
			->setDefaultHandler(function($asserter, $arguments) use ($asserterGenerator) { return $asserterGenerator->getAsserterInstance($asserter, $arguments); })
		;

		return $this;
	}

	public function getAssertionManager()
	{
		return $this->assertionManager;
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
		$number = (int) $number;

		if ($number < 1)
		{
			throw new exceptions\logic\invalidArgument('Maximum number of children must be greater or equal to 1');
		}

		$this->maxAsynchronousEngines = $number;

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

	public function setMockGenerator(test\mock\generator $generator)
	{
		$this->mockGenerator = $generator->setTest($this);

		return $this;
	}

	public function getMockGenerator()
	{
		return $this->mockGenerator ?: $this->setMockGenerator($this->factory['mageekguy\atoum\test\mock\generator']($this))->mockGenerator;
	}

	public function setAsserterGenerator(test\asserter\generator $generator)
	{
		$this->asserterGenerator = $generator->setTest($this);

		return $this;
	}

	public function getAsserterGenerator()
	{
		test\adapter::resetCallsForAllInstances();

		return $this->asserterGenerator ?: $this->setAsserterGenerator($this->factory['mageekguy\atoum\test\asserter\generator']($this))->asserterGenerator;
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
		return $this->phpPath;
	}

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

	public function setTags(array $tags)
	{
		$this->tags = $tags;

		return $this;
	}

	public function getTags()
	{
		return $this->tags;
	}

	public function setMethodTags($testMethodName, array $tags)
	{
		if (isset($this->testMethods[$testMethodName]) === false)
		{
			throw new exceptions\logic\invalidArgument('Test method ' . $this->class . '::' . $testMethodName . '() is unknown');
		}

		$this->testMethods[$testMethodName]['tags'] = $tags;

		return $this;
	}

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

	public function getDataProviders()
	{
		return $this->dataProviders;
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

	public function setScore(test\score $score)
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
		if ($this->testedClass === null)
		{
			$testClass = $this->getClass();
			$testNamespace = $this->getTestNamespace();

			if (self::isRegex($testNamespace) === true)
			{
				if (preg_match($testNamespace, $testClass) === 0)
				{
					throw new exceptions\runtime('Test class \'' . $testClass . '\' is not in a namespace which match pattern \'' . $testNamespace . '\'');
				}

				$this->testedClass = trim(preg_replace($testNamespace, '\\', $testClass), '\\');
			}
			else
			{
				$position = strpos($testClass, $testNamespace);

				if ($position === false)
				{
					throw new exceptions\runtime('Test class \'' . $testClass . '\' is not in a namespace which contains \'' . $testNamespace . '\'');
				}

				$this->testedClass = trim(substr($testClass, 0, $position) . substr($testClass, $position + strlen($testNamespace) + 1), '\\');
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
		return $this->maxAsynchronousEngines;
	}

	public function getCoverage()
	{
		return $this->score->getCoverage();
	}

	public function count()
	{
		return $this->size;
	}

	public function addObserver(observer $observer)
	{
		$this->observers[] = $observer;

		return $this;
	}

	public function callObservers($event)
	{
		foreach ($this->observers as $observer)
		{
			$observer->handleEvent($event, $this);
		}

		return $this;
	}

	public function ignore($boolean)
	{
		$this->ignore = ($boolean == true);

		return $this->runTestMethods($this->getTestMethods());
	}

	public function isIgnored()
	{
		return (sizeof($this) <=0 || $this->ignore === true);
	}

	public function ignoreMethod($methodName, $boolean)
	{
		$this->checkMethod($methodName)->testMethods[$methodName]['ignore'] = $boolean == true;

		return $this->runTestMethods($this->getTestMethods());
	}

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

	public function runTestMethod($testMethod, array $tags = array())
	{
		if ($this->methodIsIgnored($testMethod, $tags) === false)
		{
			$mockGenerator = $this->getMockGenerator();
			$mockNamespacePattern = '/^' . preg_quote($mockGenerator->getDefaultNamespace()) . '\\\/i';

			$mockAutoloader = function($class) use ($mockGenerator, $mockNamespacePattern) {
				$mockedClass = preg_replace($mockNamespacePattern, '', $class);

				if ($mockedClass !== $class)
				{
					$mockGenerator->generate($mockedClass);
				}
			};

			if (spl_autoload_register($mockAutoloader, true, true) === false)
			{
				throw new \runtimeException('Unable to register mock autoloader');
			}

			set_error_handler(array($this, 'errorHandler'));

			ini_set('display_errors', 'stderr');
			ini_set('log_errors', 'Off');
			ini_set('log_errors_max_len', '0');

			$this->currentMethod = $testMethod;
			$this->executeOnFailure = array();

			try
			{
				try
				{
					ob_start();

					if ($this->adapter->class_exists($testedClassName = $this->getTestedClassName()) === false)
					{
						throw new exceptions\runtime('Tested class \'' . $testedClassName . '\' does not exist for test class \'' . $this->getClass() . '\'');
					}

					$this->beforeTestMethod($this->currentMethod);

					if ($this->codeCoverageIsEnabled() === true)
					{
						xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
					}

					$assertionNumber = $this->score->getAssertionNumber();
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

						$reflectedTestMethod = $this->factory['reflectionMethod']($this, $testMethod);
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

					$this->score
						->addMemoryUsage($this->path, $this->class, $this->currentMethod, $memoryUsage)
						->addDuration($this->path, $this->class, $this->currentMethod, $duration)
						->addOutput($this->path, $this->class, $this->currentMethod, ob_get_clean())
					;

					if ($this->codeCoverageIsEnabled() === true)
					{
						$this->score->getCoverage()->addXdebugDataForTest($this, xdebug_get_code_coverage());
						xdebug_stop_code_coverage();
					}

					if ($assertionNumber == $this->score->getAssertionNumber() && $this->methodIsNotVoid($this->currentMethod) === false)
					{
						$this->score->addVoidMethod($this->path, $this->class, $this->currentMethod);
					}
				}
				catch (\exception $exception)
				{
					foreach ($this->executeOnFailure as $closure)
					{
						$closure();
					}

					$this->score->addOutput($this->path, $this->class, $this->currentMethod, ob_get_clean());

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
				$this->score->addRuntimeException($this->path, $this->class, $this->currentMethod, $exception);
			}
			catch (test\exceptions\stop $exception)
			{
			}
			catch (\exception $exception)
			{
				$this->addExceptionToScore($exception);
			}

			$this->afterTestMethod($this->currentMethod);

			$this->currentMethod = null;

			restore_error_handler();

			ini_restore('display_errors');
			ini_restore('log_errors');
			ini_restore('log_errors_max_len');

			if (spl_autoload_unregister($mockAutoloader) === false)
			{
				throw new \runtimeException('Unable to unregister mock autoloader');
			}
		}

		return $this;
	}

	public function run(array $runTestMethods = array(), array $tags = array())
	{
		if ($runTestMethods)
		{
			$this->runTestMethods(array_intersect($runTestMethods, $this->getTestMethods($tags)));
		}

		if ($this->isIgnored() === false)
		{
			$this->callObservers(self::runStart);

			try
			{
				$this->runEngines();
			}
			catch (\exception $exception)
			{
				$this->stopEngines();

				throw $exception;
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

			$this->score->addError($file, $this->class, $this->currentMethod, $line, $errno, $errstr, $errfile, $errline);
		}

		return true;
	}

	public function startCase($case)
	{
		test\adapter::resetCallsForAllInstances();

		$this->score->setCase($case);

		return $this;
	}

	public function stopCase()
	{
		test\adapter::resetCallsForAllInstances();

		$this->score->unsetCase();

		return $this;
	}

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

	public static function setDefaultEngine($defaultEngine)
	{
		self::$defaultEngine = (string) $defaultEngine;
	}

	public static function getDefaultEngine()
	{
		return self::$defaultEngine ?: self::defaultEngine;
	}

	protected function setUp() {}

	protected function beforeTestMethod($testMethod) {}

	protected function afterTestMethod($testMethod) {}

	protected function tearDown() {}

	protected function addExceptionToScore(\exception $exception)
	{
		list($file, $line) = $this->getBacktrace($exception->getTrace());

		$this->score->addException($file, $this->class, $this->currentMethod, $line, $exception);

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

	protected function checkMethod($methodName)
	{
		if (isset($this->testMethods[$methodName]) === false)
		{
			throw new exceptions\logic\invalidArgument('Test method ' . $this->class . '::' . $methodName . '() is unknown');
		}

		return $this;
	}

	private function runEngines()
	{
		$this->callObservers(self::beforeSetUp);
		$this->setUp();
		$this->callObservers(self::afterSetUp);

		while ($this->runEngine()->engines)
		{
			$engines = $this->engines;

			foreach ($engines as $this->currentMethod => $engine)
			{
				$score = $engine->getScore();

				if ($score !== null)
				{
					unset($this->engines[$this->currentMethod]);

					$this->callObservers(self::afterTestMethod);

					switch (true)
					{
						case $score->getRuntimeExceptionNumber() > 0:
							$this->callObservers(self::runtimeException);
							$runtimeExceptions = $score->getRuntimeExceptions();
							throw array_shift($runtimeExceptions);

						case $score->getVoidMethodNumber() > 0:
							$this->callObservers(self::void);
							break;

						case $score->getUncompletedMethodNumber():
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

						default:
							$this->callObservers(self::success);
					}

					$this->score->merge($score);

					if ($engine->isAsynchronous() === true)
					{
						$this->asynchronousEngines--;
					}
				}
			}

			$this->currentMethod = null;
		}

		return $this->doTearDown();
	}

	private function stopEngines()
	{
		while ($this->engines)
		{
			$engines = $this->engines;

			foreach ($engines as $currentMethod => $engine)
			{
				if ($engine->getScore() !== null)
				{
					unset($this->engines[$currentMethod]);
				}
			}
		}

		return $this->doTearDown();
	}

	private function runEngine()
	{
		$this->currentMethod = current($this->runTestMethods);

		$engineName = $engineClass = ($this->getMethodEngine($this->currentMethod) ?: $this->getClassEngine() ?: self::getDefaultEngine());

		if (ltrim($engineClass, '\\') === $engineClass)
		{
			$engineClass = self::enginesNamespace . '\\' . $engineClass;
		}

		if (class_exists($engineClass) === false)
		{
			throw new exceptions\runtime('Test engine \'' . $engineName . '\' does not exist for method \'' . $this->class . '::' . $this->currentMethod . '()\'');
		}

		$engine = $this->factory[$engineClass]($this->factory);

		if ($engine instanceof test\engine === false)
		{
			throw new exceptions\runtime('Test engine \'' . $engineName . '\' is invalid for method \'' . $this->class . '::' . $this->currentMethod . '()\'');
		}

		if ($this->canRunEngine($engine) === true)
		{
			array_shift($this->runTestMethods);

			$this->engines[$this->currentMethod] = $engine->run($this->callObservers(self::beforeTestMethod));

			if ($engine->isAsynchronous() === true)
			{
				$this->asynchronousEngines++;
			}
		}

		$this->currentMethod = null;

		return $this;
	}

	private function canRunEngine(test\engine $engine)
	{
		return ($this->runTestMethods && ($engine->isAsynchronous() === false || ($this->maxAsynchronousEngines === null || $this->asynchronousEngines < $this->maxAsynchronousEngines)));
	}

	private function doTearDown()
	{
		$this->callObservers(self::beforeTearDown);
		$this->tearDown();
		$this->callObservers(self::afterTearDown);

		return $this;
	}

	private static function cleanNamespace($namespace)
	{
		return trim((string) $namespace, '\\');
	}

	private static function isRegex($namespace)
	{
		return preg_match('/^([^\\\[:alnum:][:space:]]).*\1.*$/', $namespace) === 1;
	}
}
