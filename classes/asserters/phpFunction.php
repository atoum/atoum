<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\asserters\adapter\call
;

class phpFunction extends atoum\asserters\adapter\call
{
	public function setWithTest(atoum\test $test)
	{
		if ($this->callIsSet()->adapter === null)
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

	public function wasCalled()
	{
		return $this->unsetArguments();
	}

	public function wasCalledWithArguments()
	{
		return $this->setArguments(func_get_args());
	}

	public function wasCalledWithIdenticalArguments()
	{
		return $this->setIdenticalArguments(func_get_args());
	}

	public function wasCalledWithAnyArguments()
	{
		return $this->unsetArguments();
	}

	public function wasCalledWithoutAnyArgument()
	{
		return $this->setArguments(array());
	}

	protected function adapterIsSet()
	{
		try
		{
			return parent::adapterIsSet();
		}
		catch (call\exceptions\logic $exception)
		{
			throw new exceptions\logic('Function is undefined');
		}
	}

	protected function callIsSet()
	{
		try
		{
			return parent::callIsSet();
		}
		catch (call\exceptions\logic $exception)
		{
			throw new exceptions\logic('Call is undefined');
		}
	}
}
