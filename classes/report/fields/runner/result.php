<?php

namespace mageekguy\atoum\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class result extends report\fields\runner
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

	public function setWithRunner(atoum\runner $runner)
	{
		$testNumber = $runner->getTestNumber();

		if ($testNumber > 0)
		{
			$score = $runner->getScore();

			$this->testNumber = $testNumber;
			$this->testMethodNumber = $runner->getTestMethodNumber();
			$this->assertionNumber = $score->getAssertionNumber();
			$this->failNumber = $score->getFailNumber();
			$this->errorNumber = $score->getErrorNumber();
			$this->exceptionNumber = $score->getExceptionNumber();
		}

		return $this;
	}

	public function toString()
	{
		$string = '';

		if ($this->testNumber === null )
		{
			$string = $this->locale->_('No test running.');
		}
		else if ($this->failNumber === 0)
		{
			$string = sprintf($this->locale->_('Success (%s, %s, %s, %s, %s) !'),
					sprintf($this->locale->__('%s test', '%s tests', $this->testNumber), $this->testNumber),
					sprintf($this->locale->__('%s method', '%s methods', $this->testMethodNumber), $this->testMethodNumber),
					sprintf($this->locale->__('%s assertion', '%s assertions', $this->assertionNumber), $this->assertionNumber),
					sprintf($this->locale->__('%s error', '%s errors', $this->errorNumber), $this->errorNumber),
					sprintf($this->locale->__('%s exception', '%s exceptions', $this->exceptionNumber), $this->exceptionNumber)
				)
			;
		}
		else
		{
			$string = sprintf($this->locale->_('Failure (%s, %s, %s, %s, %s) !'),
					sprintf($this->locale->__('%s test', '%s tests', $this->testNumber), $this->testNumber),
					sprintf($this->locale->__('%s method', '%s methods', $this->testMethodNumber), $this->testMethodNumber),
					sprintf($this->locale->__('%s failure', '%s failures', $this->failNumber), $this->failNumber),
					sprintf($this->locale->__('%s error', '%s errors', $this->errorNumber), $this->errorNumber),
					sprintf($this->locale->__('%s exception', '%s exceptions', $this->exceptionNumber), $this->exceptionNumber)
				)
			;
		}

		return $string;
	}
}

?>
