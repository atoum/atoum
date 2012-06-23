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

	public function __construct(prompt $prompt = null, colorizer $titleColorizer = null, colorizer $durationColorizer = null, locale $locale = null)
	{
		parent::__construct($locale);

		$this
			->setPrompt($prompt ?: new prompt())
			->setTitleColorizer($titleColorizer ?: new colorizer())
			->setDurationColorizer($durationColorizer ?: new colorizer())
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

	public function setTitleColorizer(colorizer $titleColorizer)
	{
		$this->titleColorizer = $titleColorizer;

		return $this;
	}

	public function getTitleColorizer()
	{
		return $this->titleColorizer;
	}

	public function setDurationColorizer(colorizer $durationColorizer)
	{
		$this->durationColorizer = $durationColorizer;

		return $this;
	}

	public function getDurationColorizer()
	{
		return $this->durationColorizer;
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
}
