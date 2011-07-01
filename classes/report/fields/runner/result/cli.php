<?php

namespace mageekguy\atoum\report\fields\runner\result;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report
;

class cli extends report\fields\runner\result
{
	const defaultPrompt = '> ';

	protected $prompt = '';
	protected $successColorizer = null;
	protected $failureColorizer = null;

	public function __construct(atoum\cli\colorizer $successColorizer = null, atoum\cli\colorizer $failureColorizer = null, atoum\cli\prompt $prompt = null, atoum\locale $locale = null)
	{
		parent::__construct($locale);

		if ($prompt === null)
		{
			$prompt = new atoum\cli\prompt(static::defaultPrompt);
		}

		$this
			->setPrompt($prompt)
		;

		if ($successColorizer === null)
		{
			$successColorizer = new atoum\cli\colorizer();
		}

		$this->setSuccessColorizer($successColorizer);

		if ($failureColorizer === null)
		{
			$failureColorizer = new atoum\cli\colorizer();
		}

		$this->setFailureColorizer($failureColorizer);
	}

	public function setPrompt(atoum\cli\prompt $prompt)
	{
		$this->prompt = $prompt;

		return $this;
	}

	public function getPrompt()
	{
		return $this->prompt;
	}

	public function setSuccessColorizer(atoum\cli\colorizer $colorizer)
	{
		$this->successColorizer = $colorizer;

		return $this;
	}

	public function getSuccessColorizer()
	{
		return $this->successColorizer;
	}

	public function setFailureColorizer(atoum\cli\colorizer $colorizer)
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
		else if ($this->failNumber === 0 && $this->errorNumber === 0 && $this->exceptionNumber === 0)
		{
			$string .= $this->successColorizer->colorize(sprintf($this->locale->_('Success (%s, %s, %s, %s, %s) !'),
						sprintf($this->locale->__('%s test', '%s tests', $this->testNumber), $this->testNumber),
						sprintf($this->locale->__('%s method', '%s methods', $this->testMethodNumber), $this->testMethodNumber),
						sprintf($this->locale->__('%s assertion', '%s assertions', $this->assertionNumber), $this->assertionNumber),
						sprintf($this->locale->__('%s error', '%s errors', $this->errorNumber), $this->errorNumber),
						sprintf($this->locale->__('%s exception', '%s exceptions', $this->exceptionNumber), $this->exceptionNumber)
					)
				)
			;
		}
		else
		{
			$string .= $this->failureColorizer->colorize(sprintf($this->locale->_('Failure (%s, %s, %s, %s, %s) !'),
						sprintf($this->locale->__('%s test', '%s tests', $this->testNumber), $this->testNumber),
						sprintf($this->locale->__('%s method', '%s methods', $this->testMethodNumber), $this->testMethodNumber),
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

?>
