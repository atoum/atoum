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

	public function __construct(prompt $prompt = null, colorizer $colorizer = null, atoum\locale $locale = null)
	{
		parent::__construct($locale);

		$this
			->setPrompt($prompt ?: new prompt())
			->setColorizer($colorizer ?: new colorizer())
		;
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

	public function setColorizer(colorizer $colorizer)
	{
		$this->colorizer = $colorizer;

		return $this;
	}

	public function getColorizer()
	{
		return $this->colorizer;
	}

	public function __toString()
	{
		return ($this->author === null || $this->version === null ? '' : $this->prompt . $this->colorizer->colorize(sprintf($this->locale->_('atoum version %s by %s (%s)'), $this->version, $this->author, $this->path)) . PHP_EOL);
	}
}
