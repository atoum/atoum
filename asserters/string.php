<?php

namespace mageekguy\tests\unit\asserters;

class string extends \mageekguy\tests\unit\asserters\variable
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
