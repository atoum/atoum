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

	public function __construct()
	{
		parent::__construct();

		$this
			->setPrompt()
			->setTitleColorizer()
			->setMemoryColorizer()
		;
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

	public function setMemoryColorizer(colorizer $colorizer = null)
	{
		$this->memoryColorizer = $colorizer ?: new colorizer();

		return $this;
	}

	public function getMemoryColorizer()
	{
		return $this->memoryColorizer;
	}
}
