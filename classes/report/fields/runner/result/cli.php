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

	public function __construct(atoum\cli\colorizer $successColorizer = null, atoum\cli\colorizer $failureColorizer = null, atoum\locale $locale = null, $prompt = null)
	{
		parent::__construct($locale);

		if ($prompt === null)
		{
			$prompt = static::defaultPrompt;
		}

		$this
			->setPrompt($prompt)
		;

		if ($successColorizer !== null)
		{
			$this->setSuccessColorizer($successColorizer);
		}

		if ($failureColorizer !== null)
		{
			$this->setFailureColorizer($failureColorizer);
		}
	}

	public function setPrompt($prompt)
	{
		$this->prompt = (string) $prompt;

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
		else if ($this->failNumber === 0)
		{
			$string .= sprintf($this->locale->_('Success (%s, %s, %s, %s, %s) !'),
					sprintf($this->locale->__('%s test', '%s tests', $this->testNumber), $this->testNumber),
					sprintf($this->locale->__('%s method', '%s methods', $this->testMethodNumber), $this->testMethodNumber),
					sprintf($this->locale->__('%s assertion', '%s assertions', $this->assertionNumber), $this->assertionNumber),
					sprintf($this->locale->__('%s error', '%s errors', $this->errorNumber), $this->errorNumber),
					sprintf($this->locale->__('%s exception', '%s exceptions', $this->exceptionNumber), $this->exceptionNumber)
				)
			;

			if ($this->successColorizer !== null)
			{
				$string = $this->successColorizer->colorize($string);
			}
		}
		else
		{
			$string .= sprintf($this->locale->_('Failure (%s, %s, %s, %s, %s) !'),
					sprintf($this->locale->__('%s test', '%s tests', $this->testNumber), $this->testNumber),
					sprintf($this->locale->__('%s method', '%s methods', $this->testMethodNumber), $this->testMethodNumber),
					sprintf($this->locale->__('%s failure', '%s failures', $this->failNumber), $this->failNumber),
					sprintf($this->locale->__('%s error', '%s errors', $this->errorNumber), $this->errorNumber),
					sprintf($this->locale->__('%s exception', '%s exceptions', $this->exceptionNumber), $this->exceptionNumber)
				)
			;

			if ($this->failureColorizer !== null)
			{
				$string = $this->failureColorizer->colorize($string);
			}
		}

		return $string . PHP_EOL;
	}
}

?>
