<?php

namespace mageekguy\atoum\report\fields\runner\php;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report
;

class cli extends report\fields\runner\php
{
	const defaultTitlePrompt = '> ';
	const defaultVersionPrompt = '=> ';

	protected $titlePrompt = '';
	protected $versionPrompt = '';

	public function __construct(atoum\locale $locale = null, $titlePrompt = null, $versionPrompt = null)
	{
		parent::__construct($locale);

		if ($titlePrompt === null)
		{
			$titlePrompt = static::defaultTitlePrompt;
		}

		if ($versionPrompt === null)
		{
			$versionPrompt = static::defaultVersionPrompt;
		}

		$this
			->setTitlePrompt($titlePrompt)
			->setVersionPrompt($versionPrompt)
		;
	}

	public function setTitlePrompt($prompt)
	{
		return $this->setPrompt($this->titlePrompt, $prompt);
	}

	public function getTitlePrompt()
	{
		return $this->titlePrompt;
	}

	public function setVersionPrompt($prompt)
	{
		return $this->setPrompt($this->versionPrompt, $prompt);
	}

	public function getVersionPrompt()
	{
		return $this->versionPrompt;
	}

	public function __toString()
	{
		return $this->titlePrompt . sprintf($this->locale->_('PHP path: %s'), $this->phpPath) . PHP_EOL
			. $this->titlePrompt . $this->locale->_('PHP version:') . PHP_EOL
			. $this->versionPrompt . str_replace(PHP_EOL, PHP_EOL . $this->versionPrompt, rtrim($this->phpVersion)) . PHP_EOL
		;
	}

	protected function setPrompt(& $property, $prompt)
	{
		$property = (string) $prompt;

		return $this;
	}
}

?>
