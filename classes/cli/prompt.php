<?php

namespace mageekguy\atoum\cli;

class prompt
{
	protected $value = '';
	protected $colorizer = null;

	public function __construct($value = '', colorizer $colorizer = null)
	{
		if ($colorizer === null)
		{
			$colorizer = new colorizer();
		}

		$this
			->setValue($value)
			->setColorizer($colorizer)
		;
	}

	public function __toString()
	{
		return $this->colorizer->colorize($this->value);
	}

	public function setValue($value)
	{
		$this->value = (string) $value;

		return $this;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function setColorizer(colorizer $colorizer)
	{
		$this->colorizer = $colorizer;

		return $this;
	}

	public function getColorizer()
	{
		return $this->colorizer;
	}
}
