<?php

namespace mageekguy\atoum\report\fields\runner\coverage;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report
;

class string extends report\fields\runner\coverage
{
	const defaultPrompt = '> ';

	protected $prompt = '';

	public function __construct(atoum\locale $locale = null, $prompt = null)
	{
		parent::__construct($locale);

		$this
			->setPrompt($prompt ?: self::defaultPrompt)
		;
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

		if ($this->coverage === null)
		{
			$string .= $this->locale->_('Code coverage: unknown.');
		}
		else
		{
			$string .= sprintf($this->locale->_('Code coverage: %3.2f%%.'), round($this->coverage->getValue() * 100, 2));
		}

		$string .= PHP_EOL;

		return $string;
	}
}

?>
