<?php

namespace mageekguy\atoum\report\fields\runner\result;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields
;

class cli extends fields\runner\result
{
	protected $prompt = null;
	protected $successColorizer = null;
	protected $failureColorizer = null;

	public function __construct(prompt $prompt = null, colorizer $successColorizer = null, colorizer $failureColorizer = null, locale $locale = null)
	{
		parent::__construct($locale);

		$this
			->setPrompt($prompt ?: new prompt())
			->setSuccessColorizer($successColorizer ?: new colorizer())
			->setFailureColorizer($failureColorizer ?: new colorizer())
		;
	}

	public function setPrompt(prompt $prompt)
	{
		$this->prompt = $prompt;

		return $this;
	}

	public function getPrompt()
	{
		return $this->prompt;
	}

	public function setSuccessColorizer(colorizer $colorizer)
	{
		$this->successColorizer = $colorizer;

		return $this;
	}

	public function getSuccessColorizer()
	{
		return $this->successColorizer;
	}

	public function setFailureColorizer(colorizer $colorizer)
	{
		$this->failureColorizer = $colorizer;

		return $this;
	}

	public function getFailureColorizer()
	{
		return $this->failureColorizer;
	}

	public function __toString()
	{
		$string = $this->prompt;

		if ($this->testNumber === null )
		{
			$string .= $this->locale->_('No test running.');
		}
		else if ($this->failNumber === 0 && $this->errorNumber === 0 && $this->exceptionNumber === 0 && $this->uncompletedMethodNumber === 0)
		{
			$string .= $this->successColorizer->colorize(
					sprintf(
						$this->locale->_('Success (%s, %s, %s, %s, %s) !'),
						sprintf($this->locale->__('%s test', '%s tests', $this->testNumber), $this->testNumber),
						sprintf($this->locale->__('%s/%s method', '%s/%s methods', $this->testMethodNumber), $this->testMethodNumber - $this->uncompletedMethodNumber, $this->testMethodNumber),
						sprintf($this->locale->__('%s assertion', '%s assertions', $this->assertionNumber), $this->assertionNumber),
						sprintf($this->locale->__('%s error', '%s errors', $this->errorNumber), $this->errorNumber),
						sprintf($this->locale->__('%s exception', '%s exceptions', $this->exceptionNumber), $this->exceptionNumber)
					)
				)
			;
		}
		else
		{
			$string .= $this->failureColorizer->colorize(
					sprintf(
						$this->locale->_('Failure (%s, %s, %s, %s, %s) !'),
						sprintf($this->locale->__('%s test', '%s tests', $this->testNumber), $this->testNumber),
						sprintf($this->locale->__('%s/%s method', '%s/%s methods', $this->testMethodNumber), $this->testMethodNumber - $this->uncompletedMethodNumber, $this->testMethodNumber),
						sprintf($this->locale->__('%s failure', '%s failures', $this->failNumber), $this->failNumber),
						sprintf($this->locale->__('%s error', '%s errors', $this->errorNumber), $this->errorNumber),
						sprintf($this->locale->__('%s exception', '%s exceptions', $this->exceptionNumber), $this->exceptionNumber)
					)
				)
			;
		}

		return $string . PHP_EOL;
	}
}
