<?php

namespace mageekguy\atoum;

use mageekguy\atoum\extension\aggregator;

class runner implements observable
{
	const atoumVersionConstant = 'mageekguy\atoum\version';
	const atoumDirectoryConstant = 'mageekguy\atoum\directory';

	const runStart = 'runnerStart';
	const runStop = 'runnerStop';

	protected $score = null;
	protected $adapter = null;
	protected $locale = null;
	protected $includer = null;
	protected $testGenerator = null;
	protected $globIteratorFactory = null;
	protected $reflectionClassFactory = null;
	protected $testFactory = null;
	protected $observers = null;
	protected $reports = null;
	protected $reportSet = null;
	protected $testPaths = array();
	protected $testNumber = 0;
	protected $testMethodNumber = 0;
	protected $codeCoverage = true;
	protected $branchesAndPathsCoverage = false;
	protected $php = null;
	protected $defaultReportTitle = null;
	protected $maxChildrenNumber = null;
	protected $bootstrapFile = null;
	protected $autoloaderFile = null;
	protected $testDirectoryIterator = null;
	protected $debugMode = false;
	protected $xdebugConfig = null;
	protected $failIfVoidMethods = false;
	protected $failIfSkippedMethods = false;
	protected $disallowUsageOfUndefinedMethodInMock = false;
	protected $extensions = null;

	private $start = null;
	private $stop = null;
	private $canAddTest = true;

	public function __construct()
	{
		$this
			->setAdapter()
			->setLocale()
			->setIncluder()
			->setScore()
			->setPhp()
			->setTestDirectoryIterator()
			->setGlobIteratorFactory()
			->setReflectionClassFactory()
			->setTestFactory()
		;

		$this->observers = new \splObjectStorage();
		$this->reports = new \splObjectStorage();
		$this->extensions = new aggregator();
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

	public function setLocale(locale $locale = null)
	{
		$this->locale = $locale ?: new locale();

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setIncluder(includer $includer = null)
	{
		$this->includer = $includer ?: new includer();

		return $this;
	}

	public function getIncluder()
	{
		return $this->includer;
	}

	public function setScore(runner\score $score = null)
	{
		$this->score = $score ?: new runner\score();

		return $this;
	}

	public function getScore()
	{
		return $this->score;
	}

	public function setTestGenerator(test\generator $generator = null)
	{
		$this->testGenerator = $generator ?: new test\generator();

		return $this;
	}

	public function getTestGenerator()
	{
		return $this->testGenerator;
	}

	public function setTestDirectoryIterator(iterators\recursives\directory\factory $iterator = null)
	{
		$this->testDirectoryIterator = $iterator ?: new iterators\recursives\directory\factory();

		return $this;
	}

	public function getTestDirectoryIterator()
	{
		return $this->testDirectoryIterator;
	}

	public function setGlobIteratorFactory(\closure $factory = null)
	{
		$this->globIteratorFactory = $factory ?: function($pattern) { return new \globIterator($pattern); };

		return $this;
	}

	public function getGlobIteratorFactory()
	{
		return $this->globIteratorFactory;
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

	public function disallowUndefinedMethodInInterface()
	{
		return $this->disallowUsageOfUndefinedMethodInMock();
	}

	public function disallowUsageOfUndefinedMethodInMock()
	{
		$this->disallowUsageOfUndefinedMethodInMock = true;

		return $this;
	}

	public function allowUndefinedMethodInInterface()
	{
		return $this->allowUsageOfUndefinedMethodInMock();
	}

	public function allowUsageOfUndefinedMethodInMock()
	{
		$this->disallowUsageOfUndefinedMethodInMock = false;

		return $this;
	}

	public function undefinedMethodInInterfaceAreAllowed()
	{
		return $this->usageOfUndefinedMethodInMockAreAllowed();
	}

	public function usageOfUndefinedMethodInMockAreAllowed()
	{
		return $this->disallowUsageOfUndefinedMethodInMock === false;
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

	public function setMaxChildrenNumber($number)
	{
		if ($number < 1)
		{
			throw new exceptions\logic\invalidArgument('Maximum number of children must be greater or equal to 1');
		}

		$this->maxChildrenNumber = $number;

		return $this;
	}

	public function acceptTestFileExtensions(array $testFileExtensions)
	{
		$this->testDirectoryIterator->acceptExtensions($testFileExtensions);

		return $this;
	}

	public function setDefaultReportTitle($title)
	{
		$this->defaultReportTitle = (string) $title;

		return $this;
	}

	public function setBootstrapFile($path)
	{
		try
		{
			$this->includer->includePath($path, function($path) { include_once($path); });
		}
		catch (includer\exception $exception)
		{
			throw new exceptions\runtime\file(sprintf($this->getLocale()->_('Unable to use bootstrap file \'%s\''), $path));
		}

		$this->bootstrapFile = $path;

		return $this;
	}

	public function setAutoloaderFile($path)
	{
		try
		{
			$this->includer->includePath($path, function($path) { include_once($path); });
		}
		catch (includer\exception $exception)
		{
			throw new exceptions\runtime\file(sprintf($this->getLocale()->_('Unable to use autoloader file \'%s\''), $path));
		}

		$this->autoloaderFile = $path;

		return $this;
	}

	public function getDefaultReportTitle()
	{
		return $this->defaultReportTitle;
	}

	public function setPhp(php $php = null)
	{
		$this->php = $php ?: new php();

		return $this;
	}

	public function getPhp()
	{
		return $this->php;
	}

	public function setPhpPath($path)
	{
		$this->php->setBinaryPath($path);

		return $this;
	}

	public function getPhpPath()
	{
		return $this->php->getBinaryPath();
	}

	public function getTestNumber()
	{
		return $this->testNumber;
	}

	public function getTestMethodNumber()
	{
		return $this->testMethodNumber;
	}

	public function getObservers()
	{
		$observers = array();

		foreach ($this->observers as $observer)
		{
			$observers[] = $observer;
		}

		return $observers;
	}

	public function getBootstrapFile()
	{
		return $this->bootstrapFile;
	}

	public function getAutoloaderFile()
	{
		return $this->autoloaderFile;
	}

	public function getTestMethods(array $namespaces = array(), array $tags = array(), array $testMethods = array(), $testBaseClass = null)
	{
		$classes = array();

		foreach ($this->getDeclaredTestClasses($testBaseClass) as $testClass)
		{
			$test = new $testClass();

			if ($test->isIgnored($namespaces, $tags) === false)
			{
				$methods = $test->runTestMethods($testMethods, $tags);

				if ($methods)
				{
					$classes[$testClass] = $methods;
				}
			}
		}

		return $classes;
	}

	public function getCoverage()
	{
		return $this->score->getCoverage();
	}

	public function enableCodeCoverage()
	{
		$this->codeCoverage = true;

		return $this;
	}

	public function disableCodeCoverage()
	{
		$this->codeCoverage = false;

		return $this;
	}

	public function codeCoverageIsEnabled()
	{
		return $this->codeCoverage;
	}

	public function enableBranchesAndPathsCoverage()
	{
		$this->branchesAndPathsCoverage = $this->codeCoverageIsEnabled();

		return $this;
	}

	public function disableBranchesAndPathsCoverage()
	{
		$this->branchesAndPathsCoverage = false;

		return $this;
	}

	public function branchesAndPathsCoverageIsEnabled()
	{
		return $this->branchesAndPathsCoverage;
	}

	public function doNotfailIfVoidMethods()
	{
		$this->failIfVoidMethods = false;

		return $this;
	}

	public function failIfVoidMethods()
	{
		$this->failIfVoidMethods = true;

		return $this;
	}

	public function shouldFailIfVoidMethods()
	{
		return $this->failIfVoidMethods;
	}

	public function doNotfailIfSkippedMethods()
	{
		$this->failIfSkippedMethods = false;

		return $this;
	}

	public function failIfSkippedMethods()
	{
		$this->failIfSkippedMethods = true;

		return $this;
	}

	public function shouldFailIfSkippedMethods()
	{
		return $this->failIfSkippedMethods;
	}

	public function addObserver(observer $observer)
	{
		$this->observers->attach($observer);

		return $this;
	}

	public function removeObserver(observer $observer)
	{
		$this->observers->detach($observer);

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

	public function setPathAndVersionInScore()
	{
		$this->score
			->setAtoumVersion($this->adapter->defined(static::atoumVersionConstant) === false ? null : $this->adapter->constant(static::atoumVersionConstant))
			->setAtoumPath($this->adapter->defined(static::atoumDirectoryConstant) === false ? null : $this->adapter->constant(static::atoumDirectoryConstant))
		;

		if ($this->php->reset()->addOption('--version')->run()->getExitCode() > 0)
		{
			throw new exceptions\runtime('Unable to get PHP version from \'' . $this->php . '\'');
		}

		$this->score
			->setPhpPath($this->php->getBinaryPath())
			->setPhpVersion($this->php->getStdout())
		;

		return $this;
	}

	public function getTestFactory()
	{
		return $this->testFactory;
	}

	public function setTestFactory($testFactory = null)
	{
		$testFactory = $testFactory ?: function($testClass) {
			return new $testClass();
		};

		$runner = $this;

		$this->testFactory = function($testClass) use ($testFactory, $runner) {
			$test = call_user_func($testFactory, $testClass);

			if ($runner->usageOfUndefinedMethodInMockAreAllowed() === false)
			{
				$test->getMockGenerator()->disallowUndefinedMethodUsage();
			}

			return $test;
		};

		return $this;
	}

	public function run(array $namespaces = array(), array $tags = array(), array $runTestClasses = array(), array $runTestMethods = array(), $testBaseClass = null)
	{
		$this->includeTestPaths();

		$this->testNumber = 0;
		$this->testMethodNumber = 0;

		$this->score->reset();

		$this->setPathAndVersionInScore();

		if ($this->defaultReportTitle !== null)
		{
			foreach ($this->reports as $report)
			{
				if ($report->getTitle() === null)
				{
					$report->setTitle($this->defaultReportTitle);
				}
			}
		}

		$declaredTestClasses = $this->getDeclaredTestClasses($testBaseClass);

		if (sizeof($runTestClasses) <= 0)
		{
			$runTestClasses = $declaredTestClasses;
		}
		else
		{
			$runTestClasses = array_intersect($runTestClasses, $declaredTestClasses);
		}

		natsort($runTestClasses);

		$tests = array();

		foreach ($runTestClasses as $runTestClass)
		{
			$test = call_user_func($this->testFactory, $runTestClass);

			if ($test->isIgnored($namespaces, $tags) === false)
			{
				$testMethodNumber = sizeof($test->runTestMethods($runTestMethods, $tags));

				if ($testMethodNumber > 0)
				{
					$tests[] = $test;
					$test->addExtensions($this->extensions);

					$this->testNumber++;
					$this->testMethodNumber += $testMethodNumber;

					$test
						->setPhpPath($this->php->getBinaryPath())
						->setAdapter($this->adapter)
						->setLocale($this->locale)
						->setBootstrapFile($this->bootstrapFile)
						->setAutoloaderFile($this->autoloaderFile)
					;

					if ($this->debugMode === true)
					{
						$test->enableDebugMode();
					}

					$test->setXdebugConfig($this->xdebugConfig);

					if ($this->maxChildrenNumber !== null)
					{
						$test->setMaxChildrenNumber($this->maxChildrenNumber);
					}

					if ($this->codeCoverageIsEnabled() === false)
					{
						$test->disableCodeCoverage();
					}
					else
					{
						if ($this->branchesAndPathsCoverageIsEnabled())
						{
							$test->enableBranchesAndPathsCoverage();
						}

						$test->getScore()->setCoverage($this->getCoverage());
					}

					foreach ($this->observers as $observer)
					{
						$test->addObserver($observer);
					}
				}
			}
		}

		$this->start = $this->adapter->microtime(true);

		$this->callObservers(self::runStart);

		foreach ($tests as $test)
		{
			$this->score->merge($test->run()->getScore());
		}

		$this->stop = $this->adapter->microtime(true);

		$this->callObservers(self::runStop);

		return $this->score;
	}

	public function getTestPaths()
	{
		return $this->testPaths;
	}

	public function setTestPaths(array $testPaths)
	{
		$this->testPaths = $testPaths;

		return $this;
	}

	public function resetTestPaths()
	{
		$this->testPaths = array();

		return $this;
	}

	public function canAddTest()
	{
		$this->canAddTest = true;

		return $this;
	}

	public function canNotAddTest()
	{
		$this->canAddTest = false;

		return $this;
	}

	public function addTest($path)
	{
		if ($this->canAddTest === true)
		{
			$path = (string) $path;

			if (in_array($path, $this->testPaths) === false)
			{
				$this->testPaths[] = $path;
			}
		}

		return $this;
	}

	public function addTestsFromDirectory($directory)
	{
		try
		{
			$paths = array();

			foreach (new \recursiveIteratorIterator($this->testDirectoryIterator->getIterator($directory)) as $path)
			{
				$paths[] = $path;
			}
		}
		catch (\UnexpectedValueException $exception)
		{
			throw new exceptions\runtime('Unable to read test directory \'' . $directory . '\'');
		}

		natcasesort($paths);

		foreach ($paths as $path)
		{
			$this->addTest($path);
		}

		return $this;
	}

	public function addTestsFromPattern($pattern)
	{
		try
		{
			$paths = array();

			foreach (call_user_func($this->globIteratorFactory, rtrim($pattern, DIRECTORY_SEPARATOR)) as $path)
			{
				$paths[] = $path;
			}
		}
		catch (\UnexpectedValueException $exception)
		{
			throw new exceptions\runtime('Unable to read test from pattern \'' . $pattern . '\'');
		}

		natcasesort($paths);

		foreach ($paths as $path)
		{
			if ($path->isDir() === false)
			{
				$this->addTest($path);
			}
			else
			{
				$this->addTestsFromDirectory($path);
			}
		}

		return $this;
	}

	public function getRunningDuration()
	{
		return ($this->start === null || $this->stop === null ? null : $this->stop - $this->start);
	}

	public function getDeclaredTestClasses($testBaseClass = null)
	{
		return $this->findTestClasses($testBaseClass);
	}

	public function setReport(report $report)
	{
		if ($this->reportSet === null)
		{
			$this->removeReports($report)->addReport($report);

			$this->reportSet = $report;
		}

		return $this;
	}

	public function addReport(report $report)
	{
		if ($this->reportSet === null || $this->reportSet->isOverridableBy($report))
		{
			$this->reports->attach($report);

			$this->addObserver($report);
		}

		return $this;
	}

	public function removeReport(report $report)
	{
		if ($this->reportSet === $report)
		{
			$this->reportSet = null;
		}

		$this->reports->detach($report);

		return $this->removeObserver($report);
	}

	public function removeReports(report $override = null)
	{
		if ($override === null)
		{
			foreach ($this->reports as $report)
			{
				$this->removeObserver($report);
			}

			$this->reports = new \splObjectStorage();

		}
		else
		{
			foreach ($this->reports as $report)
			{
				if ($report->isOverridableBy($override) === true)
				{
					continue;
				}

				$this->removeObserver($report);
				$this->reports->detach($report);
			}
		}

		$this->reportSet = null;

		return $this;
	}

	public function hasReports()
	{
		return (sizeof($this->reports) > 0);
	}

	public function getReports()
	{
		$reports = array();

		foreach ($this->reports as $report)
		{
			$reports[] = $report;
		}

		return $reports;
	}

	public function getExtension($className)
	{
		foreach ($this->getExtensions() as $extension) if (get_class($extension) === $className)
		{
			return $extension;
		}

		throw new exceptions\logic\invalidArgument(sprintf('Extension %s is not loaded', $className));
	}

	public function getExtensions()
	{
		return $this->extensions;
	}

	public function removeExtension($extension)
	{
		if (is_object($extension) === true)
		{
			$extension = get_class($extension);
		}

		$extension = $this->getExtension($extension);
		$this->extensions->detach($extension);

		return $this->removeObserver($extension);
	}

	public function removeExtensions()
	{
		foreach ($this->extensions as $extension)
		{
			$this->removeObserver($extension);
		}

		$this->extensions = new aggregator();

		return $this;
	}

	public function addExtension(extension $extension, extension\configuration $configuration = null)
	{
		if ($this->extensions->contains($extension) === false)
		{
			$extension->setRunner($this);
			$this->addObserver($extension);
		}

		$this->extensions->attach($extension, $configuration);

		return $this;
	}

	public static function isIgnored(test $test, array $namespaces, array $tags)
	{
		$isIgnored = $test->isIgnored();

		if ($isIgnored === false && $namespaces)
		{
			$classNamespace = strtolower($test->getClassNamespace());

			$isIgnored = sizeof(array_filter($namespaces, function($value) use ($classNamespace) { return strpos($classNamespace, strtolower($value)) === 0; })) <= 0;
		}

		if ($isIgnored === false && $tags)
		{
			$isIgnored = sizeof($testTags = $test->getAllTags()) <= 0 || sizeof(array_intersect($tags, $testTags)) == 0;
		}

		return $isIgnored;
	}

	protected function findTestClasses($testBaseClass = null)
	{
		$reflectionClassFactory = $this->reflectionClassFactory;
		$testBaseClass = $testBaseClass ?: __NAMESPACE__ . '\test';

		return array_filter($this->adapter->get_declared_classes(), function($class) use ($reflectionClassFactory, $testBaseClass) {
				$class = $reflectionClassFactory($class);

				return ($class->isSubClassOf($testBaseClass) === true && $class->isAbstract() === false);
			}
		);
	}

	private function includeTestPaths()
	{
		$runner = $this;
		$includer = function($path) use ($runner) { include_once($path); };

		foreach ($this->testPaths as $testPath)
		{
			try
			{
				$declaredTestClasses = $this->findTestClasses();
				$numberOfIncludedFiles = sizeof(get_included_files());

				$this->includer->includePath($testPath, $includer);

				if ($numberOfIncludedFiles < sizeof(get_included_files()) && sizeof(array_diff($this->findTestClasses(), $declaredTestClasses)) <= 0 && $this->testGenerator !== null)
				{
					$this->testGenerator->generate($testPath);

					try
					{
						$this->includer->includePath($testPath, function($testPath) use ($runner) { include($testPath); });
					}
					catch (includer\exception $exception)
					{
						throw new exceptions\runtime\file(sprintf($this->getLocale()->_('Unable to add test file \'%s\''), $testPath));
					}
				}
			}
			catch (includer\exception $exception)
			{
				if ($this->testGenerator === null)
				{
					throw new exceptions\runtime\file(sprintf($this->getLocale()->_('Unable to add test file \'%s\''), $testPath));
				}
				else
				{
					$this->testGenerator->generate($testPath);

					try
					{
						$this->includer->includePath($testPath, $includer);
					}
					catch (includer\exception $exception)
					{
						throw new exceptions\runtime\file(sprintf($this->getLocale()->_('Unable to generate test file \'%s\''), $testPath));
					}
				}
			}
		}

		return $this;
	}
}
