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

	public function __construct()
	{
		parent::__construct();

		$this
			->setTitlePrompt()
			->setTitleColorizer()
			->setVersionPrompt()
			->setVersionColorizer()
		;
	}

	public function __toString()
	{
		$string =
			$this->titlePrompt .
			sprintf(
				'%s:',
				$this->titleColorizer->colorize($this->locale->_('PHP version'))
			) .
			PHP_EOL
		;

		foreach (explode(PHP_EOL, $this->version) as $line)
		{
			$string .= $this->versionPrompt . $this->versionColorizer->colorize(rtrim($line)) . PHP_EOL;
		}

		return $string;
	}

	public function setTitlePrompt(prompt $prompt = null)
	{
		$this->titlePrompt = $prompt ?: new prompt();

		return $this;
	}

	public function getTitlePrompt()
	{
		return $this->titlePrompt;
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

	public function setVersionPrompt(prompt $prompt = null)
	{
		$this->versionPrompt = $prompt ?: new prompt();

		return $this;
	}

	public function getVersionPrompt()
	{
		return $this->versionPrompt;
	}

	public function setVersionColorizer(colorizer $colorizer = null)
	{
		$this->versionColorizer = $colorizer ?: new colorizer();

		return $this;
	}

	public function getVersionColorizer()
	{
		return $this->versionColorizer;
	}
}
