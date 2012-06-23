<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class afterDestructionOf extends atoum\asserter
{
	public function setWith($value)
	{
		if (is_object($value) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not an object'), $this->getTypeOf($value)));
		}
		else if (method_exists($value, '__destruct') === false)
		{
			$this->fail(sprintf($this->getLocale()->_('Destructor of class %s is undefined'), get_class($value)));
		}
		else
		{
			$value->__destruct();

			$this->pass();
		}

		return $this;
	}
}
