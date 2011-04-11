<?php

namespace mageekguy\atoum\report\fields\runner\version;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\runner\version
{
	const defaultPrompt = '> ';

	public function __construct(atoum\locale $locale =null, $prompt = null, $label = null)
	{
		parent::__construct($locale);

		if ($prompt === null)
		{
			$prompt = static::defaultPrompt;
		}

		if ($label === null)
		{
			$label = $this->locale->_('Atoum version %s by %s (%s)');
		}

		$this
			->setPrompt($prompt)
			->setLabel($label)
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

	public function setLabel($label)
	{
		$this->label = (string) $label;

		return $this;
	}

	public function getLabel()
	{
		return $this->label;
	}

	public function __toString()
	{
		return ($this->author === null || $this->version === null ? '' : $this->prompt . sprintf($this->label, $this->version, $this->author, $this->path) . PHP_EOL);
	}
}

?>
