<?php

namespace mageekguy\atoum\report\fields\runner\result;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report
;

class string extends report\fields\runner\result
{
	const defaultTitlePrompt = '> ';

	protected $titlePrompt = '';

	public function __construct(atoum\locale $locale = null, $titlePrompt = null)
	{
		parent::__construct($locale);

		if ($titlePrompt === null)
		{
			$titlePrompt = static::defaultTitlePrompt;
		}

		$this->setTitlePrompt($titlePrompt);
	}

	public function setTitlePrompt($prompt)
	{
		$this->titlePrompt = (string) $prompt;

		return $this;
	}

	public function getTitlePrompt()
	{
		return $this->titlePrompt;
	}

	public function __toString()
	{
		$string = static::defaultTitlePrompt;

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
		}

		return $string . PHP_EOL;
	}
}

?>
