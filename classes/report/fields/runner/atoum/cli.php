<?php

namespace mageekguy\atoum\report\fields\runner\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\report,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer
;

class cli extends report\fields\runner\atoum
{
	protected $prompt = null;
	protected $colorizer = null;

	public function __construct()
	{
		parent::__construct();

		$this
			->setPrompt()
			->setColorizer()
		;
	}

	public function __toString()
	{
		return ($this->author === null || $this->version === null ? '' : $this->prompt . $this->colorizer->colorize($this->locale->_('atoum version %s by %s (%s)', $this->version, $this->author, $this->path)) . PHP_EOL);
	}

	public function setPrompt(prompt $prompt = null)
	{
		$this->prompt = $prompt ?: new prompt;

		return $this;
	}

	public function getPrompt()
	{
		return $this->prompt;
	}

	public function setColorizer(colorizer $colorizer = null)
	{
		$this->colorizer = $colorizer ?: new colorizer();

		return $this;
	}

	public function getColorizer()
	{
		return $this->colorizer;
	}
}
