<?php

namespace mageekguy\atoum\report\fields\runner\result;

use
	mageekguy\atoum,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields,
	mageekguy\atoum\observable
;

class cli extends fields\runner\result
{
	protected $observable = null;
	protected $prompt = null;
	protected $successColorizer = null;
	protected $failureColorizer = null;

	public function __construct()
	{
		parent::__construct();

		$this
			->setPrompt()
			->setSuccessColorizer()
			->setFailureColorizer()
		;
	}

	public function __toString()
	{
		$string = $this->prompt;

		if ($this->testNumber === null)
		{
			$string .= $this->locale->_('No test running.');
		}
		else if ($this->success)
		{
			$string .= $this->successColorizer->colorize(
					sprintf(
						$this->locale->_('Success (%s, %s, %s, %s, %s)!'),
						sprintf($this->locale->__('%s test', '%s tests', $this->testNumber), $this->testNumber),
						sprintf($this->locale->__('%s/%s method', '%s/%s methods', $this->testMethodNumber), $this->testMethodNumber - $this->voidMethodNumber - $this->skippedMethodNumber, $this->testMethodNumber),
						sprintf($this->locale->__('%s void method', '%s void methods', $this->voidMethodNumber), $this->voidMethodNumber),
						sprintf($this->locale->__('%s skipped method', '%s skipped methods', $this->skippedMethodNumber), $this->skippedMethodNumber),
						sprintf($this->locale->__('%s assertion', '%s assertions', $this->assertionNumber), $this->assertionNumber)
					)
				)
			;
		}
		else
		{
			$string .= $this->failureColorizer->colorize(
					sprintf(
						$this->locale->_('Failure (%s, %s, %s, %s, %s, %s, %s, %s)!'),
						sprintf($this->locale->__('%s test', '%s tests', $this->testNumber), $this->testNumber),
						sprintf($this->locale->__('%s/%s method', '%s/%s methods', $this->testMethodNumber), $this->testMethodNumber - $this->voidMethodNumber - $this->skippedMethodNumber - $this->uncompletedMethodNumber, $this->testMethodNumber),
						sprintf($this->locale->__('%s void method', '%s void methods', $this->voidMethodNumber), $this->voidMethodNumber),
						sprintf($this->locale->__('%s skipped method', '%s skipped methods', $this->skippedMethodNumber), $this->skippedMethodNumber),
						sprintf($this->locale->__('%s uncompleted method', '%s uncompleted methods', $this->uncompletedMethodNumber), $this->uncompletedMethodNumber),
						sprintf($this->locale->__('%s failure', '%s failures', $this->failNumber), $this->failNumber),
						sprintf($this->locale->__('%s error', '%s errors', $this->errorNumber), $this->errorNumber),
						sprintf($this->locale->__('%s exception', '%s exceptions', $this->exceptionNumber), $this->exceptionNumber)
					)
				)
			;
		}

		return $string . PHP_EOL;
	}

	public function setPrompt(prompt $prompt = null)
	{
		$this->prompt = $prompt ?: new prompt();

		return $this;
	}

	public function getPrompt()
	{
		return $this->prompt;
	}

	public function setSuccessColorizer(colorizer $colorizer = null)
	{
		$this->successColorizer = $colorizer ?: new colorizer();

		return $this;
	}

	public function getSuccessColorizer()
	{
		return $this->successColorizer;
	}

	public function setFailureColorizer(colorizer $colorizer = null)
	{
		$this->failureColorizer = $colorizer ?: new colorizer();

		return $this;
	}

	public function getFailureColorizer()
	{
		return $this->failureColorizer;
	}

	public function handleEvent($event, observable $observable)
	{
		if (parent::handleEvent($event, $observable) === false)
		{
			return false;
		}

		$this->observable = $observable;

		return true;
	}
}
