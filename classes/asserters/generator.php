<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\exceptions
;

class generator extends iterator
{
	public function __get($property)
	{
		switch (strtolower($property))
		{
			case 'yields':
				$asserter = new generator\child($this);

				$generator = $this->valueIsSet()->value;

				$childAsserter = $asserter->setWith($generator->current());
				$generator->next();

				return $childAsserter;
			case 'returns':
				$generator = $this->valueIsSet()->value;

				if (!method_exists($generator, 'getReturn')) {
					throw new exceptions\logic("The returns asserter could only be used with PHP>=7.0");
				}

				return $this->generator->__call('variable', array($generator->getReturn()));
			default:
				return parent::__get($property);
		}
	}

	public function setWith($value)
	{
		parent::setWith($value);

		if ($value instanceof \Generator)
		{
			$this->pass();
		}
		else
		{
			$this->fail($this->_('%s is not a generator', $this));
		}

		return $this;
	}
}
