<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\exceptions
;

class extension extends atoum\asserter
{
	protected $name = null;
	protected $adapter = null;

	public function __construct(atoum\asserter\generator $generator = null, atoum\adapter $adapter = null)
	{
		parent::__construct($generator);

		$this->setAdapter($adapter);
	}

	public function __toString()
	{
		return (string) $this->name;
	}

	public function setWith($name)
	{
		$this->name = $name;

		return $this;
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
		if ($this->valueIsSet()->adapter->extension_loaded($this->name) === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('PHP extension \'%s\' is not loaded'), $this));
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
		throw new test\exceptions\skip($reason);
	}
}
