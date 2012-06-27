<?php

namespace mageekguy\atoum\report\fields\runner\tests\memory;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report
;

class cli extends report\fields\runner\tests\memory
{
	protected $prompt = null;
	protected $memoryColorizer = null;
	protected $titleColorizer = null;

	public function __construct(prompt $prompt = null, colorizer $titleColorizer = null, colorizer $memoryColorizer = null, locale $locale = null)
	{
		parent::__construct($locale);

		$this
			->setPrompt($prompt ?: new prompt())
			->setTitleColorizer($titleColorizer ?: new colorizer())
			->setMemoryColorizer($memoryColorizer ?: new colorizer())
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
		$title = $this->locale->__('Total test memory usage', 'Total tests memory usage', $this->testNumber);

		if ($this->value === null)
		{
			$memory = $this->locale->_('unknown');
		}
		else
		{
			$memory = sprintf($this->locale->_('%4.2f Mb'), $this->value / 1048576);
		}

		return $this->prompt . sprintf($this->locale->_('%s: %s.'), $this->titleColorizer->colorize($title), $this->memoryColorizer->colorize($memory)) . PHP_EOL;
	}
}
