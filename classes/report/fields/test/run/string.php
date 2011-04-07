<?php

namespace mageekguy\atoum\report\fields\test\run;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\test\run
{
	const titlePrompt = '> ';

	protected $label = '';

	public function __construct($label = null, $prompt = null, atoum\locale $locale = null)
	{
		parent::__construct($locale);

		if ($label === null)
		{
			$label = $this->locale->_('Run %s...');
		}

		if ($prompt === null)
		{
			$prompt = static::titlePrompt;
		}

		$this
			->setLabel($label)
			->setPrompt($prompt)
		;
	}

	public function getLabel()
	{
		return $this->label;
	}

	public function setLabel($label)
	{
		$this->label = (string) $label;

		return $this;
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
			$string .= sprintf($this->label, $this->testClass);
		}

		$string .= PHP_EOL;

		return $string;
	}
}

?>
