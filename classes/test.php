<?php

namespace mageekguy\atoum;

use
	\mageekguy\atoum,
	\mageekguy\atoum\asserter,
	\mageekguy\atoum\exceptions
;

abstract class test implements observable, \countable
{
	const version = '$Rev$';
	const author = 'Frédéric Hardy';
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
	const defaultTestsSubNamespace = 'tests\units';

	private $php = null;
	private $path = '';
	private $class = '';
	private $adapter = null;
	private $asserterGenerator = null;
	private $score = null;
	private $observers = array();
	private $isolation = true;
	private $ignore = false;
	private $testMethods = array();
	private $runTestMethods = array();
	private $currentMethod = null;
	private $testsSubNamespace = null;

	public static $runningTest = null;

	public function __construct(score $score = null, locale $locale = null, adapter $adapter = null)
	{
		if ($score === null)
		{
			$score = new score();
		}

		if ($locale === null)
		{
			$locale = new locale();
		}

		if ($adapter === null)
		{
			$adapter = new adapter();
		}

		$this
			->setScore($score)
			->setLocale($locale)
			->setAdapter($adapter)
		;

		$class = new \reflectionClass($this);

		$this->class = $class->getName();

		$this->path = $class->getFilename();

		$testedClassName = $this->getTestedClassName();

		if ($testedClassName === null)
		{
			throw new exceptions\runtime('Test class \'' . $this->getClass() . '\' is not in a namespace which contains \'' . $this->getTestsSubNamespace() . '\'');
		}

		if ($this->adapter->class_exists($testedClassName) === false)
		{
			throw new exceptions\runtime('Tested class \'' . $testedClassName . '\' does not exist for test class \'' . $this->getClass() . '\'');
		}

		$this->asserterGenerator = new asserter\generator($this, $this->locale);

		$this->assert
			->setAlias('array', 'phpArray')
			->setAlias('class', 'phpClass')
		;

		foreach (new annotations\extractor($class->getDocComment()) as $annotation => $value)
		{
			switch ($annotation)
			{
				case 'isolation':
					$this->isolation = $value == 'on';
					break;

				case 'ignore':
					$this->ignore = $value == 'on';
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
						case 'isolation':
							$annotations['isolation'] = $value == 'on';
							break;

						case 'ignore':
							$annotations['ignore'] = $value == 'on';
							break;
					}
				}

				$this->testMethods[$methodName] = $annotations;
			}
		}

		$this->runTestMethods = $this->getTestMethods();
	}

	public function __get($property)
	{
		switch ($property)
		{
			case 'assert':
			case 'define':
				return $this->asserterGenerator;

			default:
				throw new exceptions\logic\invalidArgument('Property \'' . $property . '\' is undefined in class \'' . get_class($this) . '\'');
		}
	}

	public function setTestsSubNamespace($testsSubNamespace)
	{
		$this->testsSubNamespace = trim((string) $testsSubNamespace, '\\');

		if ($this->testsSubNamespace === '')
		{
			throw new atoum\exceptions\logic\invalidArgument('Tests sub-namespace must not be empty');
		}

		return $this;
	}

	public function getTestsSubNamespace()
	{
		return ($this->testsSubNamespace === null ? self::defaultTestsSubNamespace : $this->testsSubNamespace);
	}

	public function setPhp($path)
	{
		$this->php = (string) $path;

		return $this;
	}

	public function getPhp()
	{
		if ($this->php === null)
		{
			if (isset($_SERVER['_']) === false)
			{
				throw new exceptions\runtime('Unable to find PHP executable');
			}

			$this->setPhp($_SERVER['_']);
		}

		return $this->php;
	}

	public function setAdapter(atoum\adapter $adapter)
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
		$testsSubNamespace = $this->getTestsSubNamespace();

		$position = strpos($testClass, $testsSubNamespace);

		if ($position !== false)
		{
			$class = trim(substr($testClass, 0, $position) . substr($testClass, $position + strlen($testsSubNamespace) + 1), '\\');
		}

		return $class;
	}

	public function getClass()
	{
		return $this->class;
	}

	public function getPath()
	{
		return $this->path;
	}

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

	public function getCurrentMethod()
	{
		return $this->currentMethod;
	}

	public function count()
	{
		return sizeof($this->runTestMethods);
	}

	public function addObserver(atoum\observers\test $observer)
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

		$this->runTestMethods = $this->getTestMethods();

		return $this;
	}

	public function isIgnored()
	{
		return ($this->ignore === true);
	}

	public function methodIsIgnored($testMethodName)
	{
		if (isset($this->testMethods[$testMethodName]) === false)
		{
			throw new exceptions\logic\invalidArgument('Test method ' . $this->class . '::' . $testMethodName . '() is unknown');
		}

		return (isset($this->testMethods[$testMethodName]['ignore']) === true ? $this->testMethods[$testMethodName]['ignore'] : $this->ignore);
	}

	public function isolate($boolean)
	{
		$this->isolation = ($boolean == true);
		return $this;
	}

	public function isIsolated()
	{
		return ($this->isolation === true);
	}

	public function run(array $runTestMethods = array(), atoum\runner $runner = null)
	{
		$this->callObservers(self::runStart);

		if (sizeof($runTestMethods) > 0)
		{
			$unknownTestMethods = array_diff($runTestMethods, $this->getTestMethods());

			if (sizeof($unknownTestMethods) > 0)
			{
				throw new exceptions\logic\invalidArgument('Test method ' . $this->class . '::' . current($unknownTestMethods) . '() is unknown or ignored');
			}

			$this->runTestMethods = $runTestMethods;
		}

		if (sizeof($this->runTestMethods) > 0)
		{
			try
			{
				if ($runner !== null)
				{
					$this->callObservers(self::beforeSetUp);
					$this->setUp();
					$this->callObservers(self::afterSetUp);
				}

				foreach ($this->runTestMethods as $testMethodName)
				{
					$failNumber = $this->score->getFailNumber();
					$errorNumber = $this->score->getErrorNumber();
					$exceptionNumber = $this->score->getExceptionNumber();

					$this->currentMethod = $testMethodName;

					$this->callObservers(self::beforeTestMethod);

					if ($runner === null || (isset($this->testMethods[$testMethodName]['isolation']) === false ? $this->isolation : $this->testMethods[$testMethodName]['isolation']) === false)
					{
						$this->runTestMethod($testMethodName);
					}
					else
					{
						$this->runInChildProcess($testMethodName, $runner);
					}

					switch (true)
					{
						case $failNumber < $this->score->getFailNumber():
							$this->callObservers(self::fail);
							break;

						case $errorNumber < $this->score->getErrorNumber():
							$this->callObservers(self::error);
							break;

						case $exceptionNumber < $this->score->getExceptionNumber():
							$this->callObservers(self::exception);
							break;

						default:
							$this->callObservers(self::success);
					}

					$this->callObservers(self::afterTestMethod);

					$this->currentMethod = null;
				}

				if ($runner !== null)
				{
					$this
						->callObservers(self::beforeTearDown)
						->tearDown()
						->callObservers(self::afterTearDown)
					;
				}
			}
			catch (\exception $exception)
			{
				$this
					->callObservers(self::exception)
					->callObservers(self::runStop)
					->callObservers(self::beforeTearDown)
					->tearDown()
					->callObservers(self::afterTearDown)
					->addExceptionToScore($exception)
				;
			}
		}

		$this->callObservers(self::runStop);

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

	public static function getVersion()
	{
		return preg_replace('/\$Rev: (\d+) \$/', '$1', self::version);
	}

	protected function setUp()
	{
		return $this;
	}

	protected function setCase($case)
	{
		$this->score->setCase($case);

		return $this;
	}

	protected function beforeTestMethod($testMethod)
	{
		return $this;
	}

	protected function runTestMethod($testMethod)
	{
		set_error_handler(array($this, 'errorHandler'));

		ini_set('display_errors', 'stderr');
		ini_set('log_errors', 'Off');

		try
		{
			try
			{
				$this->beforeTestMethod($testMethod);

				ob_start();

				$time = microtime(true);
				$memory = memory_get_usage(true);

				$this->{$testMethod}();

				$this->score
					->addMemoryUsage($this->class, $this->currentMethod, memory_get_usage(true) - $memory)
					->addDuration($this->class, $this->currentMethod, microtime(true) - $time)
					->addOutput($this->class, $this->currentMethod, ob_get_contents())
				;

				ob_end_clean();

				$this->afterTestMethod($testMethod);
			}
			catch (\exception $exception)
			{
				$this->score->addOutput($this->class, $this->currentMethod, ob_get_contents());
				$this->score->addDuration($this->class, $this->currentMethod, microtime(true) - $time);
				ob_end_clean();

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

		restore_error_handler();

		ini_restore('display_errors');
		ini_restore('log_errors');

		return $this;
	}

	protected function runInChildProcess($testMethod, atoum\runner $runner)
	{
		$php = $this->getPhp();

		$tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5($this->currentMethod);

		$phpCode  = '<?php ';
		$phpCode .= 'define(\'' . __NAMESPACE__ . '\scripts\runner\autorun\', false);';
		$phpCode .= 'require(\'' . $runner->getPath() . '\');';
		$phpCode .= 'require(\'' . $this->path . '\');';
		$phpCode .= '$runner = new ' . $runner->getClass() . '();';
		$phpCode .= '$runner->setPhp(\'' . $php . '\')';

		if ($runner->codeCoverageIsEnabled() === false)
		{
			$phpCode .= '$runner->disableCodeCoverage();';
		}

		$phpCode .= '$runner->run(array(\'' . $this->class . '\'), array(\'' . $this->class . '\' => array(\'' . $testMethod . '\')), false);';
		$phpCode .= 'file_put_contents(\'' . $tmpFile . '\', serialize($runner->getScore()));';
		$phpCode .= '?>';

		$descriptors = array
			(
				0 => array('pipe', 'r'),
				1 => array('pipe', 'w'),
				2 => array('pipe', 'w')
			);

		$php = proc_open($php, $descriptors, $pipes);

		if ($php !== false)
		{
			fwrite($pipes[0], $phpCode);
			fclose($pipes[0]);

			$stdOut = stream_get_contents($pipes[1]);
			fclose($pipes[1]);

			$stdErr = stream_get_contents($pipes[2]);
			fclose($pipes[2]);

			$returnValue = proc_close($php);

			if ($stdErr != '')
			{
				$this->extractError($stdErr, $returnValue);
			}

			if ($stdOut !== '')
			{
				$this->extractError($stdOut, $returnValue);
			}

			if (is_file($tmpFile) === true && is_writable($tmpFile) === true)
			{
				$tmpFileContent = file_get_contents($tmpFile);

				$score = @unserialize($tmpFileContent);

				if ($score instanceof score)
				{
					$this->score->merge($score);
				}
				else
				{
					$this->score->addError($this->path, null, $this->class, $this->currentMethod, $returnValue, 'Unable to retrieve score for ' . $this->currentMethod . ': ' . $tmpFileContent);
				}

				unlink($tmpFile);
			}
		}
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
				return array(
					$debugBacktrace[$key - 1]['file'],
					$debugBacktrace[$key - 1]['line']
				);
			}
		}

		return null;
	}

	protected function extractError($string, $returnValue)
	{
		$error = trim($string);

		if ($error !== '')
		{
			if (preg_match('/ in (.+) on line (\d+)/', $error, $match) === 1)
			{
				$this->score->addError($this->path, null, $this->class, $this->currentMethod, $returnValue, preg_replace('/ in (.+) on line (\d+)/', '', $error), $match[1], $match[2]);
			}
			else
			{
				$this->score->addError($this->path, null, $this->class, $this->currentMethod, $returnValue, $error);
			}
		}

		return $this;
	}

	protected static function getAnnotations($comments)
	{
		$annotations = array();

		$comments = explode("\n", trim(trim($comments, '/*')));

		foreach ($comments as $comment)
		{
			$comment = preg_split("/\s+/", trim($comment));

			if (sizeof($comment) == 2)
			{
				if (substr($comment[0], 0, 1) == '@')
				{
					$annotations[$comment[0]] = $comment[1];
				}
			}
		}

		return $annotations;
	}
}

?>
