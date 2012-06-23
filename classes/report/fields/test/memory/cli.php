<?php

namespace mageekguy\atoum\report\fields\test\memory;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report
;

class cli extends report\fields\test\memory
{
	protected $prompt = null;
	protected $titleColorizer = null;
	protected $memoryColorizer = null;

	public function __construct(prompt $prompt = null, colorizer $titleColorizer = null, colorizer $memoryColorizer = null, locale $locale = null)
	{
		parent::__construct($locale);

		$this
			->setPrompt($prompt ?: new prompt())
			->setTitleColorizer($titleColorizer ?: new colorizer())
			->setMemoryColorizer($memoryColorizer ?: new colorizer())
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

	public function setTitleColorizer(colorizer $colorizer)
	{
		$this->titleColorizer = $colorizer;

		return $this;
	}

	public function getTitleColorizer()
	{
		return $this->titleColorizer;
	}

	public function setMemoryColorizer(colorizer $colorizer)
	{
		$this->memoryColorizer = $colorizer;

		return $this;
	}

	public function getMemoryColorizer()
	{
		return $this->memoryColorizer;
	}

	public function __toString()
	{
			return $this->prompt .
			sprintf(
				$this->locale->_('%1$s: %2$s.'),
				$this->titleColorizer->colorize($this->locale->_('Memory usage')),
				$this->memoryColorizer->colorize(
					$this->value === null
					?
					$this->locale->_('unknown')
					:
					sprintf(
						$this->locale->_('%4.2f Mb'),
						$this->value / 1048576
					)
				)
			) .
			PHP_EOL
		;
	}
}
