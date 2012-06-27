<?php

namespace mageekguy\atoum\report\fields\runner\php\version;

use
	mageekguy\atoum\report,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer
;

class cli extends report\fields\runner\php\version
{
	protected $titlePrompt = null;
	protected $titleColorizer = null;
	protected $versionPrompt = null;
	protected $versionColorizer = null;

	public function __construct(prompt $titlePrompt = null, colorizer $titleColorizer = null, prompt $versionPrompt = null, colorizer $versionColorizer = null, locale $locale = null)
	{
		parent::__construct($locale);

		$this
			->setTitlePrompt($titlePrompt ?: new prompt())
			->setTitleColorizer($titleColorizer ?: new colorizer())
			->setVersionPrompt($versionPrompt ?: new prompt())
			->setVersionColorizer($versionColorizer ?: new colorizer())
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

	public function setVersionPrompt($prompt)
	{
		$this->versionPrompt = $prompt;

		return $this;
	}

	public function getVersionPrompt()
	{
		return $this->versionPrompt;
	}

	public function setVersionColorizer(colorizer $colorizer)
	{
		$this->versionColorizer = $colorizer;

		return $this;
	}

	public function getVersionColorizer()
	{
		return $this->versionColorizer;
	}

	public function __toString()
	{
		$string =
			$this->titlePrompt .
			sprintf(
				'%s:',
				$this->colorizeTitle($this->locale->_('PHP version'))
			) .
			PHP_EOL
		;

		foreach (explode(PHP_EOL, $this->version) as $line)
		{
			$string .= $this->versionPrompt . $this->colorizeVersion(rtrim($line)) . PHP_EOL;
		}

		return $string;
	}

	public function colorizeTitle($title)
	{
		return ($this->titleColorizer === null ? $title : $this->titleColorizer->colorize($title));
	}

	public function colorizeVersion($version)
	{
		return ($this->versionColorizer === null ? $version : $this->versionColorizer->colorize($version));
	}
}
