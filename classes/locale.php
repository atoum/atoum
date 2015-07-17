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

	public function __toString()
	{
		return ($this->value === null ? 'unknown' : $this->value);
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
		return self::format($string, array_slice(func_get_args(), 1));
	}

	public function __($singular, $plural, $quantity)
	{
		return self::format($quantity <= 1 ? $singular : $plural, array_slice(func_get_args(), 3));
	}

	private static function format($string, $arguments)
	{
		if (sizeof($arguments) > 0)
		{
			$string = vsprintf($string, $arguments);
		}

		return $string;
	}
}
