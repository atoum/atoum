<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class adapter extends atoum\asserter
{
	protected $adapter = null;
	protected $calledFunctionName = null;
	protected $calledFunctionArguments = null;

	public function getTestedFunctionName()
	{
		return $this->calledFunctionName;
	}

	public function getTestedFunctionArguments()
	{
		return $this->calledFunctionArguments;
	}

	public function setWith($adapter)
	{
		$this->adapter = $adapter;

		if ($this->adapter instanceof \mageekguy\atoum\test\adapter === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not a test adapter'), $this->getTypeOf($this->adapter)));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function reset()
	{
		if ($this->adapter !== null)
		{
			$this->adapter->resetCalls();
		}

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function call($method)
	{
		$this->adapterIsSet()->calledFunctionName = $method;

		$this->calledFunctionArguments = null;

		return $this;
	}

	public function withArguments()
	{
		$this->calledFunctionNameIsSet()->calledFunctionArguments = func_get_args();

		return $this;
	}

	public function once($failMessage = null)
	{
		$callNumber = sizeof($this->calledFunctionNameIsSet()->adapter->getCalls($this->calledFunctionName, $this->calledFunctionArguments));

		if ($callNumber !== 1)
		{
			$this->fail(
				$failMessage !== null
				?  $failMessage
				:  sprintf(
						$this->getLocale()->__(
							'function %s() is called %d time instead of 1',
							'function %s() is called %d times instead of 1',
							$callNumber
						),
						$this->calledFunctionName,
						$callNumber
					)
			);
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function atLeastOnce($failMessage = null)
	{
		$callNumber = sizeof($this->calledFunctionNameIsSet()->adapter->getCalls($this->calledFunctionName, $this->calledFunctionArguments));

		if ($callNumber < 1)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('function %s() is called 0 time'), $this->calledFunctionName));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function exactly($number, $failMessage = null)
	{
		$callNumber = sizeof($this->calledFunctionNameIsSet()->adapter->getCalls($this->calledFunctionName, $this->calledFunctionArguments));

		if ($number != $callNumber)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf(
					$this->getLocale()->__(
						'function %s() is called %d time instead of %d',
						'function %s() is called %d times instead of %d',
						$callNumber
					),
					$this->calledFunctionName,
					$callNumber,
					$number
				)
			);
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function never($failMessage = null)
	{
		$callNumber = sizeof($this->calledFunctionNameIsSet()->adapter->getCalls($this->calledFunctionName, $this->calledFunctionArguments));

		if ($callNumber != 0)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf(
					$this->getLocale()->__(
						'function %s() is called %d time instead of 0',
						'function %s() is called %d times instead of 0',
						$callNumber
					),
					$this->calledFunctionName,
					$callNumber
				)
			);
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

	protected function calledFunctionNameIsSet()
	{
		if ($this->adapterIsSet()->calledFunctionName === null)
		{
			throw new exceptions\logic('Called function is undefined');
		}

		return $this;
	}
}

?>
