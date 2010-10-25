<?php

namespace mageekguy\atoum\asserters;

class adapter extends \mageekguy\atoum\asserter
{
	protected $adapter = null;

	public function setWith($adapter)
	{
		$this->adapter = $adapter;

		if ($this->adapter instanceof \mageekguy\atoum\adapter === false)
		{
			$this->fail(sprintf($this->locale->_('%s is not an adapter'), $this->toString($this->adapter)));
		}
		else
		{
			$this->pass();

			return $this;
		}
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function call($function, array $args = null, $failMessage = null)
	{
		$calls = $this->adapterIsSet()->adapter->getCalls($function);

		if (sizeof($calls) <= 0)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('Function %s is not called'), $function));
		}
		else if ($args !== null && in_array($args, $calls) === false)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->__('Function %s is not called with this argument', 'Function %s is not called with these arguments', sizeof($args)), $function));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	protected function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new \logicException('Argument must be set at index 0');
		}

		return $this->setWith($arguments[0]);
	}

	protected function adapterIsSet()
	{
		if ($this->adapter === null)
		{
			throw new \logicException('Adapter is undefined');
		}

		return $this;
	}
}

?>
