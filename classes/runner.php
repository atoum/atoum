<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class runner implements observable, adapter\aggregator
{
	const runStart = 'runnerStart';
	const runStop = 'runnerStop';
	const atoumVersionConstant = 'mageekguy\atoum\version';
	const atoumDirectoryConstant = 'mageekguy\atoum\directory';

	protected $path = '';
	protected $class = '';
	protected $score = null;
	protected $locale = null;
	protected $adapter = null;
	protected $observers = array();
	protected $testObservers = array();
	protected $reports = array();
	protected $testNumber = 0;
	protected $testMethodNumber = 0;
	protected $codeCoverage = true;
	protected $phpPath = null;
	protected $defaultReportTitle = null;
	protected $maxChildrenNumber = null;

	private $start = null;
	private $stop = null;

	public function __construct(score $score = null, adapter $adapter = null, superglobals $superglobals = null)
	{
		$this
			->setSuperglobals($superglobals ?: new superglobals())
			->setAdapter($adapter ?: new adapter())
			->setScore($score ?: new score())
			->setLocale(new locale())
		;

		$runnerClass = new \reflectionClass($this);

		$this->path = $runnerClass->getFilename();
		$this->class = $runnerClass->getName();
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

	public function setLocale(locale $locale)
	{
		$this->locale = $locale;

		return $this;
	}

	public function setSuperglobals(atoum\superglobals $superglobals)
	{
		$this->superglobals = $superglobals;

		return $this;
	}

	public function setDefaultReportTitle($title)
	{
		$this->defaultReportTitle = (string) $title;

		return $this;
	}

	public function setScore(score $score)
	{
		$this->score = $score;

		return $this;
	}

	public function setAdapter(adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function getDefaultReportTitle()
	{
		return $this->defaultReportTitle;
	}

	public function getSuperglobals()
	{
		return $this->superglobals;
	}

	public function getScore()
	{
		return $this->score;
	}

	public function getPhpPath()
	{
		if ($this->phpPath === null)
		{
			if (isset($this->superglobals->_SERVER['_']) === true)
			{
				$this->setPhpPath($this->superglobals->_SERVER['_']);
			}
			else
			{
				$phpPath = PHP_BINDIR . '/php';

				if ($this->adapter->is_executable($phpPath) === false)
				{
					throw new exceptions\runtime('Unable to find PHP executable');
				}

				$this->setPhpPath($phpPath);
			}
		}

		return $this->phpPath;
	}

	public function getAdapter()
	{
		return $this->adapter;
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
		return $this->observers;
	}

	public function getTestClasses(array $namespaces = array(), array $tags = array(), array $testMethods = array(), $testBaseClass = null)
	{
		$classes = array();

		$testClasses = $this->getDeclaredTestClasses($testBaseClass);

		foreach ($testClasses as $testClass)
		{
			$test = new $testClass();

			if (self::isIgnored($test, $namespaces, $tags) === false && self::getMethods($test, $testClass, $testMethods, $tags))
			{
				$classes[] = $test;
			}
		}

		return $classes;
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

	public function addObserver(atoum\observers\runner $observer)
	{
		$this->observers[] = $observer;

		return $this;
	}

	public function removeObserver(atoum\observers\runner $observer)
	{
		$this->observers = self::remove($observer, $this->observers);

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

	public function addTestObserver(atoum\observers\test $observer)
	{
		$this->testObservers[] = $observer;

		return $this;
	}

	public function removeTestObserver(atoum\observers\test $observer)
	{
		$this->testObservers = self::remove($observer, $this->testObservers);

		return $this;
	}

	public function getTestObservers()
	{
		return $this->testObservers;
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

		$this->callObservers(self::runStart);

		if ($runTestClasses)
		{
			natsort($runTestClasses);

			$phpPath = $this->getPhpPath();

			foreach ($runTestClasses as $runTestClass)
			{
				$test = new $runTestClass();

				if (self::isIgnored($test, $namespaces, $tags) === false && ($methods = self::getMethods($test, $runTestClass, $runTestMethods, $tags)))
				{
					$test
						->setLocale($this->locale)
						->setPhpPath($phpPath)
					;

					if ($this->maxChildrenNumber !== null)
					{
						$test->setMaxChildrenNumber($this->maxChildrenNumber);
					}

					if ($this->codeCoverageIsEnabled() === false)
					{
						$test->disableCodeCoverage();
					}

					foreach ($this->testObservers as $observer)
					{
						$test->addObserver($observer);
					}

					$this->score->merge($test->run($methods)->getScore());

					$this->testNumber++;
					$this->testMethodNumber += sizeof($methods);
				}
			}
		}

		$this->stop = $this->adapter->microtime(true);

		$this->callObservers(self::runStop);

		return $this->score;
	}

	public function getRunningDuration()
	{
		return ($this->start === null || $this->stop === null ? null : $this->stop - $this->start);
	}

	public function getDeclaredTestClasses($testBaseClass = null)
	{
		$testBaseClass = $testBaseClass ?: __NAMESPACE__ . '\test';

		return array_filter($this->adapter->get_declared_classes(), function($class) use ($testBaseClass) {
				$class = new \reflectionClass($class);
				return ($class->isSubClassOf($testBaseClass) === true && $class->isAbstract() === false);
			}
		);
	}

	public function addReport(atoum\report $report)
	{
		$this->reports[] = $report;

		return $this
			->addObserver($report)
			->addTestObserver($report)
		;
	}

	public function removeReport(atoum\report $report)
	{
		$this->reports = self::remove($report, $this->reports);

		return $this
			->removeObserver($report)
			->removeTestObserver($report)
		;
	}

	public function removeReports()
	{
		foreach ($this->reports as $report)
		{
			$this->removeReport($report);
		}

		return $this;
	}

	public function hasReports()
	{
		return (sizeof($this->reports) > 0);
	}

	public function getReports()
	{
		return $this->reports;
	}

	public static function getObserverEvents()
	{
		return array(self::runStart, self::runStop);
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
			$isIgnored = sizeof($testTags = $test->getTags()) <= 0 || sizeof(array_intersect($tags, $testTags)) == 0;
		}

		return $isIgnored;
	}

	protected static function remove($needle, array $haystack)
	{
		$key = array_search($needle, $haystack, true);

		if ($key !== false)
		{
			unset($haystack[$key]);
			$haystack = array_values($haystack);
		}

		return $haystack;
	}

	private static function getMethods(test $test, $testClass, array $runTestMethods, array $tags)
	{
		$methods = array();

		if (isset($runTestMethods['*']) === true)
		{
			$methods = $runTestMethods['*'];
		}

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

?>
