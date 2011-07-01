<?php

namespace mageekguy\atoum\report\fields\runner\php;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer
;

class cli extends report\fields\runner\php
{
	const defaultTitlePrompt = '> ';
	const defaultDataPrompt = '=> ';

	protected $titlePrompt = null;
	protected $titleColorizer = null;
	protected $dataPrompt = null;
	protected $dataColorizer = null;

	public function __construct(prompt $titlePrompt = null, colorizer $titleColorizer = null, prompt $dataPrompt = null, colorizer $dataColorizer = null, atoum\locale $locale = null)
	{
		parent::__construct($locale);

		if ($titlePrompt === null)
		{
			$titlePrompt = new prompt(static::defaultTitlePrompt);
		}

		if ($titleColorizer === null)
		{
			$titleColorizer = new colorizer('1;36');
		}

		if ($dataPrompt === null)
		{
			$dataPrompt = new prompt(static::defaultDataPrompt, new colorizer('1;36'));
		}

		if ($dataColorizer === null)
		{
			$dataColorizer = new colorizer();
		}

		$this
			->setTitlePrompt($titlePrompt)
			->setTitleColorizer($titleColorizer)
			->setDataPrompt($dataPrompt)
			->setDataColorizer($dataColorizer)
		;
	}

	public function setTitlePrompt($prompt)
	{
		$this->titlePrompt = $prompt;

		return $this;
	}

	public function getTitlePrompt()
	{
		return $this->titlePrompt;
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

	public function setDataPrompt($prompt)
	{
		$this->dataPrompt = $prompt;

		return $this;
	}

	public function getDataPrompt()
	{
		return $this->dataPrompt;
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
		$string =
			$this->titlePrompt . $this->titleColorizer->colorize($this->locale->_('PHP path:')) . ' ' . $this->dataColorizer->colorize($this->phpPath) . PHP_EOL
			.
			$this->titlePrompt . $this->titleColorizer->colorize($this->locale->_('PHP version:')) . PHP_EOL
		;

		foreach (explode(PHP_EOL, $this->phpVersion) as $line)
		{
			$string .= $this->dataPrompt . $this->dataColorizer->colorize(rtrim($line)) . PHP_EOL;
		}

		return $string;
	}
}

?>
