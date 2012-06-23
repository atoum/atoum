<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\iterators,
	mageekguy\atoum\exceptions
;

class runner implements observable, adapter\aggregator
{
	const atoumVersionConstant = 'mageekguy\atoum\version';
	const atoumDirectoryConstant = 'mageekguy\atoum\directory';

	const runStart = 'runnerStart';
	const runStop = 'runnerStop';

	protected $path = '';
	protected $class = '';
	protected $score = null;
	protected $adapter = null;
	protected $locale = null;
	protected $factory = null;
	protected $includer = null;
	protected $observers = null;
	protected $reports = null;
	protected $testNumber = 0;
	protected $testMethodNumber = 0;
	protected $codeCoverage = true;
	protected $phpPath = null;
	protected $defaultReportTitle = null;
	protected $maxChildrenNumber = null;
	protected $bootstrapFile = null;
	protected $testDirectoryIterator = null;

	private $start = null;
	private $stop = null;

	public function __construct(factory $factory = null)
	{
		$this
			->setFactory($factory ?: new factory())
			->setAdapter($this->factory['mageekguy\atoum\adapter']())
			->setLocale($this->factory['mageekguy\atoum\locale']())
			->setIncluder($this->factory['mageekguy\atoum\includer']())
			->setScore($this->factory['mageekguy\atoum\score']($this->factory))
			->setTestDirectoryIterator($this->factory['mageekguy\atoum\iterators\recursives\directory']())
		;

		$this->factory['mageekguy\atoum\adapter'] = $this->adapter;
		$this->factory['mageekguy\atoum\locale'] = $this->locale;
		$this->factory['mageekguy\atoum\includer'] = $this->includer;

		$runnerClass = $this->factory['reflectionClass']($this);

		$this->path = $runnerClass->getFilename();
		$this->class = $runnerClass->getName();

		$this->observers = new \splObjectStorage();
		$this->reports = new \splObjectStorage();
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

	public function setTestDirectoryIterator(iterators\recursives\directory $iterator)
	{
		$this->testDirectoryIterator = $iterator;

		return $this;
	}

	public function getTestDirectoryIterator()
	{
		return $this->testDirectoryIterator;
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

	public function setAdapter(adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
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

	public function setIncluder(includer $includer)
	{
		$this->includer = $includer;

		return $this;
	}

	public function getIncluder()
	{
		return $this->includer;
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
		catch (atoum\includer\exception $exception)
		{
			throw new exceptions\runtime\file(sprintf($this->getLocale()->_('Unable to use bootstrap file \'%s\''), $path));
		}

		$this->bootstrapFile = $path;

		return $this;
	}

	public function getDefaultReportTitle()
	{
		return $this->defaultReportTitle;
	}

	public function getPhpPath()
	{
		if ($this->phpPath === null)
		{
			if (($phpPath = $this->adapter->getenv('PHP_PEAR_PHP_BIN')) === false)
			{
				if (($phpPath = $this->adapter->getenv('PHPBIN')) === false)
				{
					if ($this->adapter->constant('DIRECTORY_SEPARATOR') === '\\' || ($phpPath = ($this->adapter->defined('PHP_BINARY') ? PHP_BINARY : PHP_BINDIR . '/php')) === false)
					{
						throw new exceptions\runtime('Unable to find PHP executable');
					}
				}
			}

			$this->setPhpPath($phpPath);
		}

		return $this->phpPath;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getClass()
	{
		return $this->class;
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

	public function getTestMethods(array $namespaces = array(), array $tags = array(), array $testMethods = array(), $testBaseClass = null)
	{
		$classes = array();

		$testClasses = $this->getDeclaredTestClasses($testBaseClass);

		foreach ($testClasses as $testClass)
		{
			$test = new $testClass($this->factory);

			if (self::isIgnored($test, $namespaces, $tags) === false)
			{
				$methods =  self::getMethods($test, $testMethods, $tags);

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

	public function setPhpPath($path)
	{
		$this->phpPath = (string) $path;

		return $this;
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

	public function addObserver(atoum\observer $observer)
	{
		$this->observers->attach($observer);

		return $this;
	}

	public function removeObserver(atoum\observer $observer)
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

		$phpPath = $this->adapter->realpath($this->getPhpPath());

		if ($phpPath === false)
		{
			throw new exceptions\runtime('Unable to find \'' . $this->getPhpPath() . '\'');
		}
		else
		{
			$descriptors = array(
				1 => array('pipe', 'w'),
				2 => array('pipe', 'w'),
			);

			$php = @$this->adapter->invoke('proc_open', array(escapeshellarg($phpPath) . ' --version', $descriptors, & $pipes));

			if ($php === false)
			{
				throw new exceptions\runtime('Unable to open \'' . $phpPath . '\'');
			}

			$phpVersion = trim($this->adapter->stream_get_contents($pipes[1]));

			$this->adapter->fclose($pipes[1]);
			$this->adapter->fclose($pipes[2]);

			$phpStatus = $this->adapter->proc_get_status($php);

			while ($phpStatus['running'] == true)
			{
				$phpStatus = $this->adapter->proc_get_status($php);
			}

			$this->adapter->proc_close($php);

			if ($phpStatus['exitcode'] > 0)
			{
				throw new exceptions\runtime('Unable to get PHP version from \'' . $phpPath . '\'');
			}

			$this->score
				->setPhpPath($phpPath)
				->setPhpVersion($phpVersion)
			;
		}

		return $this;
	}

	public function run(array $namespaces = array(), array $tags = array(), array $runTestClasses = array(), array $runTestMethods = array(), $testBaseClass = null)
	{
		$this->start = $this->adapter->microtime(true);
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
			$test = new $runTestClass($this->factory);

			if (self::isIgnored($test, $namespaces, $tags) === false && ($methods = self::getMethods($test, $runTestMethods, $tags)))
			{
				$tests[] = array($test, $methods);

				$this->testNumber++;
				$this->testMethodNumber += sizeof($methods);
			}
		}

		$this->callObservers(self::runStart);

		if ($tests)
		{
			$phpPath = $this->getPhpPath();

			foreach ($tests as $testMethods)
			{
				list($test, $methods) = $testMethods;

				$test
					->setPhpPath($phpPath)
					->setLocale($this->locale)
					->setBootstrapFile($this->bootstrapFile)
				;

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
					$test->getScore()->setCoverage($this->getCoverage());
				}

				foreach ($this->observers as $observer)
				{
					$test->addObserver($observer);
				}

				$this->score->merge($test->run($methods)->getScore());
			}
		}

		$this->stop = $this->adapter->microtime(true);

		$this->callObservers(self::runStop);

		return $this->score;
	}

	public function addTest($path)
	{
		$runner = $this;

		try
		{
			$this->includer->includePath($path, function($path) use ($runner) { include_once($path); });
		}
		catch (atoum\includer\exception $exception)
		{
			throw new exceptions\runtime\file(sprintf($this->getLocale()->_('Unable to add test file \'%s\''), $path));
		}

		return $this;
	}

	public function addTestsFromDirectory($directory)
	{
		try
		{
			foreach (new \recursiveIteratorIterator($this->testDirectoryIterator->getIterator($directory)) as $path)
			{
				$this->addTest($path);
			}
		}
		catch (\UnexpectedValueException $exception)
		{
			throw new exceptions\runtime('Unable to read test directory \'' . $directory . '\'');
		}

		return $this;
	}

	public function addTestsFromPattern($pattern)
	{
		try
		{
			foreach ($this->factory['globIterator'](rtrim($pattern, DIRECTORY_SEPARATOR)) as $path)
			{
				if ($path->isDir() === true)
				{
					$this->addTestsFromDirectory($path);
				}
				else
				{
					$this->addTest($path);
				}
			}
		}
		catch (\UnexpectedValueException $exception)
		{
			throw new exceptions\runtime('Unable to read test from pattern \'' . $pattern . '\'');
		}

		return $this;
	}

	public function getRunningDuration()
	{
		return ($this->start === null || $this->stop === null ? null : $this->stop - $this->start);
	}

	public function getDeclaredTestClasses($testBaseClass = null)
	{
		$factory = $this->factory;
		$testBaseClass = $testBaseClass ?: __NAMESPACE__ . '\test';

		return array_filter($this->adapter->get_declared_classes(), function($class) use ($factory, $testBaseClass) {
				$class = $factory['reflectionClass']($class);
				return ($class->isSubClassOf($testBaseClass) === true && $class->isAbstract() === false);
			}
		);
	}

	public function addReport(atoum\report $report)
	{
		$this->reports->attach($report);

		return $this->addObserver($report);
	}

	public function removeReport(atoum\report $report)
	{
		$this->reports->detach($report);

		return $this->removeObserver($report);
	}

	public function removeReports()
	{
		foreach ($this->reports as $report)
		{
			$this->removeObserver($report);
		}

		$this->reports = new \splObjectStorage();

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

	private static function getMethods(test $test, array $runTestMethods, array $tags)
	{
		$methods = array();

		if (isset($runTestMethods['*']) === true)
		{
			$methods = $runTestMethods['*'];
		}

		$testClass = $test->getClass();

		if (isset($runTestMethods[$testClass]) === true)
		{
			$methods = $runTestMethods[$testClass];
		}

		if (in_array('*', $methods) === true)
		{
			$methods = array();
		}

		if (sizeof($methods) <= 0)
		{
			$methods = $test->getTestMethods($tags);
		}
		else
		{
			$methods = $test->getTaggedTestMethods($methods, $tags);
		}

		return $methods;
	}
}
