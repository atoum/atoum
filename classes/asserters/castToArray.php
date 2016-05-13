<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools
;

class castToArray extends phpArray
{
	protected $adapter = null;

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

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setWith($value)
	{
		parent::setWith($value, false);

		$fail = false;
		$this->adapter->set_error_handler(function() use (& $fail) { $fail = true; });

		switch (true)
		{
			case $this->value instanceof \Iterator:
				$value = iterator_to_array($this->value);
				break;

			case $this->getAnalyzer()->isString($value):
				$value = $this->adapter->str_split($value);
				$fail = $value === false;
				break;

			default:
				$value = (array) $value;
		}

		$this->adapter->restore_error_handler();

		if ($fail)
		{
			$this->fail($this->getLocale()->_('%s could not be converted to array', $this->getTypeOf($this->value)));
		}

		$this->value = $value;

		return $this->pass();
	}
}
