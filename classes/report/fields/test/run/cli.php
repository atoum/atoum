<?php

namespace mageekguy\atoum\report\fields\test\run;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report
;

class cli extends report\fields\test\run
{
	protected $prompt = null;
	protected $colorizer = null;

	public function __construct(prompt $prompt = null, colorizer $colorizer = null, locale $locale = null)
	{
		parent::__construct($locale);

		$this
			->setPrompt($prompt ?: new prompt())
			->setColorizer($colorizer ?: new colorizer())
		;
	}

	public function setPrompt($prompt)
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
		return $this->prompt .
			(
				$this->testClass === null
				?
				$this->colorizer->colorize($this->locale->_('There is currently no test running.'))
				:
				sprintf($this->locale->_('%s...'), $this->colorizer->colorize($this->testClass))
			) .
			PHP_EOL
		;
	}
}
