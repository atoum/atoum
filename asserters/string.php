<?php

namespace mageekguy\atoum\asserters;

class string extends \mageekguy\atoum\asserters\variable
{
	protected static function check($mixed, $method)
	{
		if (is_string($mixed) === false)
		{
			throw new \logicException('Argument of ' . $method . '() must be a string');
		}
	}
}

?>
