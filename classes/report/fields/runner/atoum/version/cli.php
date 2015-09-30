<?php

namespace mageekguy\atoum\report\fields\runner\atoum\version;

use
	mageekguy\atoum\report,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer
;

class cli extends report\fields\runner\atoum\version
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
			->setVersionColorizer()
		;
	}

	public function __toString()
	{
		return
			$this->titlePrompt .
			sprintf(
				'%s: %s',
				$this->titleColorizer->colorize($this->locale->_('atoum version')),
				$this->versionColorizer->colorize(rtrim($this->version))
			) .
			PHP_EOL
		;
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
