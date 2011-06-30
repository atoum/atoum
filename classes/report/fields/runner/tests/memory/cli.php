<?php

namespace mageekguy\atoum\report\fields\runner\tests\memory;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report
;

class cli extends report\fields\runner\tests\memory
{
	const defaultPrompt = '> ';

	protected $prompt = '';

	public function __construct(atoum\locale $locale = null, $prompt = null)
	{
		parent::__construct($locale);

		if ($prompt === null)
		{
			$prompt = static::defaultPrompt;
		}

		$this->setPrompt($prompt);
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

	public function __toString()
	{
		$string = $this->prompt;

		if ($this->value === null)
		{
			$string .= $this->locale->_('Total test memory usage: unknown.');
		}
		else
		{
			$string .= sprintf($this->locale->__('Total test memory usage: %4.2f Mb.', 'Total tests memory usage: %4.2f Mb.', $this->testNumber), $this->value / 1048576);
		}

		$string .= PHP_EOL;

		return $string;
	}
}

?>
