<?php

namespace mageekguy\atoum\report\fields\runner\version;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report
;

class cli extends report\fields\runner\version
{
	const defaultPrompt = '> ';

	protected $prompt = '';

	public function __construct(atoum\locale $locale =null, $prompt = null)
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
		return ($this->author === null || $this->version === null ? '' : $this->prompt . sprintf($this->locale->_('Atoum version %s by %s (%s)'), $this->version, $this->author, $this->path) . PHP_EOL);
	}
}

?>
