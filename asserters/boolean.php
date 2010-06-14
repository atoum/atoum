<?php

namespace mageekguy\tests\unit\asserters;

class boolean extends \mageekguy\tests\unit\asserters\variable
{
	public function isTrue()
	{
		return $this->isEqualTo(true);
	}

	public function isFalse()
	{
		return $this->isEqualTo(false);
	}

	protected static function check($mixed, $method)
	{
		if (is_bool($mixed) === false)
		{
			throw new \logicException('Argument of ' . $method . '() must be a boolean');
		}
	}
}

?>
