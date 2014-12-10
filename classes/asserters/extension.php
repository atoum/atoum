<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

class extension extends atoum\asserter
{
	protected $name = null;

	public function __construct(asserter\generator $generator = null, atoum\locale $locale = null)
	{
		parent::__construct($generator, null, $locale);
	}

	public function __toString()
	{
		return (string) $this->name;
	}

	public function __get($asserter)
	{
		switch (strtolower($asserter))
		{
			case 'isloaded':
				return $this->{$asserter}();

			default:
				return parent::__get($asserter);
		}
	}

	public function setWith($name)
	{
		$this->name = $name;

		return $this;
	}

	public function reset()
	{
		$this->name = null;

		return $this;
	}

	public function getName()
	{
		return $this->name;
	}

	public function isLoaded($failMessage = null)
	{
		if (extension_loaded($this->valueIsSet()->name) === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('PHP extension \'%s\' is not loaded', $this));
		}

		return $this;
	}

	protected function valueIsSet($message = 'Name of PHP extension is undefined')
	{
		if ($this->name === null)
		{
			throw new exceptions\logic($message);
		}

		return $this;
	}

	protected function pass()
	{
		return $this;
	}

	protected function fail($reason)
	{
		try
		{
			parent::fail($reason);
		}
		catch (asserter\exception $exception)
		{
			throw new test\exceptions\skip($reason);
		}
	}
}
