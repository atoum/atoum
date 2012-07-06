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
	protected $voidMethods = array();
	protected $uncompletedMethods = array();
	protected $coverage = null;

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

	public function merge(container $container)
	{
		$this->passNumber += $container->getPassNumber();
		$this->failAssertions = array_merge($this->failAssertions, $container->getFailAssertions());
		$this->exceptions = array_merge($this->exceptions, $container->getExceptions());
		$this->runtimeExceptions = array_merge($this->runtimeExceptions, $container->getRuntimeExceptions());
		$this->errors = array_merge($this->errors, $container->getErrors());
		$this->outputs = array_merge($this->outputs, $container->getOutputs());
		$this->durations = array_merge($this->durations, $container->getDurations());
		$this->memoryUsages = array_merge($this->memoryUsages, $container->getMemoryUsages());
		$this->voidMethods = array_merge($this->voidMethods, $container->getVoidMethods());
		$this->uncompletedMethods = array_merge($this->uncompletedMethods, $container->getUncompletedMethods());
		$this->coverage->merge($container->getCoverage());

		return $this;
	}
}
