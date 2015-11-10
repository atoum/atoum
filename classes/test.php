<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\mock,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\annotations,
	mageekguy\atoum\tools\variable\analyzer
;

abstract class test implements observable, \countable
{
	const testMethodPrefix = 'test';
	const defaultNamespace = '#(?:^|\\\)tests?\\\units?\\\#i';
	const defaultMethodPrefix = '#^(?:test|_*[^_]+_should_)#i';
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
	private $mockAutoloader = null;
	private $factoryBuilder = null;
	private $reflectionMethodFactory = null;
	private $phpExtensionFactory;
	private $asserterGenerator = null;
	private $assertionManager = null;
	private $phpFunctionMocker = null;
	private $phpConstantMocker = null;
	private $testAdapterStorage = null;
	private $asserterCallManager = null;
	private $mockControllerLinker = null;
	private $phpPath = null;
	private $testedClassName = null;
	private $testedClassPath = null;
	private $currentMethod = null;
	private $testNamespace = null;
	private $testMethodPrefix = null;
	private $classEngine = null;
	private $bootstrapFile = null;
	private $maxAsynchronousEngines = null;
	private $asynchronousEngines = 0;
	private $path = '';
	private $class = '';
	private $classNamespace = '';
	private $observers = null;
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
	private $branchesAndPathsCoverage = false;
	private $classHasNotVoidMethods = false;
	private $extensions = null;
	private $analyzer;

	private static $namespace = null;
	private static $methodPrefix = null;
	private static $defaultEngine = self::defaultEngine;

	public function __construct(adapter $adapter = null, annotations\extractor $annotationExtractor = null, asserter\generator $asserterGenerator = null, test\assertion\manager $assertionManager = null, \closure $reflectionClassFactory = null, \closure $phpExtensionFactory = null, analyzer $analyzer = null)
	{
		$this
			->setAdapter($adapter)
			->setPhpFunctionMocker()
			->setPhpConstantMocker()
			->setMockGenerator()
			->setMockAutoloader()
			->setAsserterGenerator($asserterGenerator)
			->setAssertionManager($assertionManager)
			->setTestAdapterStorage()
			->setMockControllerLinker()
			->setScore()
			->setLocale()
			->setFactoryBuilder()
			->setReflectionMethodFactory()
			->setAsserterCallManager()
			->enableCodeCoverage()
			->setPhpExtensionFactory($phpExtensionFactory)
			->setAnalyzer($analyzer)
		;

		$this->observers = new \splObjectStorage();
		$this->extensions = new \splObjectStorage();

		$class = ($reflectionClassFactory ? $reflectionClassFactory($this) : new \reflectionClass($this));

		$this->path = $class->getFilename();
		$this->class = $class->getName();
		$this->classNamespace = $class->getNamespaceName();

		if ($annotationExtractor === null)
		{
			$annotationExtractor = new annotations\extractor();
		}

		$this->setClassAnnotations($annotationExtractor);

		$annotationExtractor->extract($class->getDocComment());

		if ($this->testNamespace === null || $this->testMethodPrefix === null)
		{
			$annotationExtractor
				->unsetHandler('ignore')
				->unsetHandler('tags')
				->unsetHandler('maxChildrenNumber')
			;

			$parentClass = $class;

			while (($this->testNamespace === null || $this->testMethodPrefix === null) && ($parentClass = $parentClass->getParentClass()) !== false)
			{
				$annotationExtractor->extract($parentClass->getDocComment());

				if ($this->testNamespace !== null)
				{
					$annotationExtractor->unsetHandler('namespace');
				}

				if ($this->testMethodPrefix !== null)
				{
					$annotationExtractor->unsetHandler('methodPrefix');
				}
			}
		}

		$this->setMethodAnnotations($annotationExtractor, $methodName);

		$testMethodPrefix = $this->getTestMethodPrefix();

		if ($this->analyzer->isRegex($testMethodPrefix) === false)
		{
			$testMethodFilter = function($methodName) use ($testMethodPrefix) { return (stripos($methodName, $testMethodPrefix) === 0); };
		}
		else
		{
			$testMethodFilter = function($methodName) use ($testMethodPrefix) { return (preg_match($testMethodPrefix, $methodName) == true); };
		}

		foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $publicMethod)
		{
			$methodName = $publicMethod->getName();

			if ($testMethodFilter($methodName) == true)
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

	public function __set($property, $handler)
	{
		$this->assertionManager->{$property} = $handler;

		return $this;
	}

	public function __call($method, array $arguments)
	{
		return $this->assertionManager->__call($method, $arguments);
	}

	public function setAnalyzer(analyzer $analyzer = null)
	{
		$this->analyzer = $analyzer ?: new analyzer();

		return $this;
	}

	public function getAnalyzer()
	{
		return $this->analyzer;
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
		$phpMocker = $phpMocker ?: new php\mocker();

		$phpMocker->addToTest($this);

		return $this;
	}

	public function setPhpFunctionMocker(php\mocker\funktion $phpFunctionMocker = null)
	{
		$this->phpFunctionMocker = $phpFunctionMocker ?: new php\mocker\funktion();

		return $this;
	}

	public function getPhpFunctionMocker()
	{
		return $this->phpFunctionMocker;
	}

	public function setPhpConstantMocker(php\mocker\constant $phpConstantMocker = null)
	{
		$this->phpConstantMocker = $phpConstantMocker ?: new php\mocker\constant();

		return $this;
	}

	public function getPhpConstantMocker()
	{
		return $this->phpConstantMocker;
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

	public function setMockAutoloader(atoum\autoloader\mock $autoloader = null)
	{
		$this->mockAutoloader = $autoloader ?: new atoum\autoloader\mock();

		return $this;
	}

	public function getMockAutoloader()
	{
		return $this->mockAutoloader;
	}

	public function setFactoryBuilder(factory\builder $factoryBuilder = null)
	{
		$this->factoryBuilder = $factoryBuilder ?: new factory\builder\closure();

		return $this;
	}

	public function getFactoryBuilder()
	{
		return $this->factoryBuilder;
	}

	public function setReflectionMethodFactory(\closure $factory = null)
	{
		$this->reflectionMethodFactory = $factory ?: function($class, $method) { return new \reflectionMethod($class, $method); };

		return $this;
	}

	public function setPhpExtensionFactory(\closure $factory = null)
	{
		$this->phpExtensionFactory = $factory ?: function($extensionName) {
			return new atoum\php\extension($extensionName);
		};

		return $this;
	}

	public function setAsserterGenerator(test\asserter\generator $generator = null)
	{
		if ($generator !== null)
		{
			$generator->setTest($this);
		}
		else
		{
			$generator = new test\asserter\generator($this);
		}

		$this->asserterGenerator = $generator->setTest($this);

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
			->setHandler('when', function($mixed) use ($test) { if ($mixed instanceof \closure) { $mixed($test); } return $test; })
			->setHandler('assert', function($case = null) use ($test) { $test->stopCase(); if ($case !== null) { $test->startCase($case); } return $test; })
			->setHandler('mockGenerator', function() use ($test) { return $test->getMockGenerator(); })
			->setHandler('mockClass', function($class, $mockNamespace = null, $mockClass = null) use ($test) { $test->getMockGenerator()->generate($class, $mockNamespace, $mockClass); return $test; })
			->setHandler('mockTestedClass', function($mockNamespace = null, $mockClass = null) use ($test) { $test->getMockGenerator()->generate($test->getTestedClassName(), $mockNamespace, $mockClass); return $test; })
			->setHandler('dump', function() use ($test) { if ($test->debugModeIsEnabled() === true) { call_user_func_array('var_dump', func_get_args()); } return $test; })
			->setHandler('stop', function() use ($test) { if ($test->debugModeIsEnabled() === true) { throw new test\exceptions\stop(); } return $test; })
			->setHandler('executeOnFailure', function($callback) use ($test) { if ($test->debugModeIsEnabled() === true) { $test->executeOnFailure($callback); } return $test; })
			->setHandler('dumpOnFailure', function($variable) use ($test) { if ($test->debugModeIsEnabled() === true) { $test->executeOnFailure(function() use ($variable) { var_dump($variable); }); } return $test; })
			->setPropertyHandler('function', function() use ($test) { return $test->getPhpFunctionMocker(); })
			->setPropertyHandler('constant', function() use ($test) { return $test->getPhpConstantMocker(); })
			->setPropertyHandler('exception', function() { return asserters\exception::getLastValue(); })
		;

		$mockGenerator = $this->mockGenerator;

		$this->assertionManager
			->setPropertyHandler('nextMockedMethod', function() use ($mockGenerator) { return $mockGenerator->getMethod(); })
		;

		$returnTest = function() use ($test) { return $test; };

		$this->assertionManager
			->setHandler('if', $returnTest)
			->setHandler('and', $returnTest)
			->setHandler('then', $returnTest)
			->setHandler('given', $returnTest)
			->setMethodHandler('define', $returnTest)
			->setMethodHandler('let', $returnTest)
		;

		$returnMockController = function(mock\aggregator $mock) { return $mock->getMockController(); };

		$this->assertionManager
			->setHandler('calling', $returnMockController)
			->setHandler('ƒ', $returnMockController)

		;

		$this->assertionManager
			->setHandler('resetMock', function(mock\aggregator $mock) { return $mock->getMockController()->resetCalls(); })
			->setHandler('resetAdapter', function(test\adapter $adapter) { return $adapter->resetCalls(); })
		;

		$phpFunctionMocker = $this->phpFunctionMocker;

		$this->assertionManager->setHandler('resetFunction', function(test\adapter\invoker $invoker) use ($phpFunctionMocker) { $phpFunctionMocker->resetCalls($invoker->getFunction()); return $invoker; });

		$assertionAliaser = $this->assertionManager->getAliaser();

		$this->assertionManager
			->setPropertyHandler('define', function() use ($assertionAliaser, $test) { return $assertionAliaser; })
			->setHandler('from', function($class) use ($assertionAliaser, $test) { $assertionAliaser->from($class); return $test; })
			->setHandler('use', function($target) use ($assertionAliaser, $test) { $assertionAliaser->alias($target); return $test; })
			->setHandler('as', function($alias) use ($assertionAliaser, $test) { $assertionAliaser->to($alias); return $test; })
		;

		$asserterGenerator = $this->asserterGenerator;

		$this->assertionManager->setDefaultHandler(function($keyword, $arguments) use ($asserterGenerator, $assertionAliaser, & $lastAsserter) {
				static $lastAsserter = null;

				if ($lastAsserter !== null)
				{
					$realKeyword = $assertionAliaser->resolveAlias($keyword, get_class($lastAsserter));

					if ($realKeyword !== $keyword)
					{
						return call_user_func_array(array($lastAsserter, $realKeyword), $arguments);
					}
				}

				return ($lastAsserter = $asserterGenerator->getAsserterInstance($keyword, $arguments));
			}
		);

		$this->assertionManager
			->use('phpArray')->as('array')
			->use('phpArray')->as('in')
			->use('phpClass')->as('class')
			->use('phpFunction')->as('function')
			->use('phpFloat')->as('float')
			->use('phpString')->as('string')
			->use('calling')->as('method')
		;

		return $this;
	}

	public function getAsserterCallManager()
	{
		return $this->asserterCallManager;
	}

	public function setAsserterCallManager(asserters\adapter\call\manager $asserterCallManager = null)
	{
		$this->asserterCallManager = $asserterCallManager ?: new asserters\adapter\call\manager();

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

	public function branchesAndPathsCoverageIsEnabled()
	{
		return $this->branchesAndPathsCoverage;
	}

	public function enableBranchesAndPathsCoverage()
	{
		$this->branchesAndPathsCoverage = $this->codeCoverageIsEnabled() && defined('XDEBUG_CC_BRANCH_CHECK');

		return $this;
	}

	public function disableBranchesAndPathsCoverage()
	{
		$this->branchesAndPathsCoverage = false;

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
		$testNamespace = self::cleanNamespace($testNamespace);

		if ($testNamespace === '')
		{
			throw new exceptions\logic\invalidArgument('Test namespace must not be empty');
		}

		if (!$this->analyzer->isRegex($testNamespace) && !$this->analyzer->isValidNamespace($testNamespace))
		{
			throw new exceptions\logic\invalidArgument('Test namespace must be a valid regex or identifier');
		}

		$this->testNamespace = $testNamespace;

		return $this;
	}

	public function getTestNamespace()
	{
		return $this->testNamespace !== null ? $this->testNamespace : self::getNamespace();
	}

	public function setTestMethodPrefix($methodPrefix)
	{
		$methodPrefix = (string) $methodPrefix;

		if ($methodPrefix == '')
		{
			throw new exceptions\logic\invalidArgument('Test method prefix must not be empty');
		}

		if (!$this->analyzer->isRegex($methodPrefix) && !$this->analyzer->isValidIdentifier($methodPrefix))
		{
			throw new exceptions\logic\invalidArgument('Test method prefix must a valid regex or identifier');
		}

		$this->testMethodPrefix = $methodPrefix;

		return $this;
	}

	public function getTestMethodPrefix()
	{
		return $this->testMethodPrefix !== null ? $this->testMethodPrefix : self::getMethodPrefix();
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
			$this->testedClassName = self::getTestedClassNameFromTestClass($this->getClass(), $this->getTestNamespace(), $this->getAnalyzer());
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
		$this->observers->attach($observer);

		return $this;
	}

	public function removeObserver(atoum\observer $observer)
	{
		$this->observers->detach($observer);

		return $this;
	}

	public function getObservers()
	{
		return iterator_to_array($this->observers);
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

	public function isIgnored(array $namespaces = array(), array $tags = array())
	{
		$isIgnored = (sizeof($this) <= 0 || $this->ignore === true);

		if ($isIgnored === false && sizeof($namespaces) > 0)
		{
			$classNamespace = strtolower($this->getClassNamespace());

			$isIgnored = sizeof(array_filter($namespaces, function($value) use ($classNamespace) { return strpos($classNamespace, strtolower($value)) === 0; })) <= 0;
		}

		if ($isIgnored === false && sizeof($tags) > 0)
		{
			$isIgnored = sizeof($testTags = $this->getAllTags()) <= 0 || sizeof(array_intersect($tags, $testTags)) == 0;
		}

		return $isIgnored;
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

	public function runTestMethods(array $methods, array $tags = array())
	{
		$this->runTestMethods = $runTestMethods = array();

		if (isset($methods['*']) === true)
		{
			$runTestMethods = $methods['*'];
		}

		$testClass = $this->getClass();

		if (isset($methods[$testClass]) === true)
		{
			$runTestMethods = $methods[$testClass];
		}

		if (in_array('*', $runTestMethods) === true)
		{
			$runTestMethods = array();
		}

		if (sizeof($runTestMethods) <= 0)
		{
			$runTestMethods = $this->getTestMethods($tags);
		}
		else
		{
			$runTestMethods = $this->getTaggedTestMethods($runTestMethods, $tags);
		}

		foreach ($runTestMethods as $method)
		{
			if ($this->xdebugConfig != null)
			{
				$engineClass = 'mageekguy\atoum\test\engines\concurrent';
			}
			else
			{
				$engineName = $engineClass = ($this->getMethodEngine($method) ?: $this->getClassEngine() ?: self::getDefaultEngine());

				if (substr($engineClass, 0, 1) !== '\\')
				{
					$engineClass = self::enginesNamespace . '\\' . $engineClass;
				}

				if (class_exists($engineClass) === false)
				{
					throw new exceptions\runtime('Test engine \'' . $engineName . '\' does not exist for method \'' . $this->class . '::' . $method . '()\'');
				}
			}

			$engine = new $engineClass();

			if ($engine instanceof test\engine === false)
			{
				throw new exceptions\runtime('Test engine \'' . $engineName . '\' is invalid for method \'' . $this->class . '::' . $method . '()\'');
			}

			$this->runTestMethods[$method] = $engine;
		}

		return $this;
	}

	public function runTestMethod($testMethod, array $tags = array())
	{
		if ($this->methodIsIgnored($testMethod, $tags) === false)
		{
			$this->mockAutoloader->setMockGenerator($this->mockGenerator)->register();

			set_error_handler(array($this, 'errorHandler'));

			ini_set('display_errors', 'stderr');
			ini_set('log_errors', 'Off');
			ini_set('log_errors_max_len', '0');

			$this->currentMethod = $testMethod;
			$this->executeOnFailure = array();

			$this->phpFunctionMocker->setDefaultNamespace($this->getTestedClassNamespace());
			$this->phpConstantMocker->setDefaultNamespace($this->getTestedClassNamespace());

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
					try
					{
						call_user_func($this->phpExtensionFactory, $mandatoryExtension)->requireExtension();
					}
					catch (atoum\php\exception $exception)
					{
						throw new test\exceptions\skip($exception->getMessage());
					}
				}

				try
				{
					ob_start();

					test\adapter::setStorage($this->testAdapterStorage);
					mock\controller::setLinker($this->mockControllerLinker);

					$this->testAdapterStorage->add(php\mocker::getAdapter());

					$this->beforeTestMethod($this->currentMethod);

					$this->mockGenerator->testedClassIs($this->getTestedClassName());

					try
					{
						$testedClass = new \reflectionClass($testedClassName = $this->getTestedClassName());
					}
					catch (\exception $exception)
					{
						throw new exceptions\runtime('Tested class \'' . $testedClassName . '\' does not exist for test class \'' . $this->getClass() . '\'');
					}

					if ($testedClass->isAbstract() === true)
					{
						$testedClass = new \reflectionClass($testedClassName = $this->mockGenerator->getDefaultNamespace() . '\\' . $testedClassName);
					}

					$this->factoryBuilder->build($testedClass, $instance)
						->addToAssertionManager($this->assertionManager, 'newTestedInstance', function() use ($testedClass) {
								throw new exceptions\runtime('Tested class ' . $testedClass->getName() . ' has no constructor or its constructor has at least one mandatory argument');
							}
						)
					;

					$this->factoryBuilder->build($testedClass)
						->addToAssertionManager($this->assertionManager, 'newInstance', function() use ($testedClass) {
								throw new exceptions\runtime('Tested class ' . $testedClass->getName() . ' has no constructor or its constructor has at least one mandatory argument');
							}
						)
					;

					$this->assertionManager->setPropertyHandler('testedInstance', function() use (& $instance) {
							if ($instance === null)
							{
								throw new exceptions\runtime('Use $this->newTestedInstance before using $this->testedInstance');
							}

							return $instance;
						}
					);

					if ($this->codeCoverageIsEnabled() === true)
					{
						$options = XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE;

						if ($this->branchesAndPathsCoverageIsEnabled() === true)
						{
							$options |= XDEBUG_CC_BRANCH_CHECK;
						}

						xdebug_start_code_coverage($options);
					}

					$assertionNumber = $this->score->getAssertionNumber();
					$time = microtime(true);
					$memory = memory_get_usage(true);

					if (isset($this->dataProviders[$testMethod]) === false)
					{
						$this->{$testMethod}();

						$this->asserterCallManager->check();
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

							$this->asserterCallManager->check();

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

			$this->mockAutoloader->unregister();
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
		$namespace = self::cleanNamespace($namespace);

		if ($namespace === '')
		{
			throw new exceptions\logic\invalidArgument('Namespace must not be empty');
		}

		self::$namespace = $namespace;
	}

	public static function getNamespace()
	{
		return self::$namespace ?: static::defaultNamespace;
	}

	public static function setMethodPrefix($methodPrefix)
	{
		if ($methodPrefix == '')
		{
			throw new exceptions\logic\invalidArgument('Method prefix must not be empty');
		}

		self::$methodPrefix = $methodPrefix;
	}

	public static function getMethodPrefix()
	{
		return self::$methodPrefix ?: static::defaultMethodPrefix;
	}

	public static function setDefaultEngine($defaultEngine)
	{
		self::$defaultEngine = (string) $defaultEngine;
	}

	public static function getDefaultEngine()
	{
		return self::$defaultEngine ?: self::defaultEngine;
	}

	public static function getTestedClassNameFromTestClass($fullyQualifiedClassName, $testNamespace = null, analyzer $analyzer = null)
	{
		$analyzer = $analyzer ?: new analyzer();

		if ($testNamespace === null)
		{
			$testNamespace = self::getNamespace();
		}

		if ($analyzer->isRegex($testNamespace) === true)
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
			->setHandler('namespace', function($value) use ($test) { $test->setTestNamespace($value === true ? static::defaultNamespace : $value); })
			->setHandler('methodPrefix', function($value) use ($test) { $test->setTestMethodPrefix($value === true ? static::defaultMethodPrefix : $value); })
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

					$this
						->callObservers(self::afterTestMethod)
						->score
							->merge($score)
					;

					$runtimeExceptions = $score->getRuntimeExceptions();

					if (sizeof($runtimeExceptions) > 0)
					{
						$this->callObservers(self::runtimeException);

						throw reset($runtimeExceptions);
					}
					else
					{
						switch (true)
						{
							case $score->getVoidMethodNumber():
								$signal = self::void;
								break;

							case $score->getUncompletedMethodNumber():
								$signal = self::uncompleted;
								break;

							case $score->getSkippedMethodNumber():
								$signal = self::skipped;
								break;

							case $score->getFailNumber():
								$signal = self::fail;
								break;

							case $score->getErrorNumber():
								$signal = self::error;
								break;

							case $score->getExceptionNumber():
								$signal = self::exception;
								break;

							default:
								$signal = self::success;
						}

						$this->callObservers($signal);
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
		$engine = reset($this->runTestMethods);

		if ($engine !== false)
		{
			$this->currentMethod = key($this->runTestMethods);

			if ($this->canRunEngine($engine) === true)
			{
				unset($this->runTestMethods[$this->currentMethod]);

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
		return ($engine->isAsynchronous() === false || $this->maxAsynchronousEngines === null || $this->asynchronousEngines < $this->maxAsynchronousEngines);
	}

	private function doTearDown()
	{
		$this->callObservers(self::beforeTearDown);
		$this->tearDown();
		$this->callObservers(self::afterTearDown);

		return $this;
	}

	public function getExtensions()
	{
		return iterator_to_array($this->extensions);
	}

	public function removeExtension(atoum\extension $extension)
	{
		$this->extensions->detach($extension);

		return $this->removeObserver($extension);;
	}

	public function removeExtensions()
	{
		foreach ($this->extensions as $extension)
		{
			$this->removeObserver($extension);
		}

		$this->extensions = new \splObjectStorage();

		return $this;
	}


	public function addExtension(atoum\extension $extension)
	{
		if ($this->extensions->contains($extension) === false)
		{
			$extension->setTest($this);

			$this->extensions->attach($extension);

			$this->addObserver($extension);
		}

		return $this;
	}

	public function addExtensions(\traversable $extensions)
	{
		foreach ($extensions as $extension)
		{
			$this->addExtension($extension);
		}

		return $this;
	}

	private static function cleanNamespace($namespace)
	{
		return trim((string) $namespace, '\\');
	}
}
