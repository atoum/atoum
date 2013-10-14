<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum\test,
	mageekguy\atoum\mock,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\annotations
;

abstract class test implements observable, \countable
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
	const skipped = 'testSkipped';
	const exception = 'testException';
	const runtimeException = 'testRuntimeException';
	const success = 'testAssertionSuccess';
	const afterTestMethod = 'afterTestMethod';
	const beforeTearDown = 'beforeTestTearDown';
	const afterTearDown = 'afterTestTearDown';
	const runStop = 'testRunStop';
	const defaultEngine = 'concurrent';
	const enginesNamespace = '\mageekguy\atoum\test\engines';

	private $score = null;
	private $locale = null;
	private $adapter = null;
	private $mockGenerator = null;
	private $reflectionMethodFactory = null;
	private $asserterGenerator = null;
	private $assertionManager = null;
	private $phpMocker = null;
	private $testAdapterStorage = null;
	private $mockControllerLinker = null;
	private $phpPath = null;
	private $testedClassName = null;
	private $testedClassPath = null;
	private $currentMethod = null;
	private $testNamespace = null;
	private $classEngine = null;
	private $bootstrapFile = null;
	private $maxAsynchronousEngines = null;
	private $asynchronousEngines = 0;
	private $path = '';
	private $class = '';
	private $classNamespace = '';
	private $observers = array();
	private $tags = array();
	private $phpVersions = array();
	private $mandatoryExtensions = array();
	private $dataProviders = array();
	private $testMethods = array();
	private $runTestMethods = array();
	private $engines = array();
	private $methodEngines = array();
	private $methodsAreNotVoid = array();
	private $executeOnFailure = array();
	private $ignore = false;
	private $debugMode = false;
	private $xdebugConfig = null;
	private $codeCoverage = false;
	private $classHasNotVoidMethods = false;

	private static $namespace = null;
	private static $defaultEngine = self::defaultEngine;

	public function __construct(adapter $adapter = null, annotations\extractor $annotationExtractor = null, asserter\generator $asserterGenerator = null, test\assertion\manager $assertionManager = null, \closure $reflectionClassFactory = null)
	{
		$this
			->setAdapter($adapter)
			->setPhpMocker()
			->setAsserterGenerator($asserterGenerator)
			->setAssertionManager($assertionManager)
			->setTestAdapterStorage()
			->setMockControllerLinker()
			->setScore()
			->setLocale()
			->setMockGenerator()
			->setReflectionMethodFactory()
			->enableCodeCoverage()
		;

		$class = ($reflectionClassFactory ? $reflectionClassFactory($this) : new \reflectionClass($this));

		$this->path = $class->getFilename();
		$this->class = $class->getName();
		$this->classNamespace = $class->getNamespaceName();

		$this->setClassAnnotations($annotationExtractor = $annotationExtractor ?: new annotations\extractor());

		$annotationExtractor->extract($class->getDocComment());

		if ($this->testNamespace === null)
		{
			$this->setParentClassAnnotations($annotationExtractor);

			$parentClass = $class;

			while ($this->testNamespace === null && ($parentClass = $parentClass->getParentClass()) !== false)
			{
				$annotationExtractor->extract($parentClass->getDocComment());
			}
		}

		$this->setMethodAnnotations($annotationExtractor, $methodName);

		foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $publicMethod)
		{
			if (stripos($methodName = $publicMethod->getName(), self::testMethodPrefix) === 0)
			{
				$this->testMethods[$methodName] = array();

				$annotationExtractor->extract($publicMethod->getDocComment());

				if ($publicMethod->getNumberOfParameters() > 0 && isset($this->dataProviders[$methodName]) === false)
				{
					$this->setDataProvider($methodName);
				}
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
		return $this->assertionManager->__get($property);
	}

	public function __call($method, array $arguments)
	{
		return $this->assertionManager->__call($method, $arguments);
	}

	public function setTestAdapterStorage(test\adapter\storage $storage = null)
	{
		$this->testAdapterStorage = $storage ?: new test\adapter\storage();

		return $this;
	}

	public function getTestAdapterStorage()
	{
		return $this->testAdapterStorage;
	}

	public function setMockControllerLinker(mock\controller\linker $linker = null)
	{
		$this->mockControllerLinker = $linker ?: new mock\controller\linker();

		return $this;
	}

	public function getMockControllerLinker()
	{
		return $this->mockControllerLinker;
	}

	public function setScore(test\score $score = null)
	{
		$this->score = $score ?: new test\score();

		return $this;
	}

	public function getScore()
	{
		return $this->score;
	}

	public function setLocale(locale $locale = null)
	{
		$this->locale = $locale ?: new locale();

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setAdapter(adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new adapter();

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setPhpMocker(php\mocker $phpMocker = null)
	{
		$this->phpMocker = $phpMocker ?: new php\mocker();

		return $this;
	}

	public function getPhpMocker()
	{
		return $this->phpMocker;
	}

	public function setMockGenerator(test\mock\generator $generator = null)
	{
		if ($generator !== null)
		{
			$generator->setTest($this);
		}
		else
		{
			$generator = new test\mock\generator($this);
		}

		$this->mockGenerator = $generator;

		return $this;
	}

	public function getMockGenerator()
	{
		return $this->mockGenerator;
	}

	public function setReflectionMethodFactory(\closure $factory = null)
	{
		$this->reflectionMethodFactory = $factory ?: function($class, $method) { return new \reflectionMethod($class, $method); };

		return $this;
	}

	public function setAsserterGenerator(test\asserter\generator $generator = null)
	{
		if ($generator === null)
		{
			$generator = new test\asserter\generator($this);
		}
		else
		{
			$generator->setTest($this);
		}

		$this->asserterGenerator = $generator
			->setAlias('array', 'phpArray')
			->setAlias('in', 'phpArray')
			->setAlias('class', 'phpClass')
			->setAlias('function', 'phpFunction')
		;

		return $this;
	}

	public function getAsserterGenerator()
	{
		$this->testAdapterStorage->resetCalls();

		return $this->asserterGenerator;
	}

	public function setAssertionManager(test\assertion\manager $assertionManager = null)
	{
		$this->assertionManager = $assertionManager ?: new test\assertion\manager();

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
			->setPropertyHandler('function', function() use ($test) { return $test->getPhpMocker(); })
			->setPropertyHandler('exception', function() { return asserters\exception::getLastValue(); })
		;

		$returnTest = function() use ($test) { return $test; };

		$this->assertionManager
			->setHandler('if', $returnTest)
			->setHandler('and', $returnTest)
			->setHandler('then', $returnTest)
			->setHandler('given', $returnTest)
		;

		$returnMockController = function(mock\aggregator $mock) { return $mock->getMockController(); };

		$this->assertionManager
			->setHandler('calling', $returnMockController)
			->setHandler('ƒ', $returnMockController)
			->setHandler('resetMock', function(mock\aggregator $mock) { return $mock->getMockController()->resetCalls(); })
		;

		$this->assertionManager
			->setHandler('resetAdapter', function(test\adapter $adapter) { return $adapter->resetCalls(); })
		;

		$asserterGenerator = $this->asserterGenerator;

		$this->assertionManager
			->setHandler('define', function() use ($asserterGenerator) { return $asserterGenerator; })
			->setDefaultHandler(function($asserter, $arguments) use ($asserterGenerator) { return $asserterGenerator->getAsserterInstance($asserter, $arguments); })
		;

		return $this;
	}

	public function addClassPhpVersion($version, $operator = null)
	{
		$this->phpVersions[$version] = $operator ?: '>=';

		return $this;
	}

	public function getClassPhpVersions()
	{
		return $this->phpVersions;
	}

	public function addMandatoryClassExtension($extension)
	{
		$this->mandatoryExtensions[] = $extension;

		return $this;
	}

	public function addMethodPhpVersion($testMethodName, $version, $operator = null)
	{
		$this->checkMethod($testMethodName)->testMethods[$testMethodName]['php'][$version] = $operator ?: '>=';

		return $this;
	}

	public function getMethodPhpVersions($testMethodName = null)
	{
		$versions = array();

		$classVersions = $this->getClassPhpVersions();

		if ($testMethodName === null)
		{
			foreach ($this->testMethods as $testMethodName => $annotations)
			{
				if (isset($annotations['php']) === false)
				{
					$versions[$testMethodName] = $classVersions;
				}
				else
				{
					$versions[$testMethodName] = array_merge($classVersions, $annotations['php']);
				}
			}
		}
		else
		{
			if (isset($this->checkMethod($testMethodName)->testMethods[$testMethodName]['php']) === false)
			{
				$versions = $classVersions;
			}
			else
			{
				$versions = array_merge($classVersions, $this->testMethods[$testMethodName]['php']);
			}
		}

		return $versions;
	}

	public function getMandatoryClassExtensions()
	{
		return $this->mandatoryExtensions;
	}

	public function addMandatoryMethodExtension($testMethodName, $extension)
	{
		$this->checkMethod($testMethodName)->testMethods[$testMethodName]['mandatoryExtensions'][] = $extension;

		return $this;
	}

	public function getMandatoryMethodExtensions($testMethodName = null)
	{
		$extensions = array();

		$mandatoryClassExtensions = $this->getMandatoryClassExtensions();

		if ($testMethodName === null)
		{
			foreach ($this->testMethods as $testMethodName => $annotations)
			{
				if (isset($annotations['mandatoryExtensions']) === false)
				{
					$extensions[$testMethodName] = $mandatoryClassExtensions;
				}
				else
				{
					$extensions[$testMethodName] = array_merge($mandatoryClassExtensions, $annotations['mandatoryExtensions']);
				}
			}
		}
		else
		{
			if (isset($this->checkMethod($testMethodName)->testMethods[$testMethodName]['mandatoryExtensions']) === false)
			{
				$extensions = $mandatoryClassExtensions;
			}
			else
			{
				$extensions = array_merge($mandatoryClassExtensions, $this->testMethods[$testMethodName]['mandatoryExtensions']);
			}
		}

		return $extensions;
	}

	public function skip($message)
	{
		throw new test\exceptions\skip($message);
	}

	public function getAssertionManager()
	{
		return $this->assertionManager;
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

	public function setXdebugConfig($value)
	{
		$this->xdebugConfig = $value;

		return $this;
	}

	public function getXdebugConfig()
	{
		return $this->xdebugConfig;
	}

	public function executeOnFailure(\closure $closure)
	{
		$this->executeOnFailure[] = $closure;

		return $this;
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

	public function setBootstrapFile($path)
	{
		$this->bootstrapFile = $path;

		return $this;
	}

	public function getBootstrapFile()
	{
		return $this->bootstrapFile;
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
		$this->checkMethod($testMethodName)->testMethods[$testMethodName]['tags'] = $tags;

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
			$tags = isset($this->checkMethod($testMethodName)->testMethods[$testMethodName]['tags']) === false ? $classTags : $this->testMethods[$testMethodName]['tags'];
		}

		return $tags;
	}

	public function getDataProviders()
	{
		return $this->dataProviders;
	}

	public function getTestedClassName()
	{
		if ($this->testedClassName === null)
		{
			$this->testedClassName = self::getTestedClassNameFromTestClass($this->getClass(), $this->getTestNamespace());
		}

		return $this->testedClassName;
	}

	public function getTestedClassNamespace()
	{
		$testedClassName = $this->getTestedClassName();

		return substr($testedClassName, 0, strrpos($testedClassName, '\\'));
	}

	public function getTestedClassPath()
	{
		if ($this->testedClassPath === null)
		{
			$testedClass = new \reflectionClass($this->getTestedClassName());

			$this->testedClassPath = $testedClass->getFilename();
		}

		return $this->testedClassPath;
	}

	public function setTestedClassName($className)
	{
		if ($this->testedClassName !== null)
		{
			throw new exceptions\runtime('Tested class name is already defined');
		}

		$this->testedClassName = $className;

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
		return sizeof($this->runTestMethods);
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
		return (sizeof($this) <= 0 || $this->ignore === true);
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

			$this->phpMocker->setDefaultNamespace($this->getTestedClassNamespace());

			try
			{
				foreach ($this->getMethodPhpVersions($testMethod) as $phpVersion => $operator)
				{
					if (version_compare(phpversion(), $phpVersion, $operator) === false)
					{
						throw new test\exceptions\skip('PHP version ' . PHP_VERSION . ' is not ' . $operator . ' to ' . $phpVersion);
					}
				}

				foreach ($this->getMandatoryMethodExtensions($testMethod) as $mandatoryExtension)
				{
					$this->extension($mandatoryExtension)->isLoaded();
				}

				try
				{
					ob_start();

					if ($this->adapter->class_exists($testedClassName = $this->getTestedClassName()) === false)
					{
						throw new exceptions\runtime('Tested class \'' . $testedClassName . '\' does not exist for test class \'' . $this->getClass() . '\'');
					}

					test\adapter::setStorage($this->testAdapterStorage);
					mock\controller::setLinker($this->mockControllerLinker);

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

						$reflectedTestMethod = call_user_func($this->reflectionMethodFactory, $this, $testMethod);
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

					$this->mockControllerLinker->reset();
					$this->testAdapterStorage->reset();

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
					$this->score->addOutput($this->path, $this->class, $this->currentMethod, ob_get_clean());

					throw $exception;
				}
			}
			catch (asserter\exception $exception)
			{
				foreach ($this->executeOnFailure as $closure)
				{
					ob_start();
					$closure();
					$this->score->addOutput($this->path, $this->class, $this->currentMethod, ob_get_clean());
				}

				if ($this->score->failExists($exception) === false)
				{
					$this->addExceptionToScore($exception);
				}
			}
			catch (test\exceptions\runtime $exception)
			{
				$this->score->addRuntimeException($this->path, $this->class, $this->currentMethod, $exception);
			}
			catch (test\exceptions\skip $exception)
			{
				list($file, $line) = $this->getBacktrace($exception->getTrace());

				$this->score->addSkippedMethod($file, $this->class, $this->currentMethod, $line, $exception->getMessage());
			}
			catch (test\exceptions\stop $exception)
			{
			}
			catch (exception $exception)
			{
				list($file, $line) = $this->getBacktrace($exception->getTrace());

				$this->errorHandler(E_USER_ERROR, $exception->getMessage(), $file, $line);
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

	public function startCase($case)
	{
		$this->testAdapterStorage->resetCalls();
		$this->score->setCase($case);

		return $this;
	}

	public function stopCase()
	{
		$this->testAdapterStorage->resetCalls();
		$this->score->unsetCase();

		return $this;
	}

	public function setDataProvider($testMethodName, $dataProvider = null)
	{
		if ($dataProvider === null)
		{
			$dataProvider = $testMethodName . 'DataProvider';
		}

		if (method_exists($this->checkMethod($testMethodName), $dataProvider) === false)
		{
			throw new exceptions\logic\invalidArgument('Data provider ' . $this->class . '::' . lcfirst($dataProvider) . '() is unknown');
		}

		$this->dataProviders[$testMethodName] = $dataProvider;

		return $this;
	}

	public function errorHandler($errno, $errstr, $errfile, $errline)
	{
		$doNotCallDefaultErrorHandler = true;
		$errorReporting = $this->adapter->error_reporting();

		if ($errorReporting !== 0 && $errorReporting & $errno)
		{
			list($file, $line) = $this->getBacktrace();

			$this->score->addError($file ?: ($errfile ?: $this->path), $this->class, $this->currentMethod, $line ?: $errline, $errno, trim($errstr), $errfile, $errline);

			$doNotCallDefaultErrorHandler = !($errno & E_RECOVERABLE_ERROR);
		}

		return $doNotCallDefaultErrorHandler;

	}

	public function setUp() {}

	public function beforeTestMethod($testMethod) {}

	public function afterTestMethod($testMethod) {}

	public function tearDown() {}

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

	public static function getTestedClassNameFromTestClass($fullyQualifiedClassName, $testNamespace = null)
	{
		if ($testNamespace === null)
		{
			$testNamespace = self::getNamespace();
		}

		if (self::isRegex($testNamespace) === true)
		{
			if (preg_match($testNamespace, $fullyQualifiedClassName) === 0)
			{
				throw new exceptions\runtime('Test class \'' . $fullyQualifiedClassName . '\' is not in a namespace which match pattern \'' . $testNamespace . '\'');
			}

			$testedClassName = preg_replace($testNamespace, '\\', $fullyQualifiedClassName);
		}
		else
		{
			$position = strpos($fullyQualifiedClassName, $testNamespace);

			if ($position === false)
			{
				throw new exceptions\runtime('Test class \'' . $fullyQualifiedClassName . '\' is not in a namespace which contains \'' . $testNamespace . '\'');
			}

			$testedClassName = substr($fullyQualifiedClassName, 0, $position) . substr($fullyQualifiedClassName, $position + 1 + strlen($testNamespace));
		}

		return trim($testedClassName, '\\');
	}

	protected function setClassAnnotations(annotations\extractor $extractor)
	{
		$test = $this;

		$extractor
			->resetHandlers()
			->setHandler('ignore', function($value) use ($test) { $test->ignore(annotations\extractor::toBoolean($value)); })
			->setHandler('tags', function($value) use ($test) { $test->setTags(annotations\extractor::toArray($value)); })
			->setHandler('namespace', function($value) use ($test) { $test->setTestNamespace($value); })
			->setHandler('maxChildrenNumber', function($value) use ($test) { $test->setMaxChildrenNumber($value); })
			->setHandler('engine', function($value) use ($test) { $test->setClassEngine($value); })
			->setHandler('hasVoidMethods', function($value) use ($test) { $test->classHasVoidMethods(); })
			->setHandler('hasNotVoidMethods', function($value) use ($test) { $test->classHasNotVoidMethods(); })
			->setHandler('php', function($value) use ($test) {
					$value = annotations\extractor::toArray($value);

					if (isset($value[0]) === true)
					{
						$operator = null;

						if (isset($value[1]) === false)
						{
							$version = $value[0];
						}
						else
						{
							$version = $value[1];

							switch ($value[0])
							{
								case '<':
								case '<=':
								case '=':
								case '==':
								case '>=':
								case '>':
									$operator = $value[0];
							}
						}

						$test->addClassPhpVersion($version, $operator);
					}
				}
			)
			->setHandler('extensions', function($value) use ($test) {
					foreach (annotations\extractor::toArray($value) as $mandatoryExtension)
					{
						$test->addMandatoryClassExtension($mandatoryExtension);
					}
				}
			)
		;

		return $this;
	}

	protected function setParentClassAnnotations(annotations\extractor $extractor)
	{
		$extractor
			->unsetHandler('ignore')
			->unsetHandler('tags')
			->unsetHandler('maxChildrenNumber')
		;

		return $this;
	}

	protected function setMethodAnnotations(annotations\extractor $extractor, & $methodName)
	{
		$test = $this;

		$extractor
			->resetHandlers()
			->setHandler('ignore', function($value) use ($test, & $methodName) { $test->ignoreMethod($methodName, annotations\extractor::toBoolean($value)); })
			->setHandler('tags', function($value) use ($test, & $methodName) { $test->setMethodTags($methodName, annotations\extractor::toArray($value)); })
			->setHandler('dataProvider', function($value) use ($test, & $methodName) { $test->setDataProvider($methodName, $value === true ? null : $value); })
			->setHandler('engine', function($value) use ($test, & $methodName) { $test->setMethodEngine($methodName, $value); })
			->setHandler('isVoid', function($value) use ($test, & $methodName) { $test->setMethodVoid($methodName); })
			->setHandler('isNotVoid', function($value) use ($test, & $methodName) { $test->setMethodNotVoid($methodName); })
			->setHandler('php', function($value) use ($test, & $methodName) {
					$value = annotations\extractor::toArray($value);

					if (isset($value[0]) === true)
					{
						$operator = null;

						if (isset($value[1]) === false)
						{
							$version = $value[0];
						}
						else
						{
							$version = $value[1];

							switch ($value[0])
							{
								case '<':
								case '<=':
								case '=':
								case '==':
								case '>=':
								case '>':
									$operator = $value[0];
							}
						}

						$test->addMethodPhpVersion($methodName, $version, $operator);
					}
				}
			)
			->setHandler('extensions', function($value) use ($test, & $methodName) {
					foreach (annotations\extractor::toArray($value) as $mandatoryExtension)
					{
						$test->addMandatoryMethodExtension($methodName, $mandatoryExtension);
					}
				}
			)
		;

		return $this;
	}

	protected function getBacktrace(array $trace = null)
	{
		$debugBacktrace = $trace === null ? debug_backtrace(false) : $trace;

		foreach ($debugBacktrace as $key => $value)
		{
			if (isset($value['class']) === true && $value['class'] === $this->class && isset($value['function']) === true && $value['function'] === $this->currentMethod)
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

	private function checkMethod($methodName)
	{
		if (isset($this->testMethods[$methodName]) === false)
		{
			throw new exceptions\logic\invalidArgument('Test method ' . $this->class . '::' . $methodName . '() does not exist');
		}

		return $this;
	}

	private function runTestMethods(array $methods)
	{
		$this->runTestMethods = $methods;

		return $this;
	}

	private function addExceptionToScore(\exception $exception)
	{
		list($file, $line) = $this->getBacktrace($exception->getTrace());

		$this->score->addException($file, $this->class, $this->currentMethod, $line, $exception);

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

					$this->score->merge($score);

					$this->callObservers(self::afterTestMethod);

					switch (true)
					{
						case $score->getRuntimeExceptionNumber():
							$this->callObservers(self::runtimeException);
							$runtimeExceptions = $score->getRuntimeExceptions();
							throw array_shift($runtimeExceptions);

						case $score->getVoidMethodNumber():
							$this->callObservers(self::void);
							break;

						case $score->getUncompletedMethodNumber():
							$this->callObservers(self::uncompleted);
							break;

						case $score->getSkippedMethodNumber():
							$this->callObservers(self::skipped);
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
		$this->currentMethod = current($this->runTestMethods) ?: null;

		if ($this->currentMethod !== null)
		{
			if ($this->xdebugConfig != null)
			{
				$engineClass = 'mageekguy\atoum\test\engines\concurrent';
			}
			else
			{
				$engineName = $engineClass = ($this->getMethodEngine($this->currentMethod) ?: $this->getClassEngine() ?: self::getDefaultEngine());

				if (substr($engineClass, 0, 1) !== '\\')
				{
					$engineClass = self::enginesNamespace . '\\' . $engineClass;
				}

				if (class_exists($engineClass) === false)
				{
					throw new exceptions\runtime('Test engine \'' . $engineName . '\' does not exist for method \'' . $this->class . '::' . $this->currentMethod . '()\'');
				}
			}

			$engine = new $engineClass();

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
		}

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
