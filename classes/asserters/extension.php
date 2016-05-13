<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

class extension extends asserter
{
	protected $name = null;
	protected $phpExtensionFactory = null;

	public function __construct(asserter\generator $generator = null, atoum\locale $locale = null, \closure $phpExtensionFactory = null)
	{
		parent::__construct($generator, null, $locale);

		$this->setPhpExtensionFactory($phpExtensionFactory);
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
		$extension = call_user_func($this->phpExtensionFactory, $this->valueIsSet()->name);

		try
		{
			$extension->requireExtension();

			$this->pass();
		}
		catch (atoum\php\exception $exception)
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

	public function setPhpExtensionFactory(\closure $factory = null)
	{
		$this->phpExtensionFactory = $factory ?: function($extensionName) {
			return new atoum\php\extension($extensionName);
		};

		return $this;
	}
}
