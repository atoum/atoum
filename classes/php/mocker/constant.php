<?php

namespace mageekguy\atoum\php\mocker;

use mageekguy\atoum;
use mageekguy\atoum\php\mocker;

class constant extends mocker
{
	public function __get($name)
	{
		if ($this->__isset($name) === false)
		{
			throw new exceptions\constant('Constant \'' . $name . '\' is not defined in namespace \'' . trim($this->getDefaultNamespace(), '\\') . '\'');
		}

		return $this->getAdapter()->constant($this->getDefaultNamespace() . $name);
	}

	public function __set($name, $value)
	{
		if (@$this->getAdapter()->define($this->getDefaultNamespace() . $name, $value) === false)
		{
			throw new exceptions\constant('Could not mock constant \'' . $name . '\' in namespace \'' . trim($this->getDefaultNamespace(), '\\') . '\'');
		}

		return $this;
	}

	public function __isset($name)
	{
		return $this->getAdapter()->defined($this->getDefaultNamespace() . $name);
	}

	public function __unset($name)
	{
		throw new exceptions\constant('Could not unset constant \'' . $name . '\' in namespace \'' . trim($this->getDefaultNamespace(), '\\') . '\'');
	}

	function addToTest(atoum\test $test)
	{
		$test->setPhpConstantMocker($this);

		return $this;
	}
}
