<?php

namespace mageekguy\atoum\report\fields\runner\coverage;

use
	mageekguy\atoum,
	mageekguy\atoum\report,
	mageekguy\atoum\runner,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer
;

class cli extends report\fields\runner\coverage
{
	protected $prompt = null;
	protected $titleColorizer = null;
	protected $coverageColorizer = null;

	public function __construct()
	{
		parent::__construct();

		$this
			->setPrompt()
			->setTitleColorizer()
			->setCoverageColorizer()
		;
	}

	public function __toString()
	{
		return $this->prompt .
			sprintf(
				'%s: %s.',
				$this->titleColorizer->colorize($this->locale->_('Code coverage')),
				$this->coverageColorizer->colorize(
					$this->coverage === null
					?
					$this->locale->_('unknown')
					:
					$this->locale->_('%3.2f%%', round($this->coverage->getValue() * 100, 2))
				)
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

	public function setTitleColorizer(colorizer $colorizer = null)
	{
		$this->titleColorizer = $colorizer ?: new colorizer();

		return $this;
	}

	public function getTitleColorizer()
	{
		return $this->titleColorizer;
	}

	public function setCoverageColorizer(colorizer $colorizer = null)
	{
		$this->coverageColorizer = $colorizer ?: new colorizer();

		return $this;
	}

	public function getCoverageColorizer()
	{
		return $this->coverageColorizer;
	}
}
