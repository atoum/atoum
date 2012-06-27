<?php

namespace mageekguy\atoum;

class locale
{
	protected $value = null;

	public function __construct($value = null)
	{
		if ($value !== null)
		{
			$this->set($value);
		}
	}

	public function set($value)
	{
		$this->value = (string) $value;

		return $this;
	}

	public function get()
	{
		return $this->value;
	}

	public function _($string)
	{
		return $string;
	}

	public function __($singular, $plural, $quantity)
	{
		return ($quantity <= 1 ? $singular : $plural);
	}
}
