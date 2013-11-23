<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

class castToArray extends asserters\phpArray
{
	protected $adapter = null;

	public function __construct(asserter\generator $generator = null, atoum\adapter $adapter = null)
	{
		parent::__construct($generator);

		$this->setAdapter($adapter);
	}

	public function setAdapter(atoum\adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new atoum\adapter();

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setWith($value, $checkType = true)
	{
		parent::setWith($value, false);

		if ($checkType === true)
		{
			if (self::isObject($value) === false)
			{
				$this->fail(sprintf($this->getLocale()->_('%s is not an object'), $this->getTypeOf($value)));
			}
			else
			{
				$fail = false;
				$this->adapter->set_error_handler(function() use (& $fail) { $fail = true; });

				switch (true)
				{
					case $this->value instanceof \Iterator:
						$value = iterator_to_array($this->value);
						break;

					default:
						$fail = true;
				}

				$this->adapter->restore_error_handler();

				if ($fail)
				{
					$this->fail(sprintf($this->getLocale()->_('%s could not be converted to array'), $this->getTypeOf($this->value)));
				}
			}
		}

		$this->value = $value;

		return $this->pass();
	}

	protected static function isObject($value)
	{
		return (is_object($value) === true);
	}
}
