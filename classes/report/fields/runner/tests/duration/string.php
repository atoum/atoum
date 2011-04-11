<?php

namespace mageekguy\atoum\report\fields\runner\tests\duration;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report
;

class string extends report\fields\runner\tests\duration
{
	const defaultPrompt = '> ';

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
			$string .= $this->locale->__('Total test duration: unknown.', 'Total tests duration: unknown.', $this->testNumber);
		}
		else
		{
			$string .= sprintf(
				$this->locale->__('Total test duration: %s.', 'Total tests duration: %s.', $this->testNumber),
				sprintf(
					$this->locale->__('%4.2f second', '%4.2f seconds', $this->value),
					$this->value
				)
			);
		}

		$string .= PHP_EOL;

		return $string;
	}
}

?>
