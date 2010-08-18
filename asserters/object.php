<?php

namespace mageekguy\atoum\asserters;

class object extends \mageekguy\atoum\asserters\variable
{
	public function isInstanceOf($mixed)
	{
		try
		{
			self::check($mixed, __METHOD__);
		}
		catch (\logicException $exception)
		{
			if (self::classExists($mixed) === false)
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

	protected static function classExists($mixed)
	{
		return (class_exists($mixed) === true || interface_exists($mixed) === true);
	}
}

?>
