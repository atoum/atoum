<?php

namespace mageekguy\atoum;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

class score
{
	private $passAssertions = 0;
	private $failAssertions = array();
	private $exceptions = array();
	private $errors = array();
	private $outputs = array();
	private $durations = array();
	private $memoryUsages = array();
	private $coverage = null;
	private $case = null;
	private $phpPath = null;
	private $phpVersion = null;
	private $atoumVersion = null;

	private static $failId = 0;

	public function __construct(score\coverage $coverage = null)
	{
		if ($coverage === null)
		{
			$coverage = new score\coverage();
		}

		$this->coverage = $coverage;
	}

	public function reset()
	{
		$this->passAssertions = 0;
		$this->failAssertions = array();
		$this->exceptions = array();
		$this->errors = array();
		$this->outputs = array();
		$this->durations = array();
		$this->memoryUsages = array();

		return $this;
	}

	public function setAtoumVersion($version)
	{
		$this->atoumVersion = (string) $version;

		return $this;
	}

	public function setPhpPath($path)
	{
		$this->phpPath = (string) $path;

		return $this;
	}

	public function setPhpVersion($version)
	{
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
			'class' => $class,
			'method' => $method,
			'file' => $file,
			'line' => $line,
			'value' => (string) $exception
		);

		return $this;
	}

	public function addError($file, $line, $class, $method, $type, $message, $errorFile = null, $errorLine = null)
	{
		$this->errors[] = array(
			'case' => $this->case,
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
				'case' => $this->case,
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
				'case' => $this->case,
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
				'case' => $this->case,
				'class' => $class,
				'method' => $method,
				'value' => $memoryUsage
			);
		}

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

		return $this;
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
		return self::cleanAssertions($this->failAssertions);
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function getExceptions()
	{
		return $this->exceptions;
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

	public function getCoverage()
	{
		return $this->coverage;
	}

	public function setCase($case)
	{
		$this->case = (string) $case;

		return $this;
	}

	public function getCase()
	{
		return $this->case;
	}

	public function errorExists($message = null, $type = null)
	{
		$messageIsNull = $message === null;
		$typeIsNull = $type === null;

		foreach ($this->errors as $key => $error)
		{
			$messageMatch = $messageIsNull === true ? true : $error['message'] == $message;
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

	private static function cleanAssertions(array $assertions)
	{
		return array_map(function ($assertion) { unset($assertion['id']); return $assertion; }, array_values($assertions));
	}
}

?>
