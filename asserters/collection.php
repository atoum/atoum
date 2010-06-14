<?php

namespace mageekguy\tests\unit\asserters;

class collection extends \mageekguy\tests\unit\asserters\variable
{
	protected static function check($mixed, $method)
	{
		if (is_array($mixed) === false)
		{
			throw new \logicException('Argument of ' . $method . '() must be an array');
		}
	}
}

?>
