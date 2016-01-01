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
		return $this->prompt .
			(
				$this->testClass === null
				?
				$this->colorizer->colorize($this->locale->_('There is currently no test running.'))
				:
				$this->locale->_('%s...', $this->colorizer->colorize($this->testClass))
			) .
			PHP_EOL
		;
	}

	public function setPrompt(prompt $prompt = null)
	{
		$this->prompt = $prompt ?: new prompt();

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
