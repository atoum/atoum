<?php

namespace mageekguy\atoum\report\fields\runner\php\path;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report,
	\mageekguy\atoum\locale,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer
;

class cli extends report\fields\runner\php\path
{
	protected $titlePrompt = null;
	protected $titleColorizer = null;
	protected $pathPrompt = null;
	protected $pathColorizer = null;

	public function __construct(prompt $titlePrompt = null, colorizer $titleColorizer = null, prompt $pathPrompt = null, colorizer $pathColorizer = null, locale $locale = null)
	{
		parent::__construct($locale);

		if ($titlePrompt === null)
		{
			$titlePrompt = new prompt();
		}

		if ($titleColorizer === null)
		{
			$titleColorizer = new colorizer();
		}

		if ($pathPrompt === null)
		{
			$pathPrompt = new prompt();
		}

		if ($pathColorizer === null)
		{
			$pathColorizer = new colorizer();
		}

		$this
			->setTitlePrompt($titlePrompt)
			->setTitleColorizer($titleColorizer)
			->setPathPrompt($pathPrompt)
			->setPathColorizer($pathColorizer)
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

	public function setPathPrompt($prompt)
	{
		$this->pathPrompt = $prompt;

		return $this;
	}

	public function getPathPrompt()
	{
		return $this->pathPrompt;
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
		$string = $this->titlePrompt . $this->colorizeTitle($this->locale->_('PHP path:')) . ' ' . $this->colorizePath($this->path) . PHP_EOL;

		return $string;
	}

	public function colorizeTitle($title)
	{
		return ($this->titleColorizer === null ? $title : $this->titleColorizer->colorize($title));
	}

	public function colorizePath($path)
	{
		return ($this->pathColorizer === null ? $path : $this->pathColorizer->colorize($path));
	}
}

?>
