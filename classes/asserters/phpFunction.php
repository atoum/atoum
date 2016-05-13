<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\asserters\adapter\call
;

class phpFunction extends adapter\call
{
	public function setWithTest(test $test)
	{
		parent::setWithTest($test);

		$function = $this->call->getFunction();

		if ($function !== null)
		{
			$this->setWith($function);
		}

		return $this;
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

	protected function setFunction($function)
	{
		if ($this->test !== null)
		{
			$lastNamespaceSeparator = strrpos($function, '\\');

			if ($lastNamespaceSeparator !== false)
			{
				$function = substr($function, $lastNamespaceSeparator + 1);
			}

			$function = $this->test->getTestedClassNamespace() . '\\' . $function;
		}

		return parent::setFunction($function);
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
