<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum\test,
	mageekguy\atoum\mock,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\annotations
;

/**
 * @property    mageekguy\atoum\asserter\generator assert
 */
abstract class test implements observable, adapter\aggregator, \countable
{
	const testMethodPrefix = 'test';
	const defaultNamespace = '#(?:^|\\\)tests?\\\units?\\\#i';
	const runStart = 'testRunStart';
	const beforeSetUp = 'beforeTestSetUp';
	const setUpFail = 'setUpFail';
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
	const defaultEngine = 'concurrent';
	const enginesNamespace = '\mageekguy\atoum\test\engines';

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
     * @var \mageekguy\atoum\factory
     */
	private $factory = null;

    /**
     * @var \mageekguy\atoum\adapter
     */
	private $adapter = null;

	/**
     * @var \mageekguy\atoum\test\assertion\manager
     */
    private $assertionManager = null;

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
     * @var array
     */
	private $engines = array();

    /**
     * @var string
     */
	private $classEngine = null;

    /**
     * @var array
     */
	private $methodEngines = array();

    /**
     * @var integer
     */
	private $asynchronousEngines = 0;

    /**
     * @var integer
     */
	private $maxAsynchronousEngines = null;

    /**
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
     * @var string
     */
	private static $defaultEngine = self::defaultEngine;

    /**
     * Constructor
     *
     * @param \mageekguy\atoum\factory  $factory
     */
	public function __construct(factory $factory = null)
	{
		$this
			->setFactory($factory ?: new factory())
			->setScore($this->factory['mageekguy\atoum\score']())
			->setLocale($this->factory['mageekguy\atoum\locale']())
			->setAdapter($this->factory['mageekguy\atoum\adapter']())
			->setSuperglobals($this->factory['mageekguy\atoum\superglobals']())
			->setIncluder($this->factory['mageekguy\atoum\includer']())
			->enableCodeCoverage()
		;

		$class = $this->factory->build('reflectionClass', array($this));

		$this->path = $class->getFilename();
		$this->class = $class->getName();

		$annotationExtractor = $this->factory->build('mageekguy\atoum\annotations\extractor');

		$test = $this;

		$annotationExtractor
			->setHandler('ignore', function($value) use ($test) { $test->ignore(annotations\extractor::toBoolean($value)); })
			->setHandler('tags', function($value) use ($test) { $test->setTags(annotations\extractor::toArray($value)); })
			->setHandler('namespace', function($value) use ($test) { $test->setTestNamespace($value); })
			->setHandler('maxChildrenNumber', function($value) use ($test) { $test->setMaxChildrenNumber($value); })
			->setHandler('engine', function($value) use ($test) { $test->setClassEngine($value); })
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

		$this->setAssertionManager($this->factory->build('mageekguy\atoum\test\assertion\manager'));
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
		return $this->assertionManager->invoke($property);
	}


    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return \mageekguy\atoum\test|\mageekguy\atoum\asserter\generator
     */
	public function __call($method, array $arguments)
	{
		return $this->assertionManager->invoke($method, $arguments);
	}


    /**
     * @param \mageekguy\atoum\factory $factory
     *
     * @return \mageekguy\atoum\test
     */
	public function setFactory(factory $factory)
	{
		$this->factory = $factory;

		return $this;
	}


    /**
     * @return \mageekguy\atoum\factory
     */
	public function getFactory()
	{
		return $this->factory;
	}


    /**
     * @param string $engine
     *
     * @return \mageekguy\atoum\test
     */
	public function setClassEngine($engine)
	{
		$this->classEngine = (string) $engine;

		return $this;
	}


    /**
     * @return string
     */
	public function getClassEngine()
	{
		return $this->classEngine;
	}


    /**
     * @param string $method
     * @param string $engine
     *
     * @return \mageekguy\atoum\test
     */
	public function setMethodEngine($method, $engine)
	{
		$this->methodEngines[(string) $method] = (string) $engine;

		return $this;
	}


    /**
     * @param string $method
     *
     * @return string
     */
	public function getMethodEngine($method)
	{
		$method = (string) $method;

		return (isset($this->methodEngines[$method]) === false ? null : $this->methodEngines[$method]);
	}


    /**
     * @param test\assertion\manager $assertionManager
     *
     * @return \mageekguy\atoum\test
     */
	public function setAssertionManager(test\assertion\manager $assertionManager)
	{
		$this->assertionManager = $assertionManager;

		$this->assertionManager->setHandler('when', function($mixed) use ($assertionManager) { if (is_callable($mixed) === true) { call_user_func($mixed); } return $assertionManager; });

		$returnAssertionManager = function() use ($assertionManager) { return $assertionManager; };
		$this->assertionManager->setHandler('if', $returnAssertionManager);
		$this->assertionManager->setHandler('and', $returnAssertionManager);
		$this->assertionManager->setHandler('then', $returnAssertionManager);
		$this->assertionManager->setHandler('given', $returnAssertionManager);

		$test = $this;
		$this->assertionManager->setHandler('assert', function($case = null) use ($test) { $test->stopCase(); if ($case !== null) { $test->startCase($case); } return $test->getAssertionManager(); });
		$this->assertionManager->setHandler('mockGenerator', function() use ($test) { return $test->getMockGenerator(); });
		$this->assertionManager->setHandler('mockClass', function($class, $mockNamespace = null, $mockClass = null) use ($test) { $test->getMockGenerator()->generate($class, $mockNamespace, $mockClass); return $test; });
		$this->assertionManager->setHandler('mockTestedClass', function($mockNamespace = null, $mockClass = null) use ($test) { $test->getMockGenerator()->generate($test->getTestedClassName(), $mockNamespace, $mockClass); return $test; });

		$asserterGenerator = $this->asserterGenerator;
		$this->assertionManager->setHandler('define', function() use ($asserterGenerator) { return $asserterGenerator; });

		$this->assertionManager->setDefaultHandler(function($asserter, $arguments) use ($asserterGenerator) { return $asserterGenerator->getAsserterInstance($asserter, $arguments); });

		return $this;
	}

    /**
     * @return test\assertion\manager
     */
	public function getAssertionManager()
	{
		return $this->assertionManager;
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
		$number = (int) $number;

		if ($number < 1)
		{
			throw new exceptions\logic\invalidArgument('Maximum number of children must be greater or equal to 1');
		}

		$this->maxAsynchronousEngines = $number;

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


    /**
     * @param \mageekguy\atoum\includer $includer
     *
     * @return \mageekguy\atoum\test
     */
	public function setIncluder(includer $includer)
	{
		$this->includer = $includer;

		return $this;
	}


    /**
     * @return \mageekguy\atoum\includer
     */
	public function getIncluder()
	{
		return $this->includer;
	}


    /**
     * @param string $path
     * @return \mageekguy\atoum\test
     */
	public function setBootstrapFile($path)
	{
		$this->bootstrapFile = $path;

		return $this;
	}


    /**
     * @return string
     */
	public function getBootstrapFile()
	{
		return $this->bootstrapFile;
	}


    /**
     * @param \mageekguy\atoum\test\mock\generator $generator
     *
     * @return \mageekguy\atoum\test
     */
	public function setMockGenerator(test\mock\generator $generator)
	{
		$this->mockGenerator = $generator->setTest($this);

		return $this;
	}


    /**
     * @return \mageekguy\atoum\mock\generator
     */
	public function getMockGenerator()
	{
		return $this->mockGenerator ?: $this->setMockGenerator($this->factory->build('mageekguy\atoum\test\mock\generator', array($this)))->mockGenerator;
	}


    /**
     * @param \mageekguy\atoum\test\asserter\generator $generator
     *
     * @return \mageekguy\atoum\test
     */
	public function setAsserterGenerator(test\asserter\generator $generator)
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

		return $this->asserterGenerator ?: $this->setAsserterGenerator($this->factory->build('mageekguy\atoum\test\asserter\generator', array($this)))->asserterGenerator;
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


    /**
     * @param string $className
     *
     * @return \mageekguy\atoum\test
     *
     * @throws exceptions\runtime
     */
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


    /**
     * @return string
     */
	public function getPath()
	{
		return $this->path;
	}


    /**
     * @param array $methods
     * @param array $tags
     *
     * @return array
     */
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
		return $this->maxAsynchronousEngines;
	}


    /**
     * @return score\coverage
     */
	public function getCoverage()
	{
		return $this->score->getCoverage();
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


    /**
     * @param string $methodName
     * @param boolean $boolean
     *
     * @return \mageekguy\atoum\test
     */
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

			$mockGenerator = $this->getMockGenerator();
			$mockNamespacePattern = '/^' . $mockGenerator->getDefaultNamespace() . '\\\/';

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

						$reflectedTestMethod = $this->factory->build('reflectionMethod', array($this, $testMethod));
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
						$this->getCoverage()->addXdebugDataForTest($this, xdebug_get_code_coverage());
						xdebug_stop_code_coverage();
					}

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
				try
				{
					$this->callObservers(self::beforeSetUp);

					if ($this->setUp() === false)
					{
						$this->callObservers(self::setUpFail);
					}
					else
					{
						$this->callObservers(self::afterSetUp);

						while ($this->runEngine()->engines)
						{
							$engines = array();

							foreach ($this->engines as $this->currentMethod => $engine)
							{
								$score = $engine->getScore();

								if ($score === null)
								{
									$engines[$this->currentMethod] = $engine;
								}
								else
								{
									$this->callObservers(self::afterTestMethod);

									switch (true)
									{
										case $score->getRuntimeExceptionNumber() > 0:
											$this->callObservers(self::runtimeException);
											throw current($score->getRuntimeExceptions());

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

										case $score->getPassNumber():
											$this->callObservers(self::success);
											break;
									}

									$this->score->merge($score);

									if ($engine->isAsynchronous() === true)
									{
										$this->asynchronousEngines--;
									}
								}
							}

							$this->engines = $engines;
							$this->currentMethod = null;
						}
					}
				}
				catch (\exception $exception)
				{
					$this->doTearDown();

					throw $exception;
				}

				$this->doTearDown();
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
     * @deprecated
     *
     * @param string $class
     * @param string $mockNamespace
     * @param string $mockClass
     *
     * @return \mageekguy\atoum\test
     */
	public function mock($class, $mockNamespace = null, $mockClass = null)
	{
		die(__METHOD__ . ' is deprecated, please use ' . __CLASS__ . '::mockClass() instead');

		return $this;
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
     * @param string $defaultEngine
     */
    public static function setDefaultEngine($defaultEngine)
	{
		self::$defaultEngine = (string) $defaultEngine;
	}


    /**
     * @return string
     */
	public static function getDefaultEngine()
	{
		return self::$defaultEngine ?: self::defaultEngine;
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
	private function runEngine()
	{
		$this->currentMethod = current($this->runTestMethods);

		$engineClass = ($this->getMethodEngine($this->currentMethod) ?: $this->getClassEngine() ?: self::getDefaultEngine());

		if (ltrim($engineClass, '\\') === $engineClass)
		{
			$engineClass = self::enginesNamespace . '\\' . $engineClass;
		}

		$engine = $this->factory[$engineClass]($this->factory);

		if ($engine instanceof test\engine === false)
		{
			throw new exceptions\runtime('Engine \'' . $engineClass . '\' is invalid');
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


    /**
     * @return boolean
     */
	private function canRunEngine(test\engine $engine)
	{
		return ($this->runTestMethods && ($engine->isAsynchronous() === false || ($this->maxAsynchronousEngines === null || $this->asynchronousEngines < $this->maxAsynchronousEngines)));
	}


    /**
     * @return \mageekguy\atoum\test
     */
	private function doTearDown()
	{
		$this->callObservers(self::beforeTearDown);
		$this->tearDown();
		$this->callObservers(self::afterTearDown);

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


    /**
     * @param string $namespace
     *
     * @return boolean
     */
	private static function isRegex($namespace)
	{
		return preg_match('/^([^\\\[:alnum:][:space:]]).*\1.*$/', $namespace) === 1;
	}
}

?>
