<?php

namespace mageekguy\atoum\report\fields\runner\php;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report,
	\mageekguy\atoum\locale,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer
;

class cli extends report\fields\runner\php
{
	protected $titlePrompt = null;
	protected $titleColorizer = null;
	protected $dataPrompt = null;
	protected $dataColorizer = null;

	public function __construct(prompt $titlePrompt = null, colorizer $titleColorizer = null, prompt $dataPrompt = null, colorizer $dataColorizer = null, locale $locale = null)
	{
		parent::__construct($locale);

		if ($titlePrompt !== null)
		{
			$this->setTitlePrompt($titlePrompt);
		}

		if ($titleColorizer !== null)
		{
			$this->setTitleColorizer($titleColorizer);
		}

		if ($dataPrompt !== null)
		{
			$this->setDataPrompt($dataPrompt);
		}

		if ($dataColorizer !== null)
		{
			$this->setDataColorizer($dataColorizer);
		}
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
			$this->titlePrompt . $this->colorizeTitle($this->locale->_('PHP path:')) . ' ' . $this->colorizeData($this->phpPath) . PHP_EOL
			.
			$this->titlePrompt . $this->colorizeTitle($this->locale->_('PHP version:')) . PHP_EOL
		;

		foreach (explode(PHP_EOL, $this->phpVersion) as $line)
		{
			$string .= $this->dataPrompt . $this->colorizeData(rtrim($line)) . PHP_EOL;
		}

		return $string;
	}

	public function colorizeTitle($title)
	{
		return ($this->titleColorizer === null ? $title : $this->titleColorizer->colorize($title));
	}

	public function colorizeData($data)
	{
		return ($this->dataColorizer === null ? $data : $this->dataColorizer->colorize($data));
	}
}

?>
