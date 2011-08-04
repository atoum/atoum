<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

abstract class test implements observable, adapter\aggregator, \countable
{
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

	private $phpPath = null;
	private $path = '';
	private $class = '';
	private $adapter = null;
	private $asserterGenerator = null;
	private $score = null;
	private $observers = array();
	private $ignore = false;
	private $testMethods = array();
	private $runTestMethods = array();
	private $currentMethod = null;
	private $testsSubNamespace = null;
	private $mockGenerator = null;
	private $workingFile = null;
	private $child = null;
	private $testsToRun = 0;
	private $numberOfChildren = 0;
	private $maxChildrenNumber = 1;

	public function __construct(score $score = null, locale $locale = null, adapter $adapter = null)
	{
		$this
			->setScore($score ?: new score())
			->setLocale($locale ?: new locale())
			->setAdapter($adapter ?: new adapter())
			->setSuperglobals(new atoum\superglobals())
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

		$this->getAsserterGenerator()
			->setAlias('array', 'phpArray')
			->setAlias('class', 'phpClass')
		;

		foreach (new annotations\extractor($class->getDocComment()) as $annotation => $value)
		{
			switch ($annotation)
			{
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

	public function __destruct()
	{
	}

	public function __toString()
	{
		return $this->getClass();
	}

	public function __get($property)
	{
		switch ($property)
		{
			case 'define':
				return $this->getAsserterGenerator();

			case 'assert':
				return $this->getAsserterGenerator()->resetAsserters();

			case 'mockGenerator':
				return $this->getMockGenerator();

			default:
				throw new exceptions\logic\invalidArgument('Property \'' . $property . '\' is undefined in class \'' . get_class($this) . '\'');
		}
	}

	public function __call($method, $arguments)
	{
		switch ($method)
		{
			case 'mock':
				$this->getMockGenerator()->generate(isset($arguments[0]) === false ? null : $arguments[0], isset($arguments[1]) === false ? null : $arguments[1], isset($arguments[2]) === false ? null : $arguments[2]);
				return $this;

			case 'assert':
				$case = isset($arguments[0]) === false ? null : $arguments[0];

				if ($case !== null)
				{
					$this->startCase($case);
				}

				return $this->getAsserterGenerator()->resetAsserters();

			default:
				throw new exceptions\logic\invalidArgument('Method ' . get_class($this) . '::' . $method . '() is undefined');
		}
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

	public function setSuperglobals(atoum\superglobals $superglobals)
	{
		$this->superglobals = $superglobals;

		return $this;
	}

	public function getSuperglobals()
	{
		return $this->superglobals;
	}

	public function setMockGenerator(mock\generator $generator)
	{
		$this->mockGenerator = $generator;

		return $this;
	}

	public function getMockGenerator()
	{
		return $this->mockGenerator ?: $this->setMockGenerator(new mock\generator())->mockGenerator;
	}

	public function setAsserterGenerator(asserter\generator $generator)
	{
		$this->asserterGenerator = $generator->setTest($this);

		return $this;
	}

	public function getAsserterGenerator()
	{
		return $this->asserterGenerator ?: $this->setAsserterGenerator(new asserter\generator($this, $this->locale))->asserterGenerator;
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

	public function setPhpPath($path)
	{
		$this->phpPath = (string) $path;

		return $this;
	}

	public function getPhpPath()
	{
		if ($this->phpPath === null)
		{
			if (isset($this->superglobals->_SERVER['_']) === false)
			{
				throw new exceptions\runtime('Unable to find PHP executable');
			}

			$this->setPhpPath($this->superglobals->_SERVER['_']);
		}

		return $this->phpPath;
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

		$this->testsToRun = sizeof($this->runTestMethods);

		if ($this->testsToRun > 0)
		{
			if ($runner === null)
			{
				foreach ($this->runTestMethods as $testMethodName)
				{
					$this->runTestMethod($testMethodName);
				}
			}
			else
			{
				try
				{
					$this->workingFile = $this->adapter->tempnam($this->adapter->sys_get_temp_dir(), 'atm');

					$this->callObservers(self::beforeSetUp);
					$this->setUp();
					$this->callObservers(self::afterSetUp);

					$phpCode =
						'<?php ' .
						'define(\'' . __NAMESPACE__ . '\scripts\runner\autorun\', false);' .
						'require(\'' . $runner->getPath() . '\');' .
						'require(\'' . $this->path . '\');' .
						'$locale = new ' . get_class($this->locale) . '(' . $this->locale->get() . ');' .
						'$runner = new ' . $runner->getClass() . '();' .
						'$runner->setLocale($locale);' .
						'$runner->setPhpPath(\'' . $this->getPhpPath() . '\');' .
						($runner->codeCoverageIsEnabled() === true ? '' : '$runner->disableCodeCoverage();') .
						'$runner->run(array(\'' . $this->class . '\'), array(\'' . $this->class . '\' => array(\'%s\')), false);' .
						'file_put_contents(\'%s\', serialize($runner->getScore()));' .
						'?>'
					;

					$failNumber = 0;
					$errorNumber = 0;
					$exceptionNumber = 0;
					$children = array();
					$stdOut = array();
					$stdErr = array();
					$pipes = array();
					$null = null;

					while ($this->testsToRun > 0 || sizeof($children) > 0)
					{
						while (($child = $this->getChild()) !== null)
						{
							$this->currentMethod = array_shift($this->runTestMethods);

							$this->callObservers(self::beforeTestMethod);

							fwrite($child[1][0], sprintf($phpCode, $this->currentMethod, $child[2]));
							fclose($child[1][0]);
							unset($child[1][0]);


							$pipes[] = $child[1][1];
							$pipes[] = $child[1][2];

							$children[$this->currentMethod] = $child;
							$stdOut[$this->currentMethod] = '';
							$stdErr[$this->currentMethod] = '';

							$this->testsToRun--;

							if ($this->testsToRun > 0)
							{
								$this->createChild();
							}
						}

						$numberOfChildren = $this->numberOfChildren;

						while ($this->numberOfChildren === $numberOfChildren)
						{
							if (stream_select($pipes, $null, $null, null) > 0)
							{
								foreach ($pipes as $writedPipe)
								{
									foreach ($children as $testMethod => $child)
									{
										switch (true)
										{
											case isset($child[1][2]) && $writedPipe === $child[1][2]:
												$stdErr[$testMethod] = stream_get_contents($child[1][2]);

												if (feof($child[1][2]) === true)
												{
													fclose($child[1][2]);
													unset($children[$testMethod][1][2]);
												}
												break;

											case isset($child[1][1]) && $writedPipe === $child[1][1]:
												$stdOut[$testMethod] = stream_get_contents($child[1][1]);

												if (feof($child[1][1]) === true)
												{
													fclose($child[1][1]);
													unset($children[$testMethod][1][1]);
												}
												break;
										}
									}
								}

								foreach (array_filter($children, function($child) { return isset($child[1][1]) === false && isset($child[1][2]) === false; }) as $testMethod => $terminatedChild)
								{
									$this->currentMethod = $testMethod;

									$this->callObservers(self::afterTestMethod);

									$returnValue = proc_close($terminatedChild[0]);

									if ($stdOut[$testMethod] !== '')
									{
										$this->score->addOutput($this->class, $testMethod, $stdOut[$testMethod]);
									}

									if ($stdErr[$testMethod] != '')
									{
										if (preg_match_all('/([^:]+): (.+) in (.+) on line ([0-9]+)/', trim($stdErr[$testMethod]), $errors, PREG_SET_ORDER) === 0)
										{
											$this->score->addError($this->path, null, $this->class, $testMethod, 'UNKNOWN', $stdErr[$testMethod]);
										}
										else
										{
											foreach ($errors as $error)
											{
												$this->score->addError($this->path, null, $this->class, $testMethod, $error[1], $error[2], $error[3], $error[4]);
											}
										}
									}

									$tmpFileContent = @file_get_contents($terminatedChild[2]);

									if ($tmpFileContent !== false)
									{
										$score = @unserialize($tmpFileContent);

										if ($score instanceof score)
										{
											$this->score->merge($score);
										}
									}

									@unlink($terminatedChild[3]);

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

									unset($stdOut[$testMethod]);
									unset($stdErr[$testMethod]);

									$this->numberOfChildren--;
								}

								$children = array_filter($children, function($child) { return isset($child[1][1]) === true && isset($child[1][2]) === true; });

								$pipes = array();

								foreach ($children as $child)
								{
									if (isset($child[1][1]) === true)
									{
										$pipes[] = $child[1][1];
									}

									if (isset($child[1][2]) === true)
									{
										$pipes[] = $child[1][2];
									}
								}
							}
						}
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

	protected function setUp()
	{
		return $this;
	}

	protected function startCase($case)
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
		ini_set('log_errors_max_len', '0');

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
					->addOutput($this->class, $this->currentMethod, ob_get_clean())
				;

				$this->afterTestMethod($testMethod);
			}
			catch (\exception $exception)
			{
				$this->score->addOutput($this->class, $this->currentMethod, ob_get_clean());
				$this->score->addDuration($this->class, $this->currentMethod, microtime(true) - $time);

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

	private function createChild()
	{
		$php = proc_open(
			$this->getPhpPath(),
			array(
				0 => array('pipe', 'r'),
				1 => array('pipe', 'w'),
				2 => array('pipe', 'w')
			),
			$pipes
		);

		$this->child = array($php, $pipes, $this->adapter->tempnam($this->adapter->sys_get_temp_dir(), 'atm'));

		return $this;
	}

	private function getChild()
	{
		$child = null;

		if ($this->testsToRun > 0 && $this->numberOfChildren < $this->maxChildrenNumber)
		{
			if ($this->child === null)
			{
				$this->createChild();
			}

			$child = $this->child;

			$this->numberOfChildren++;
		}

		return $child;
	}
}

?>
