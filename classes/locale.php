<?php

namespace mageekguy\atoum;

class locale
{
	public function _($string)
	{
		return $string;
	}

	public function __($singular, $plural, $quantity)
	{
		return ($quantity <= 1 ? $singular : $plural);
	}
}

?>
