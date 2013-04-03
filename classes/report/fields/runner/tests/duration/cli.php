<?php

namespace mageekguy\atoum\report\fields\runner\tests\duration;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report
;

class cli extends report\fields\runner\tests\duration
{
	protected $prompt = null;
	protected $titleColorizer = null;
	protected $durationColorizer = null;

	public function __construct()
	{
		parent::__construct();

		$this
			->setPrompt()
			->setTitleColorizer()
			->setDurationColorizer()
		;
	}

	public function __toString()
	{
		return $this->prompt .
			sprintf(
				$this->locale->_('%s: %s.'),
				$this->titleColorizer->colorize($this->locale->__('Total test duration', 'Total tests duration', $this->testNumber)),
				$this->durationColorizer->colorize(
					sprintf(
						$this->value === null ? $this->locale->_('unknown') : $this->locale->__('%4.2f second', '%4.2f seconds', $this->value),
						$this->value
					)
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

	public function setTitleColorizer(colorizer $titleColorizer = null)
	{
		$this->titleColorizer = $titleColorizer ?: new colorizer();

		return $this;
	}

	public function getTitleColorizer()
	{
		return $this->titleColorizer;
	}

	public function setDurationColorizer(colorizer $durationColorizer = null)
	{
		$this->durationColorizer = $durationColorizer ?: new colorizer();

		return $this;
	}

	public function getDurationColorizer()
	{
		return $this->durationColorizer;
	}
}
