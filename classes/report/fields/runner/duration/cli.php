<?php

namespace mageekguy\atoum\report\fields\runner\duration;

use
	\mageekguy\atoum,
	\mageekguy\atoum\locale,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\report\fields\runner\duration
;

class cli extends duration
{
	protected $prompt = null;
	protected $titleColorizer = null;
	protected $durationColorizer = null;

	public function __construct(prompt $prompt = null, colorizer $titleColorizer = null, colorizer $durationColorizer = null, locale $locale = null)
	{
		parent::__construct($locale);

		if ($prompt !== null)
		{
			$this->setPrompt($prompt);
		}

		if ($titleColorizer !== null)
		{
			$this->setTitleColorizer($titleColorizer);
		}

		if ($durationColorizer !== null)
		{
			$this->setDurationColorizer($durationColorizer);
		}
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

	public function setDurationColorizer(colorizer $colorizer)
	{
		$this->durationColorizer = $colorizer;

		return $this;
	}

	public function getDurationColorizer()
	{
		return $this->durationColorizer;
	}

	public function __toString()
	{
		$title = $this->locale->_('Running duration');

		if ($this->value === null)
		{
			$duration = $this->locale->_('unknown');
		}
		else
		{
			$duration = sprintf($this->locale->__('%4.2f second', '%4.2f seconds', $this->value), $this->value);
		}

		return $this->prompt . sprintf($this->locale->_('%s: %s.'), $this->titleColorizer === null ? $title : $this->titleColorizer->colorize($title), $this->durationColorizer === null ? $duration : $this->durationColorizer->colorize($duration)) . PHP_EOL;
	}
}

?>
