<?php

namespace mageekguy\atoum\report\fields\runner;

use
	mageekguy\atoum\runner,
	mageekguy\atoum\report
;

abstract class result extends report\fields\runner
{
	protected $testNumber = null;
	protected $testMethodNumber = null;
	protected $assertionNumber = null;
	protected $failNumber = null;
	protected $errorNumber = null;
	protected $exceptionNumber = null;
	protected $uncompletedTestNumber = null;

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

	public function getUncompletedTestNumber()
	{
		return $this->uncompletedTestNumber;
	}

	public function setWithRunner(runner $runner, $event = null)
	{
		if ($event === runner::runStop)
		{
			$score = $runner->getScore();

			$this->testNumber = $runner->getTestNumber();
			$this->testMethodNumber = $runner->getTestMethodNumber();
			$this->assertionNumber = $score->getAssertionNumber();
			$this->failNumber = $score->getFailNumber();
			$this->errorNumber = $score->getErrorNumber();
			$this->exceptionNumber = $score->getExceptionNumber();
			$this->uncompletedTestNumber = $score->getUncompletedTestNumber();
		}

		return $this;
	}
}

?>
