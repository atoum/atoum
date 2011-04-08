<?php

namespace mageekguy\atoum\report\fields\test\duration;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\test\duration
{
	const titlePrompt = '=> ';

	protected $prompt = '';
	protected $singularLabel = '';
	protected $pluralLabel = '';

	public function __construct(atoum\locale $locale = null, $singularLabel = null, $pluralLabel = null, $prompt = null)
	{
		parent::__construct($locale);

		if ($singularLabel === null)
		{
			$singularLabel = $this->locale->_('Test duration: %4.2f second.');
		}

		if ($pluralLabel === null)
		{
			$pluralLabel = $this->locale->_('Test duration: %4.2f seconds.');
		}

		if ($prompt === null)
		{
			$prompt = static::titlePrompt;
		}

		$this
			->setSingularLabel($singularLabel)
			->setPluralLabel($pluralLabel)
			->setPrompt($prompt)
		;
	}

	public function setPrompt($prompt)
	{
		$this->prompt = (string) $prompt;

		return $this;
	}

	public function getPrompt()
	{
		return $this->prompt;

	}

	public function setSingularLabel($label)
	{
		return $this->setLabel($this->singularLabel, $label);
	}

	public function getSingularLabel()
	{
		return $this->singularLabel;
	}

	public function setPluralLabel($label)
	{
		return $this->setLabel($this->pluralLabel, $label);
	}

	public function getPluralLabel()
	{
		return $this->pluralLabel;
	}

	public function __toString()
	{
		$string = $this->prompt;

		if ($this->value === null)
		{
			$string .= $this->locale->_('Test duration: unknown.');
		}
		else
		{
			$string .= sprintf($this->locale->__($this->singularLabel, $this->pluralLabel, $this->value), $this->value);
		}

		$string .= PHP_EOL;

		return $string;
	}

	protected function setLabel(& $property, $label)
	{
		$property = (string) $label;

		return $this;
	}
}

?>
