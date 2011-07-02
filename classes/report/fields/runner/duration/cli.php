<?php

namespace mageekguy\atoum\report\fields\runner\duration;

use
	\mageekguy\atoum,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\report\fields\runner\duration
;

class cli extends duration
{
	const defaultPrompt = '> ';

	protected $prompt = null;
	protected $titleColorizer = null;
	protected $dataColorizer = null;

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
		$title = $this->locale->_('Running duration');

		if ($this->value === null)
		{
			$data = $this->locale->_('unknown');
		}
		else
		{
			$data = sprintf($this->locale->__('%4.2f second', '%4.2f seconds', $this->value), $this->value);
		}

		return $this->prompt . sprintf($this->locale->_('%s: %s.'), $this->titleColorizer->colorize($title), $this->dataColorizer->colorize($data)) . PHP_EOL;
	}
}

?>
