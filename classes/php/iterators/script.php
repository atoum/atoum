<?php

namespace mageekguy\atoum\php\iterators;

use
	\mageekguy\atoum\php
;

class script extends php\iterator
{
	public function __construct($string = '')
	{
		if ($string !== '')
		{
			$this->parseString($string);
		}
	}

	public function parseString($string)
	{
		foreach (token_get_all($string) as $token)
		{
			$this->append(new php\token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));
		}

		return $this;
	}
}

?>
