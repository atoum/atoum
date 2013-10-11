<?php

namespace mageekguy\atoum\asserters\adapter;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\arguments,
	mageekguy\atoum\asserters\adapter\call\exceptions
;

abstract class call extends atoum\asserter
{
	protected $adapter = null;
	protected $call = null;
	protected $identicalCall = false;
	protected $beforeCalls = array();
	protected $afterCalls = array();

	public function __construct(asserter\generator $generator = null)
	{
		parent::__construct($generator);

		$this->call = new test\adapter\call();
	}

	public function reset()
	{
		if ($this->adapter !== null)
		{
			$this->adapter->resetCalls();
		}

		return $this;
	}

	public function setWith($adapter)
	{
		$this->adapter = $adapter;

		if ($this->adapter instanceof \mageekguy\atoum\test\adapter)
		{
			$this->pass();
		}
		else
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not a test adapter'), $this->getTypeOf($this->adapter)));
		}

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function before(self $call)
	{
		$this->beforeCalls[] = $call;

		return $this;
	}

	public function after(self $call)
	{
		$this->afterCalls[] = $call;

		return $this;
	}

	public function getCall()
	{
		return ($this->call === null ? null : clone $this->call);
	}

	public function once($failMessage = null)
	{
		return $this->exactly(1, $failMessage);
	}

	public function twice($failMessage = null)
	{
		return $this->exactly(2, $failMessage);
	}

	public function thrice($failMessage = null)
	{
		return $this->exactly(3, $failMessage);
	}

	public function atLeastOnce($failMessage = null)
	{
		if (($callsNumber = sizeof($this->checkBeforeAndAfterCalls())) >= 1)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is called 0 time'), $this->call) . $this->getCallsAsString());
		}

		return $this;
	}

	public function exactly($number, $failMessage = null)
	{
		if (($callsNumber = sizeof($this->checkBeforeAndAfterCalls())) === $number)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf(
					$this->getLocale()->__(
						'%s is called %d time instead of %d',
						'%s is called %d times instead of %d',
						$callsNumber
					),
					$this->call,
					$callsNumber,
					$number
				) . $this->getCallsAsString()
		);
		}

		return $this;
	}

	public function never($failMessage = null)
	{
		return $this->exactly(0, $failMessage);
	}

	protected function adapterIsSet()
	{
		if ($this->adapter === null)
		{
			throw new exceptions\logic('Adapter is undefined');
		}

		return $this;
	}

	protected function callIsSet()
	{
		if ($this->adapterIsSet()->call->getFunction() === null)
		{
			throw new exceptions\logic('Call is undefined');
		}

		return $this;
	}

	protected function checkBeforeAndAfterCalls()
	{
		if ($this->callIsSet()->identicalCall === false)
		{
			$calls = $this->adapter->getCallsEqualTo($this->call);
		}
		else
		{
			$calls = $this->adapter->getCallsIdenticalTo($this->call);
		}

		if (sizeof($calls) > 0 && (sizeof($this->beforeCalls) > 0 || sizeof($this->afterCalls) > 0))
		{
			$arrayOfCalls = $calls->toArray();

			foreach ($this->beforeCalls as $asserter)
			{
				foreach ($arrayOfCalls as $position => $call)
				{
					$afterAsserterCalls = $asserter->getAfterCalls($position);

					if (sizeof($afterAsserterCalls) > 0)
					{
						$this->pass();
					}
					else
					{
						$this->fail(sprintf($this->getLocale()->_('%s is not called before %s'), $this->call, $asserter->getCall()));
					}
				}
			}

			foreach ($this->afterCalls as $asserter)
			{
				foreach ($arrayOfCalls as $position => $call)
				{
					$previousAsserterCalls = $asserter->getPreviousCalls($position);

					if (sizeof($previousAsserterCalls) > 0)
					{
						$this->pass();
					}
					else
					{
						$this->fail(sprintf($this->getLocale()->_('%s is not called after %s'), $this->call, $asserter->getCall()));
					}
				}
			}
		}

		return $calls;
	}

	protected function setFunction($function)
	{
		$this->adapterIsSet()
			->call
				->setFunction($function)
				->unsetArguments()
		;

		$this->beforeCalls = array();
		$this->afterCalls = array();

		return $this;
	}

	protected function getFunction()
	{
		return $this->adapterIsSet()->call->getFunction();
	}

	protected function setArguments(array $arguments)
	{
		$this->callIsSet()->call->setArguments($arguments);
		$this->identicalCall = false;

		return $this;
	}

	protected function unsetArguments()
	{
		$this->callIsSet()->call->unsetArguments();
		$this->identicalCall = false;

		return $this;
	}

	protected function setIdenticalArguments(array $arguments)
	{
		$this->setArguments($arguments)->identicalCall = true;

		return $this;
	}

	protected function getPreviousCalls($position)
	{
		return $this->adapter->getPreviousCalls($this->call, $position, $this->identicalCall);
	}

	protected function getAfterCalls($position)
	{
		return $this->adapter->getAfterCalls($this->call, $position, $this->identicalCall);
	}

	protected function getCallsAsString()
	{
		$referenceCall = clone $this->call;
		$calls = $this->adapter->getCallsEqualTo($referenceCall->unsetArguments());

		return (sizeof($calls) <= 0 ? '' : PHP_EOL . rtrim($calls));
	}
}
