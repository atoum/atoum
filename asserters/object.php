<?php

namespace mageekguy\tests\unit\asserters;

class object extends \mageekguy\tests\unit\asserter
{
	protected $value = null;

	public function setWith($mixed)
	{
		$this->mixed = $mixed;
		return $this;
	}

	public function isInstanceOf($mixed)
	{
		if (is_object($mixed) === false && class_exists($mixed) === false && interface_exists($mixed) === false)
		{
			throw new \logicException('Argument of ' . __METHOD__ . '() must be a class instance or a class name');
		}

		$this->value instanceof $mixed ? $this->pass() : $this->fail(sprintf($this->locale->_('%s is not an instance of %s'), $this, is_string($mixed) === true ? $mixed : get_class($mixed)));

		return $this;
	}

	protected function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new \logicException('Argument must be set at index 0');
		}

		return $this->setWith($arguments[0]);
	}
}

?>
