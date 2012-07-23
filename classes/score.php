<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

class score
{
	protected $passNumber = 0;
	protected $failAssertions = array();
	protected $exceptions = array();
	protected $runtimeExceptions = array();
	protected $errors = array();
	protected $outputs = array();
	protected $durations = array();
	protected $memoryUsages = array();
	protected $voidMethods = array();
	protected $uncompletedMethods = array();
	protected $coverage = null;

	private static $failId = 0;

	public function __construct(dependencies $dependencies = null)
	{
		$this->setDependencies($dependencies ?: new atoum\dependencies());
	}

	public function setDependencies(dependencies $dependencies)
	{
		return $this->setCoverage(isset($dependencies['coverage']) === false ? new score\coverage($dependencies) : $dependencies['coverage']());
	}

	public function setCoverage(score\coverage $coverage)
	{
		$this->coverage = $coverage;

		return $this;
	}

	public function reset()
	{
		$this->passNumber = 0;
		$this->failAssertions = array();
		$this->exceptions = array();
		$this->runtimeExceptions = array();
		$this->errors = array();
		$this->outputs = array();
		$this->durations = array();
		$this->memoryUsages = array();
		$this->uncompletedMethods = array();
		$this->coverage->reset();

		return $this;
	}

	public function getPassNumber()
	{
		return $this->passNumber;
	}

	public function getRuntimeExceptions()
	{
		return $this->runtimeExceptions;
	}

	public function getVoidMethods()
	{
		return $this->voidMethods;
	}

	public function getUncompletedMethods()
	{
		return $this->uncompletedMethods;
	}

	public function getCoverage()
	{
		return $this->coverage;
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
		return ($this->passNumber + sizeof($this->failAssertions));
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

	public function getVoidMethodNumber()
	{
		return sizeof($this->voidMethods);
	}

	public function getUncompletedMethodNumber()
	{
		return sizeof($this->uncompletedMethods);
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

	public function getMethodsNotCompleted()
	{
		return self::getMethods($this->getUncompletedMethods());
	}

	public function addPass()
	{
		$this->passNumber++;

		return $this;
	}

	public function addFail($file, $class, $method, $line, $asserter, $reason, $case = null, $dataSetKey = null, $dataSetProvider = null)
	{
		$this->failAssertions[] = array(
			'id' => ++self::$failId,
			'case' => $case,
			'dataSetKey' => $dataSetKey,
			'dataSetProvider' => $dataSetProvider,
			'class' => $class,
			'method' => $method,
			'file' => $file,
			'line' => $line,
			'asserter' => $asserter,
			'fail' => $reason
		);

		return self::$failId;
	}

	public function addException($file, $class, $method, $line, \exception $exception, $case = null, $dataSetKey = null, $dataSetProvider = null)
	{
		$this->exceptions[] = array(
			'case' => $case,
			'dataSetKey' => $dataSetKey,
			'dataSetProvider' => $dataSetProvider,
			'class' => $class,
			'method' => $method,
			'file' => $file,
			'line' => $line,
			'value' => (string) $exception
		);

		return $this;
	}

	public function addRuntimeException($file, $class, $method, exceptions\runtime $exception)
	{
		$this->runtimeExceptions[] = $exception;

		return $this;
	}

	public function addError($file, $class, $method, $line, $type, $message, $errorFile = null, $errorLine = null, $case = null, $dataSetKey = null, $dataSetProvider = null)
	{
		$this->errors[] = array(
			'case' => $case,
			'dataSetKey' => $dataSetKey,
			'dataSetProvider' => $dataSetProvider,
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

	public function addOutput($file, $class, $method, $output)
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

	public function addDuration($file, $class, $method, $duration)
	{
		if ($duration > 0)
		{
			$this->durations[] = array(
				'class' => $class,
				'method' => $method,
				'value' => $duration,
				'path' => $file
			);
		}

		return $this;
	}

	public function addMemoryUsage($file, $class, $method, $memoryUsage)
	{
		if ($memoryUsage > 0)
		{
			$this->memoryUsages[] = array(
				'class' => $class,
				'method' => $method,
				'value' => $memoryUsage
			);
		}

		return $this;
	}

	public function addVoidMethod($file, $class, $method)
	{
		$this->voidMethods[] = array(
			'class' => $class,
			'method' => $method
		);

		return $this;
	}

	public function addUncompletedMethod($file, $class, $method, $exitCode, $output)
	{
		$this->uncompletedMethods[] = array(
			'class' => $class,
			'method' => $method,
			'exitCode' => $exitCode,
			'output' => $output
		);

		return $this;
	}

	public function merge(score $score)
	{
		$this->passNumber += $score->getPassNumber();
		$this->failAssertions = array_merge($this->failAssertions, $score->getFailAssertions());
		$this->exceptions = array_merge($this->exceptions, $score->getExceptions());
		$this->runtimeExceptions = array_merge($this->runtimeExceptions, $score->getRuntimeExceptions());
		$this->errors = array_merge($this->errors, $score->getErrors());
		$this->outputs = array_merge($this->outputs, $score->getOutputs());
		$this->durations = array_merge($this->durations, $score->getDurations());
		$this->memoryUsages = array_merge($this->memoryUsages, $score->getMemoryUsages());
		$this->voidMethods = array_merge($this->voidMethods, $score->getVoidMethods());
		$this->uncompletedMethods = array_merge($this->uncompletedMethods, $score->getUncompletedMethods());
		$this->coverage->merge($score->getCoverage());

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

	public function failExists(asserter\exception $exception)
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
