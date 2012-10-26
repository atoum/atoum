<?php

namespace mageekguy\atoum\report\fields\runner;

use
	mageekguy\atoum\runner,
	mageekguy\atoum\report,
	mageekguy\atoum\observable
;

abstract class result extends report\field
{
	protected $testNumber = null;
	protected $testMethodNumber = null;
	protected $assertionNumber = null;
	protected $failNumber = null;
	protected $errorNumber = null;
	protected $exceptionNumber = null;
	protected $voidMethodNumber = null;
	protected $uncompletedMethodNumber = null;
	protected $skippedMethodNumber = null;

	public function __construct()
	{
		parent::__construct(array(runner::runStop));
	}

	public function getTestNumber()
	{
		return $this->testNumber;
	}

	public function getTestMethodNumber()
	{
		return $this->testMethodNumber;
	}

	public function getAssertionNumber()
	{
		return $this->assertionNumber;
	}

	public function getFailNumber()
	{
		return $this->failNumber;
	}

	public function getErrorNumber()
	{
		return $this->errorNumber;
	}

	public function getExceptionNumber()
	{
		return $this->exceptionNumber;
	}

	public function getVoidMethodNumber()
	{
		return $this->voidMethodNumber;
	}

	public function getUncompletedMethodNumber()
	{
		return $this->uncompletedMethodNumber;
	}

	public function getSkippedMethodNumber()
	{
		return $this->skippedMethodNumber;
	}

	public function handleEvent($event, observable $observable)
	{
		if (parent::handleEvent($event, $observable) === false)
		{
			return false;
		}
		else
		{
			$score = $observable->getScore();

			$this->testNumber = $observable->getTestNumber();
			$this->testMethodNumber = $observable->getTestMethodNumber();
			$this->assertionNumber = $score->getAssertionNumber();
			$this->failNumber = $score->getFailNumber();
			$this->errorNumber = $score->getErrorNumber();
			$this->exceptionNumber = $score->getExceptionNumber();
			$this->voidMethodNumber = $score->getVoidMethodNumber();
			$this->uncompletedMethodNumber = $score->getUncompletedMethodNumber();
			$this->skippedMethodNumber = $score->getSkippedMethodNumber();

			return true;
		}
	}
}
