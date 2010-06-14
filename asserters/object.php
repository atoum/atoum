<?php

namespace mageekguy\tests\unit\asserters;

class object extends \mageekguy\tests\unit\asserters\variable
{
	public function isInstanceOf($mixed)
	{
		try
		{
			self::check($mixed, __METHOD__);
		}
		catch (\logicException $exception)
		{
			if (class_exists($mixed) === false && interface_exists($mixed) === false)
			{
				throw new \logicException('Argument of ' . __METHOD__ . '() must be a class instance or a class name');
			}
		}

		$this->mixed instanceof $mixed ? $this->pass() : $this->fail(sprintf($this->locale->_('%s is not an instance of %s'), $this, is_string($mixed) === true ? $mixed : self::toString($mixed)));

		return $this;
	}

	protected static function check($mixed, $method)
	{
		if (is_object($mixed) === false)
		{
			throw new \logicException('Argument of ' . $method . '() must be a class instance');
		}
	}
}

?>
