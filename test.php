<?php

namespace mageekguy\atoum;

use mageekguy\atoum;
use mageekguy\atoum\asserter;

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

	protected $score = null;
	protected $assert = null;
	protected $observers = array();
	protected $isolation = true;
	protected $ignore = false;

	private $class = '';
	private $path = '';
	private $testMethods = array();
	private $runTestMethods = array();
	private $currentMethod = null;

	public function __construct(score $score = null, locale $locale = null)
	{
		if ($score === null)
		{
			$score = new score();
		}

		$this->setScore($score);

		if ($locale === null)
		{
			$locale = new locale();
		}

		$this->setLocale($locale);

		$this->assert = new asserter($this->score, $this->locale);

		$class = new \reflectionClass($this);

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

		$this->class = $class->getName();

		$this->path = $class->getFilename();

		foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $publicMethod)
		{
			$methodName = $publicMethod->getName();

			if (strpos($methodName, self::testMethodPrefix) === 0)
			{
				$annotations = array(
					'isolation' => $this->isolation
				);

				foreach (new annotations\extractor($publicMethod->getDocComment()) as $annotation => $value)
				{
					switch ($annotation)
					{
						case '@isolation':
							$annotations['isolation'] = $value == 'on';
							break;
					}
				}

				$this->testMethods[$methodName] = $annotations;
			}
		}

		$this->runTestMethods = $this->getTestMethods();
	}

	public function __destruct()
	{
		atoum\asserter::resetAliases();
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
		return array_keys($this->testMethods);
	}

	public function getCurrentMethod()
	{
		return $this->currentMethod;
	}

	public function ignore($boolean)
	{
		$this->ignore = ($boolean == true);
		return $this;
	}

	public function isIgnored()
	{
		return ($this->ignore === true);
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

	public function run(array $runTestMethods = array(), $runInChildProcess = true)
	{
		if ($this->ignore === false)
		{
			$this->callObservers(self::runStart);

			if (sizeof($runTestMethods) > 0)
			{
				$this->runTestMethods = $runTestMethods;
			}

			try
			{
				if ($runInChildProcess === true)
				{
					$this->callObservers(self::beforeSetUp);
					$this->setUp();
					$this->callObservers(self::afterSetUp);
				}

				foreach ($this->runTestMethods as $testMethodName)
				{
					if (isset($this->testMethods[$testMethodName]) === false)
					{
						throw new \runtimeException('Test method ' . $this->class . '::' . $testMethodName . '() is undefined');
					}

					$failNumber = $this->score->getFailNumber();
					$errorNumber = $this->score->getErrorNumber();
					$exceptionNumber = $this->score->getExceptionNumber();

					$this->currentMethod = $testMethodName;

					$this->callObservers(self::beforeTestMethod);

					if ($runInChildProcess === false || $this->testMethods[$testMethodName]['isolation'] === false)
					{
						$this->runTestMethod($testMethodName);
					}
					else
					{
						$this->runInChildProcess($testMethodName);
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

				if ($runInChildProcess === true)
				{
					$this->callObservers(self::beforeTearDown);
					$this->tearDown();
					$this->callObservers(self::afterTearDown);
				}
			}
			catch (\exception $exception)
			{
				$this->tearDown();
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
			$this->score->addError($file, $line, $this->class, $this->currentMethod, $errno, $errstr);
		}

		return true;
	}

	public static function getVersion()
	{
		return substr(self::version, 6, -2);
	}

	protected function setUp()
	{
		return $this;
	}

	protected function runTestMethod($testMethod)
	{
		set_error_handler(array($this, 'errorHandler'));

		try
		{
			try
			{
				ob_start();
				$time = microtime(true);
				$memory = memory_get_usage(true);
				$this->{$testMethod}();
				$this->score->addMemoryUsage($this->class, $this->currentMethod, memory_get_usage(true) - $memory);
				$this->score->addDuration($this->class, $this->currentMethod, microtime(true) - $time);
				$this->score->addOutput($this->class, $this->currentMethod, ob_get_contents());
				ob_end_clean();
			}
			catch (\exception $exception)
			{
				$this->score->addOutput($this->class, $this->currentMethod, ob_get_contents());
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

		return $this;
	}

	protected function runInChildProcess($testMethod)
	{
		$phpCode = '<?php define(\'' . __NAMESPACE__ . '\runners\autorun\', false); require(\'' . $this->getPath() . '\'); $unit = new ' . $this->class . '; $unit->run(array(\'' . $testMethod . '\'), false); echo serialize($unit->getScore()); ?>';

		$descriptors = array
			(
				0 => array('pipe', 'r'),
				1 => array('pipe', 'w'),
				2 => array('pipe', 'w')
			);

		$php = proc_open($_SERVER['_'], $descriptors, $pipes);

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
				foreach (explode("\n", trim($stdErr)) as $error)
				{
					$file = null;
					$line = null;
					$message = $error;

					if (preg_match('/^(.*?) in ([^ ]+) on line (.*)$/', $error, $match) === true)
					{
						$file = $match[2];
						$line = $match[3];
						$message = $message[1];
					}

					$this->score->addError($file, $line, $this->class, $this->currentMethod, $returnValue, $message);
				}
			}

			if ($stdOut !== '')
			{
				$score = unserialize($stdOut);

				if ($score instanceof score === false)
				{
					throw new atoum\exception('Unable to retrieve score from \'' . $stdOut . '\'');
				}

				$this->score->merge($score);
			}
		}
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

	protected function testMethodExists($testMethodName)
	{
		foreach ($this->testMethods as $testMethod)
		{
			if ($testMethod['name'] == $testMethodName)
			{
				return true;
			}
		}

		return false;
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
