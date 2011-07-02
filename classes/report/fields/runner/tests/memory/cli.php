<?php

namespace mageekguy\atoum\report\fields\runner\tests\memory;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer
;

class cli extends report\fields\runner\tests\memory
{
	const defaultPrompt = '> ';

	protected $prompt = null;
	protected $dataColorizer = null;
	protected $titleColorizer = null;

	public function __construct(prompt $prompt = null, colorizer $titleColorizer = null, colorizer $dataColorizer = null, atoum\locale $locale = null)
	{
		parent::__construct($locale);

		if ($prompt === null)
		{
			$prompt = new prompt(self::defaultPrompt);
		}

		if ($titleColorizer === null)
		{
			$titleColorizer = new colorizer('1;36');
		}

		if ($dataColorizer === null)
		{
			$dataColorizer = new colorizer();
		}

		$this
			->setPrompt($prompt)
			->setTitleColorizer($titleColorizer)
			->setDataColorizer($dataColorizer)
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

	public function setDataColorizer(colorizer $colorizer)
	{
		$this->dataColorizer = $colorizer;

		return $this;
	}

	public function getDataColorizer()
	{
		return $this->dataColorizer;
	}

	public function __toString()
	{
		$title = $this->locale->__('Total test memory usage', 'Total tests memory usage', $this->testNumber);

		if ($this->value === null)
		{
			$data = $this->locale->_('unknown');
		}
		else
		{
			$data = sprintf($this->locale->_('%4.2f Mb'), $this->value / 1048576);
		}

		return $this->prompt . sprintf($this->locale->_('%s: %s.'), $this->titleColorizer->colorize($title), $this->dataColorizer->colorize($data)) . PHP_EOL;
	}
}

?>
