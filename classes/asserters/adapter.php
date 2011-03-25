<?php

namespace mageekguy\atoum\asserters;

use \mageekguy\atoum\exceptions;

class adapter extends \mageekguy\atoum\asserter
{
	protected $adapter = null;

	public function setWith($adapter, $label = null)
	{
		$this->setLabel($label)->adapter = $adapter;

		if ($this->adapter instanceof \mageekguy\atoum\test\adapter === false)
		{
			$this->fail(sprintf($this->locale->_('%s is not a test adapter'), $this->toString($this->adapter)));
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
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('function %s was not called'), $function));
		}
		else if ($args !== null && in_array($args, $calls) === false)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->__('function %s was not called with this argument', 'function %s was not called with these arguments', sizeof($args)), $function));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function notCall($function, array $args = null, $failMessage = null)
	{
		$calls = $this->adapterIsSet()->adapter->getCalls($function);

		if (sizeof($calls) <= 0)
		{
			$this->pass();
		}
		else if ($args === null)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('function %s was called'), $function));
		}
		else if (in_array($args, $calls) === true)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->__('function %s was called with this argument', 'function %s was called with these arguments', sizeof($args)), $function));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	protected function adapterIsSet()
	{
		if ($this->adapter === null)
		{
			throw new exceptions\logic('Adapter is undefined');
		}

		return $this;
	}
}

?>
