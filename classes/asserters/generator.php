<?php

namespace mageekguy\atoum\asserters;

class generator extends iterator
{
	public function __get($property)
	{
		switch (strtolower($property))
		{
			case 'yields':
				return $this->{$property}();

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

	public function yields()
	{
		$asserter = $this->generator->__call('variable', array($this->getValue()->current()));
		$this->getValue()->next();
		return $asserter;
	}
}
