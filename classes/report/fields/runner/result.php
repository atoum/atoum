<?php

namespace mageekguy\atoum\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

abstract class result extends report\fields\runner
{
	protected $testNumber = null;
	protected $testMethodNumber = null;
	protected $assertionNumber = null;
	protected $failNumber = null;
	protected $errorNumber = null;
	protected $exceptionNumber = null;

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

	public function setWithRunner(atoum\runner $runner, $event = null)
	{
		if ($event === atoum\runner::runStop)
		{
			$score = $runner->getScore();

			$this->testNumber = $runner->getTestNumber();
			$this->testMethodNumber = $runner->getTestMethodNumber();
			$this->assertionNumber = $score->getAssertionNumber();
			$this->failNumber = $score->getFailNumber();
			$this->errorNumber = $score->getErrorNumber();
			$this->exceptionNumber = $score->getExceptionNumber();
		}

		return $this;
	}
}

?>
