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
		$title = $this->locale->__('Total test memory usage', 'Total tests memory usage', $this->testNumber);

		if ($this->value === null)
		{
			$memory = $this->locale->_('unknown');
		}
		else
		{
			$memory = $this->locale->_('%4.2f Mb', $this->value / 1048576);
		}

		return $this->prompt . $this->locale->_('%s: %s.', $this->titleColorizer->colorize($title), $this->memoryColorizer->colorize($memory)) . PHP_EOL;
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
