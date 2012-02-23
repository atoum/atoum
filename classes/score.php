<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\exceptions
;

class score
{
	private $passAssertions = 0;
	private $failAssertions = array();
	private $exceptions = array();
	private $runtimeExceptions = array();
	private $errors = array();
	private $outputs = array();
	private $durations = array();
	private $memoryUsages = array();
	private $coverage = null;
	private $uncompletedTests = array();
	private $case = null;
	private $dataSetKey = null;
	private $dataSetProvider = null;
	private $phpPath = null;
	private $phpVersion = null;
	private $atoumPath = null;
	private $atoumVersion = null;
	private $incomptedTests = array();

	private static $failId = 0;

	public function __construct(score\coverage $coverage = null)
	{
		$this->coverage = $coverage ?: new score\coverage();
	}

	public function reset()
	{
		$this->phpPath = null;
		$this->phpVersion = null;
		$this->atoumPath = null;
		$this->atoumVersion = null;
		$this->passAssertions = 0;
		$this->failAssertions = array();
		$this->exceptions = array();
		$this->errors = array();
		$this->outputs = array();
		$this->durations = array();
		$this->memoryUsages = array();

		return $this;
	}

	public function setAtoumPath($path)
	{
		if ($this->atoumPath !== null)
		{
			throw new exceptions\runtime('Path of atoum is already set');
		}

		$this->atoumPath = (string) $path;

		return $this;
	}

	public function setAtoumVersion($version)
	{
		if ($this->atoumVersion !== null)
		{
			throw new exceptions\runtime('Version of atoum is already set');
		}

		$this->atoumVersion = (string) $version;

		return $this;
	}

	public function setPhpPath($path)
	{
		if ($this->phpPath !== null)
		{
			throw new exceptions\runtime('PHP path is already set');
		}

		$this->phpPath = (string) $path;

		return $this;
	}

	public function setPhpVersion($version)
	{
		if ($this->phpVersion !== null)
		{
			throw new exceptions\runtime('PHP version is already set');
		}

		$this->phpVersion = (string) $version;

		return $this;
	}

	public function addPass()
	{
		$this->passAssertions++;

		return $this;
	}

	public function addFail($file, $line, $class, $method, $asserter, $reason)
	{
		$this->failAssertions[] = array(
			'id' => ++self::$failId,
			'case' => $this->case,
			'dataSetKey' => $this->dataSetKey,
			'dataSetProvider' => $this->dataSetProvider,
			'class' => $class,
			'method' => $method,
			'file' => $file,
			'line' => $line,
			'asserter' => $asserter,
			'fail' => $reason
		);

		return self::$failId;
	}

	public function addException($file, $line, $class, $method, \exception $exception)
	{
		$this->exceptions[] = array(
			'case' => $this->case,
			'dataSetKey' => $this->dataSetKey,
			'dataSetProvider' => $this->dataSetProvider,
			'class' => $class,
			'method' => $method,
			'file' => $file,
			'line' => $line,
			'value' => (string) $exception
		);

		return $this;
	}

	public function addRuntimeException(test\exceptions\runtime $exception)
	{
		$this->runtimeExceptions[] = $exception;

		return $this;
	}

	public function addError($file, $line, $class, $method, $type, $message, $errorFile = null, $errorLine = null)
	{
		$this->errors[] = array(
			'case' => $this->case,
			'dataSetKey' => $this->dataSetKey,
			'dataSetProvider' => $this->dataSetProvider,
			'class' => $class,
			'method' => $method,
			'file' => $file,
			'line' => $line,
			'type' => $type,
			'message' => trim($message),
			'errorFile' => $errorFile,
			'errorLine' => $errorLine
		);

		return $this;
	}

	public function addOutput($class, $method, $output)
	{
		if ($output != '')
		{
			$this->outputs[] = array(
				'class' => $class,
				'method' => $method,
				'value' => $output
			);
		}

		return $this;
	}

	public function addDuration($class, $path, $method, $duration)
	{
		if ($duration > 0)
		{
			$this->durations[] = array(
				'case' => $this->case,
				'class' => $class,
				'path' => $path,
				'method' => $method,
				'value' => $duration
			);
		}

		return $this;
	}

	public function addMemoryUsage($class, $method, $memoryUsage)
	{
		if ($memoryUsage > 0)
		{
			$this->memoryUsages[] = array(
				'case' => $this->case,
				'class' => $class,
				'method' => $method,
				'value' => $memoryUsage
			);
		}

		return $this;
	}

	public function addUncompletedTest($class, $method, $exitCode, $output)
	{
		$this->uncompletedTests[] = array(
			'class' => $class,
			'method' => $method,
			'exitCode' => $exitCode,
			'output' => $output
		);

		return $this;
	}

	public function merge(score $score)
	{
		$this->passAssertions += $score->passAssertions;
		$this->failAssertions = array_merge($this->failAssertions, $score->failAssertions);
		$this->exceptions = array_merge($this->exceptions, $score->exceptions);
		$this->errors = array_merge($this->errors, $score->errors);
		$this->outputs = array_merge($this->outputs, $score->outputs);
		$this->durations = array_merge($this->durations, $score->durations);
		$this->memoryUsages = array_merge($this->memoryUsages, $score->memoryUsages);
		$this->coverage->merge($score->coverage);
		$this->uncompletedTests = array_merge($this->uncompletedTests, $score->uncompletedTests);

		return $this;
	}

	public function getAtoumPath()
	{
		return $this->atoumPath;
	}

	public function getAtoumVersion()
	{
		return $this->atoumVersion;
	}

	public function getPhpPath()
	{
		return $this->phpPath;
	}

	public function getPhpVersion()
	{
		return $this->phpVersion;
	}

	public function getOutputs()
	{
		return array_values($this->outputs);
	}

	public function getTotalDuration()
	{
		$total = 0.0;

		foreach ($this->durations as $duration)
		{
			$total += $duration['value'];
		}

		return $total;
	}

	public function getDurations()
	{
		return array_values($this->durations);
	}

	public function getTotalMemoryUsage()
	{
		$total = 0.0;

		foreach ($this->memoryUsages as $memoryUsage)
		{
			$total += $memoryUsage['value'];
		}

		return $total;
	}

	public function getMemoryUsages()
	{
		return array_values($this->memoryUsages);
	}

	public function getFailAssertions()
	{
		return self::sort(self::cleanAssertions($this->failAssertions));
	}

	public function getErrors()
	{
		return self::sort($this->errors);
	}

	public function getExceptions()
	{
		return self::sort($this->exceptions);
	}

	public function getRuntimeExceptions()
	{
		return $this->runtimeExceptions;
	}

	public function getDurationNumber()
	{
		return sizeof($this->durations);
	}

	public function getOutputNumber()
	{
		return sizeof($this->outputs);
	}

	public function getAssertionNumber()
	{
		return ($this->passAssertions + sizeof($this->failAssertions));
	}

	public function getPassNumber()
	{
		return ($this->getAssertionNumber() - sizeof($this->getFailAssertions()));
	}

	public function getExceptionNumber()
	{
		return sizeof($this->exceptions);
	}

	public function getRuntimeExceptionNumber()
	{
		return sizeof($this->runtimeExceptions);
	}

	public function getMemoryUsageNumber()
	{
		return sizeof($this->memoryUsages);
	}

	public function getFailNumber()
	{
		return sizeof($this->getFailAssertions());
	}

	public function getErrorNumber()
	{
		return sizeof($this->errors);
	}

	public function getUncompletedTestNumber()
	{
		return sizeof($this->uncompletedTests);
	}

	public function getCoverage()
	{
		return $this->coverage;
	}

	public function getUncompletedTests()
	{
		return $this->uncompletedTests;
	}

	public function getCase()
	{
		return $this->case;
	}

	public function getDataSetKey()
	{
		return $this->dataSetKey;
	}

	public function getDataSetProvider()
	{
		return $this->dataSetProvider;
	}

	public function getMethodsWithFail()
	{
		return self::getMethods($this->getFailAssertions());
	}

	public function getMethodsWithError()
	{
		return self::getMethods($this->getErrors());
	}

	public function getMethodsWithException()
	{
		return self::getMethods($this->getExceptions());
	}

	public function setCase($case)
	{
		$this->case = (string) $case;

		return $this;
	}

	public function setDataSet($key, $dataProvider)
	{
		$this->dataSetKey = $key;
		$this->dataSetProvider = $dataProvider;

		return $this;
	}

	public function unsetCase()
	{
		$this->case = null;

		return $this;
	}

	public function unsetDataSet()
	{
		$this->dataSetKey = null;
		$this->dataSetProvider = null;

		return $this;
	}

	public function errorExists($message = null, $type = null, $messageIsPattern = false)
	{
		$messageIsNull = $message === null;
		$typeIsNull = $type === null;

		foreach ($this->errors as $key => $error)
		{
			$messageMatch = $messageIsNull === true ? true : ($messageIsPattern == false ? $message == $error['message'] : preg_match($message, $error['message']) == 1);
			$typeMatch = $typeIsNull === true ? true : $error['type'] == $type;

			if ($messageMatch === true && $typeMatch === true)
			{
				return $key;
			}
		}

		return null;
	}

	public function deleteError($key)
	{
		if (isset($this->errors[$key]) === false)
		{
			throw new exceptions\logic\invalidArgument('Error key \'' . $key . '\' does not exist');
		}

		unset($this->errors[$key]);

		return $this;
	}

	public function failExists(atoum\asserter\exception $exception)
	{
		$id = $exception->getCode();

		return (sizeof(array_filter($this->failAssertions, function($assertion) use ($id) { return ($assertion['id'] === $id); })) > 0);
	}

	private static function getMethods(array $array)
	{
		$methods = array();

		foreach ($array as $value)
		{
			if (isset($methods[$value['class']]) === false || in_array($value['method'], $methods[$value['class']]) === false)
			{
				$methods[$value['class']][] = $value['method'];
			}
		}

		return $methods;
	}

	private static function cleanAssertions(array $assertions)
	{
		return array_map(function ($assertion) { unset($assertion['id']); return $assertion; }, array_values($assertions));
	}

	private static function sort(array $array)
	{
		usort($array, function($a, $b) {
				switch (true)
				{
					case $a['file'] === $b['file'] && $a['line'] === $b['line']:
						return 0;

					case $a['file'] === $b['file']:
						return $a['line'] < $b['line'] ? -1 : 1;

					default:
						return strcmp($a['file'], $b['file']);
				}
			}
		);

		return $array;
	}
}

?>
