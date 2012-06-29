<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

class score extends atoum\score\container
{
	protected $factory = null;

	private static $failId = 0;

	public function __construct(factory $factory = null)
	{
		$this
			->setFactory($factory ?: new factory())
			->setCoverage($this->factory['mageekguy\atoum\score\coverage']($this->factory))
		;
	}

	public function setFactory(factory $factory)
	{
		$this->factory = $factory;

		return $this;
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

	public function merge(score $score)
	{
		$this->passNumber += $score->passNumber;
		$this->failAssertions = array_merge($this->failAssertions, $score->failAssertions);
		$this->exceptions = array_merge($this->exceptions, $score->exceptions);
		$this->runtimeExceptions = array_merge($this->runtimeExceptions, $score->runtimeExceptions);
		$this->errors = array_merge($this->errors, $score->errors);
		$this->outputs = array_merge($this->outputs, $score->outputs);
		$this->durations = array_merge($this->durations, $score->durations);
		$this->memoryUsages = array_merge($this->memoryUsages, $score->memoryUsages);
		$this->uncompletedMethods = array_merge($this->uncompletedMethods, $score->uncompletedMethods);
		$this->coverage->merge($score->coverage);

		return $this;
	}

	public function addPass()
	{
		$this->passNumber++;

		return $this;
	}

	public function addFail($file, $line, $class, $method, $asserter, $reason, $case = null, $dataSetKey = null, $dataSetProvider = null)
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

	public function addException($file, $line, $class, $method, \exception $exception, $case = null, $dataSetKey = null, $dataSetProvider = null)
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

	public function addRuntimeException(exceptions\runtime $exception)
	{
		$this->runtimeExceptions[] = $exception;

		return $this;
	}

	public function addError($file, $line, $class, $method, $type, $message, $errorFile = null, $errorLine = null, $case = null, $dataSetKey = null, $dataSetProvider = null)
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

	public function addDuration($class, $method, $duration)
	{
		if ($duration > 0)
		{
			$this->durations[] = array(
				'class' => $class,
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
				'class' => $class,
				'method' => $method,
				'value' => $memoryUsage
			);
		}

		return $this;
	}

	public function addUncompletedMethod($class, $method, $exitCode, $output)
	{
		$this->uncompletedMethods[] = array(
			'class' => $class,
			'method' => $method,
			'exitCode' => $exitCode,
			'output' => $output
		);

		return $this;
	}

	public function getFactory()
	{
		return $this->factory;
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

	public function getUncompletedMethodNumber()
	{
		return sizeof($this->uncompletedMethods);
	}

	public function getCoverageContainer()
	{
		return $this->coverage->getContainer();
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

	public function getContainer()
	{
		return $this->factory['mageekguy\atoum\score\container']($this);
	}

	public function mergeContainer(score\container $container)
	{
		$this->passNumber += $container->getPassNumber();
		$this->failAssertions = array_merge($this->failAssertions, $container->getFailAssertions());
		$this->exceptions = array_merge($this->exceptions, $container->getExceptions());
		$this->runtimeExceptions = array_merge($this->runtimeExceptions, $container->getRuntimeExceptions());
		$this->errors = array_merge($this->errors, $container->getErrors());
		$this->outputs = array_merge($this->outputs, $container->getOutputs());
		$this->durations = array_merge($this->durations, $container->getDurations());
		$this->memoryUsages = array_merge($this->memoryUsages, $container->getMemoryUsages());
		$this->uncompletedMethods = array_merge($this->uncompletedMethods, $container->getUncompletedMethods());
		$this->coverage->mergeContainer($container->getCoverage());

		return $this;
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
