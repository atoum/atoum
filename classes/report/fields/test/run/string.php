<?php

namespace mageekguy\atoum\report\fields\test\run;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report
;

class string extends report\fields\test\run
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

	public function getPrompt()
	{
		return $this->prompt;
	}

	public function setPrompt($prompt)
	{
		$this->prompt = (string) $prompt;

		return $this;
	}

	public function __toString()
	{
		$string = $this->prompt;

		if ($this->testClass === null)
		{
			$string .= $this->locale->_('There is currently no test running.');
		}
		else
		{
			$string .= sprintf('%s:', $this->testClass);
		}

		$string .= PHP_EOL;

		return $string;
	}
}

?>
