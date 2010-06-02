<?php

namespace mageekguy\tests\unit;

class locale
{
	public function _($string)
	{
		return $string;
	}

	public function __($stringSingular, $stringPlural, $quantity)
	{
		return ($quantity <= 1 ? $stringSingular : $stringPlural);
	}
}

?>
