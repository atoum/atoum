<?php

namespace mageekguy\atoum\report\fields\runner\version;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer
;

class cli extends report\fields\runner\version
{
	const defaultPrompt = '> ';

	protected $prompt = '';

	public function __construct(prompt $prompt = null, atoum\locale $locale = null)
	{
		parent::__construct($locale);

		if ($prompt === null)
		{
			$prompt = new prompt(static::defaultPrompt);
		}

		$this->setPrompt($prompt);
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

	public function __toString()
	{
		return ($this->author === null || $this->version === null ? '' : $this->prompt . sprintf($this->locale->_('Atoum version %s by %s (%s)'), $this->version, $this->author, $this->path) . PHP_EOL);
	}
}

?>
