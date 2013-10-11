<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\exceptions
;

class phpFunction extends atoum\asserters\adapter\call
{
	public function setWithTest(atoum\test $test)
	{
		if ($this->adapter === null)
		{
			parent::setWith(clone php\mocker::getAdapter());
		}

		$this->setFunction($test->getTestedClassNamespace() . '\\' . $this->getFunction());

		return parent::setWithTest($test);
	}

	public function setWith($function)
	{
		return parent::setWith(clone php\mocker::getAdapter())->setFunction($function);
	}

	public function isCalled()
	{
		return $this->unsetArguments();
	}

	public function isCalledWithArguments()
	{
		return $this->setArguments(func_get_args());
	}

	public function isCalledWithIdenticalArguments()
	{
		return $this->setIdenticalArguments(func_get_args());
	}

	public function isCalledWithAtLeastArguments(array $arguments)
	{
		return $this->setArguments($arguments);
	}

	public function isCalledWithAtLeastIdenticalArguments(array $arguments)
	{
		return $this->setIdenticalArguments($arguments);
	}

	public function isCalledWithAnyArguments()
	{
		return $this->unsetArguments();
	}

	public function isCalledWithoutAnyArgument()
	{
		return $this->withAtLeastArguments(array());
	}
}
