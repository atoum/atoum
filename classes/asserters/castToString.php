<?php

namespace mageekguy\atoum\asserters;

use mageekguy\atoum;
use mageekguy\atoum\asserter;
use mageekguy\atoum\asserters;
use mageekguy\atoum\tools;

class castToString extends asserters\phpString
{
	private $adapter;

	public function __construct(asserter\generator $generator = null, tools\variable\analyzer $analyzer = null, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($generator, $analyzer, $locale);

		$this->setAdapter($adapter);
	}

	public function setAdapter(atoum\adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new atoum\adapter();

		return $this;
	}

	public function setWith($value, $charlist = null, $checkType = true)
	{
		parent::setWith($value, $charlist, false);

		if ($checkType === true)
		{
			if (self::isObject($value) === false)
			{
				$this->fail($this->_('%s is not an object', $this->getTypeOf($value)));
			}
			else
			{
				$fail = false;
				$this->adapter->set_error_handler(function() use (& $fail) { $fail = true; });

				switch (true)
				{
					case $this->value instanceof \DOMDocument:
						$value = $this->value->saveXML();
						break;

					default:
						$value = (string) $this->value;
				}

				$this->adapter->restore_error_handler();

				if ($fail)
				{
					$this->fail(sprintf($this->getLocale()->_('%s could not be converted to string'), $this->getTypeOf($this->value)));
				}

				$this->value = $value;
			}
		}

		return $this->pass();
	}

	protected static function isObject($value)
	{
		return (is_object($value) === true);
	}
}
