<?php

namespace mageekguy\atoum\score;

use
	mageekguy\atoum
;

class container
{
	protected $passNumber = 0;
	protected $failAssertions = array();
	protected $exceptions = array();
	protected $runtimeExceptions = array();
	protected $errors = array();
	protected $outputs = array();
	protected $durations = array();
	protected $memoryUsages = array();
	protected $uncompletedMethods = array();
	protected $coverage = null;

	public function __construct(atoum\score $score)
	{
		$this->passNumber = $score->getPassNumber();
		$this->failAssertions = $score->getFailAssertions();
		$this->exceptions = $score->getExceptions();
		$this->runtimeExceptions = $score->getRuntimeExceptions();
		$this->errors = $score->getErrors();
		$this->outputs = $score->getOutputs();
		$this->durations = $score->getDurations();
		$this->memoryUsages = $score->getMemoryUsages();
		$this->uncompletedMethods = $score->getUncompletedMethods();
		$this->coverage = $score->getCoverage()->getContainer();
	}

	public function getPassNumber()
	{
		return $this->passNumber;
	}

	public function getFailAssertions()
	{
		return $this->failAssertions;
	}

	public function getExceptions()
	{
		return $this->exceptions;
	}

	public function getRuntimeExceptions()
	{
		return $this->runtimeExceptions;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function getOutputs()
	{
		return $this->outputs;
	}

	public function getDurations()
	{
		return $this->durations;
	}

	public function getMemoryUsages()
	{
		return $this->memoryUsages;
	}

	public function getUncompletedMethods()
	{
		return $this->uncompletedMethods;
	}

	public function getCoverage()
	{
		return $this->coverage;
	}
}
