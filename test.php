<?php

namespace mageekguy\atoum;

use mageekguy\atoum;
use mageekguy\atoum\asserter;

abstract class test implements observable, \countable
{
	const version = '$Rev$';
	const author = 'Frédéric Hardy';
	const testMethodPrefix = 'test';

	const eventRunStart = 1;
	const eventBeforeSetUp = 2;
	const eventAfterSetUp = 3;
	const eventBeforeTestMethod = 4;
	const eventFailure = 5;
	const eventError = 6;
	const eventException = 7;
	const eventSuccess = 8;
	const eventAfterTestMethod = 9;
	const eventBeforeTearDown = 10;
	const eventAfterTearDown = 11;
	const eventRunEnd = 12;

	protected $score = null;
	protected $assert = null;
	protected $observers = array();
	protected $isolation = true;

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

	public function setScore(score $score)
	{
		$this->score = $score;
		return $this;
	}

	public function setLocale(locale $locale)
	{
		$this->locale = $locale;
		return $this;
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

	public function sendEventToObservers($event)
	{
		foreach ($this->observers as $observer)
		{
			$observer->manageObservableEvent($this, $event);
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

	public function getScore()
	{
		return $this->score;
	}

	public function getTestMethods()
	{
		return array_keys($this->testMethods);
	}

	public function getCurrentMethod()
	{
		return $this->currentMethod;
	}

	public function run(array $runTestMethods = array(), $runInChildProcess = true)
	{
		$this->sendEventToObservers(self::eventRunStart);

		if (sizeof($runTestMethods) > 0)
		{
			$this->runTestMethods = $runTestMethods;
		}

		try
		{
			if ($runInChildProcess === true)
			{
				$this->sendEventToObservers(self::eventBeforeSetUp);
				$this->setUp();
				$this->sendEventToObservers(self::eventAfterSetUp);
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

				$this->sendEventToObservers(self::eventBeforeTestMethod);

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
						$this->sendEventToObservers(self::eventFailure);
						break;

					case $errorNumber < $this->score->getErrorNumber():
						$this->sendEventToObservers(self::eventError);
						break;

					case $exceptionNumber < $this->score->getExceptionNumber():
						$this->sendEventToObservers(self::eventException);
						break;

					default:
						$this->sendEventToObservers(self::eventSuccess);
				}

				$this->sendEventToObservers(self::eventAfterTestMethod);

				$this->currentMethod = null;
			}

			if ($runInChildProcess === true)
			{
				$this->sendEventToObservers(self::eventBeforeTearDown);
				$this->tearDown();
				$this->sendEventToObservers(self::eventAfterTearDown);
			}
		}
		catch (\exception $exception)
		{
			$this->tearDown();
			throw $exception;
		}

		$this->sendEventToObservers(self::eventRunEnd);

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
			# Do nothing, just break execution of current method because an assertion failed.
		}
		catch (\exception $exception)
		{
			list($file, $line) = $this->getBacktrace($exception->getTrace());
			$this->score->addException($file, $line, $this->class, $this->currentMethod, $exception);
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
