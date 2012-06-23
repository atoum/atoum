<?php

namespace mageekguy\atoum\report\fields\runner\php\path;

use
	mageekguy\atoum,
	mageekguy\atoum\report,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer
;

class cli extends report\fields\runner\php\path
{
	protected $prompt = null;
	protected $titleColorizer = null;
	protected $pathColorizer = null;

	public function __construct(prompt $prompt = null, colorizer $titleColorizer = null, colorizer $pathColorizer = null, locale $locale = null)
	{
		parent::__construct($locale);

		$this
			->setPrompt($prompt ?: new prompt())
			->setTitleColorizer($titleColorizer ?: new colorizer())
			->setPathColorizer($pathColorizer ?: new colorizer())
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

	public function setPathColorizer(colorizer $colorizer)
	{
		$this->pathColorizer = $colorizer;

		return $this;
	}

	public function getPathColorizer()
	{
		return $this->pathColorizer;
	}

	public function __toString()
	{
		return
			$this->prompt .
			sprintf(
				$this->locale->_('%1$s: %2$s'),
				$this->titleColorizer->colorize($this->locale->_('PHP path')),
				$this->pathColorizer->colorize($this->path)
			) .
			PHP_EOL
		;
	}
}
