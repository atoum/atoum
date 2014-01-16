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
	protected $isEvaluated = false;
	protected $lastAssertion = array('file' => null, 'line' => null);

	private static $instances = null;

	public function __construct(asserter\generator $generator = null)
	{
		parent::__construct($generator);

		$this->call = new test\adapter\call();

		if (self::$instances === null)
		{
			self::$instances = new \splObjectStorage();
		}

		self::$instances->attach($this);
	}

	public function __get($property)
	{
		switch (strtolower($property))
		{
			case 'once':
			case 'twice':
			case 'thrice':
			case 'never':
			case 'atleastonce':
				return $this->{$property}();

			default:
				return parent::__get($property);
		}
	}

	public function isEvaluated()
	{
		if ($this->call->getFunction() !== null && $this->isEvaluated === false)
		{
			throw new test\exceptions\runtime('Assertion is not evaluated in file \'' . $this->getLastAssertionFile() . '\' on line ' . $this->getLastAssertionLine());
		}

		return $this;
	}

	public function disableEvaluationChecking()
	{
		self::$instances->detach($this);

		return $this;
	}

	public function getLastAssertionFile()
	{
		return $this->lastAssertion['file'];
	}

	public function getLastAssertionLine()
	{
		return $this->lastAssertion['line'];
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

	public function before(call $call)
	{
		$this->setLastAssertion(__METHOD__);

		foreach (func_get_args() as $call)
		{
			$this->addBeforeCall($call);
		}

		return $this;
	}

	public function getBefore()
	{
		return $this->beforeCalls;
	}

	public function after(call $call)
	{
		$this->setLastAssertion(__METHOD__);

		foreach (func_get_args() as $call)
		{
			$this->addAfterCall($call);
		}

		return $this;
	}

	public function getAfter()
	{
		return $this->afterCalls;
	}

	public function getCall()
	{
		return clone $this->call;
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
		$this->isEvaluated = true;

		if (($callsNumber = sizeof($this->checkBeforeAndAfterCalls())) >= 1)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: sprintf($this->getLocale()->_('%s is called 0 time'), $this->call) . $this->getCallsAsString());
		}

		return $this;
	}

	public function exactly($number, $failMessage = null)
	{
		$this->isEvaluated = true;

		if (($callsNumber = sizeof($this->checkBeforeAndAfterCalls())) === $number)
		{
			$this->pass();
		}
		else
		{
			if ($failMessage === null)
			{
				$failMessage = sprintf(
					$this->getLocale()->__(
						'%s is called %d time instead of %d',
						'%s is called %d times instead of %d',
						$callsNumber
					),
					$this->call,
					$callsNumber,
					$number
				);

				if (sizeof($this->beforeCalls) > 0)
				{
					$beforeCalls = array();

					foreach ($this->beforeCalls as $asserter)
					{
						$beforeCalls[] = (string) $asserter->getCall();
					}

					$failMessage = sprintf('%s before %s', $failMessage, join(', ', $beforeCalls));
				}

				if (sizeof($this->afterCalls) > 0)
				{
					$afterCalls = array();

					foreach ($this->afterCalls as $asserter)
					{
						$afterCalls[] = (string) $asserter->getCall();
					}

					$failMessage = sprintf('%s after %s', $failMessage, join(', ', $afterCalls));
				}

				$failMessage .= $this->getCallsAsString();
			}

			$this->fail($failMessage);
		}

		return $this;
	}

	public function never($failMessage = null)
	{
		return $this->exactly(0, $failMessage);
	}

	public static function areEvaluated()
	{
		if (self::$instances !== null)
		{
			foreach (self::$instances as $asserter)
			{
				$asserter->isEvaluated();
			}

			self::$instances = null;
		}
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
				$pass = 0;

				foreach ($arrayOfCalls as $position => $call)
				{
					if ($asserter->hasAfterCalls($position) === true)
					{
						$pass++;
					}
					else
					{
						$calls->removeCall($call, $position);
						unset($arrayOfCalls[$position]);
					}
				}

				if ($pass == 0)
				{
					$this->fail(sprintf($this->getLocale()->_('%s is not called before %s'), $this->call, $asserter->getCall()));
				}
			}

			foreach ($this->afterCalls as $asserter)
			{
				$pass = 0;

				foreach ($arrayOfCalls as $position => $call)
				{
					if ($asserter->hasPreviousCalls($position) === true)
					{
						$pass++;
					}
					else
					{
						$calls->removeCall($call, $position);
						unset($arrayOfCalls[$position]);
					}
				}

				if ($pass == 0)
				{
					$this->fail(sprintf($this->getLocale()->_('%s is not called after %s'), $this->call, $asserter->getCall()));
				}
			}
		}

		return $calls;
	}

	protected function setFunction($function)
	{
		$this->setLastAssertion(__METHOD__)->isEvaluated = false;

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
		$this->setLastAssertion(__METHOD__)->isEvaluated = false;

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

	protected function hasPreviousCalls($position)
	{
		return $this->adapter->hasPreviousCalls($this->call, $position, $this->identicalCall);
	}

	protected function hasAfterCalls($position)
	{
		return $this->adapter->hasAfterCalls($this->call, $position, $this->identicalCall);
	}

	protected function getCalls($call)
	{
		return $this->adapter->getCalls($call);
	}

	protected function getCallsAsString()
	{
		$string = '';

		if (sizeof($this->beforeCalls) <= 0 && sizeof($this->afterCalls) <= 0)
		{
			$referenceCall = clone $this->call;
			$calls = $this->adapter->getCallsEqualTo($referenceCall->unsetArguments());

			$string = (sizeof($calls) <= 0 ? '' : PHP_EOL . rtrim($calls));
		}

		return $string;
	}

	protected function setLastAssertion($method)
	{
		foreach (debug_backtrace() as $backtrace)
		{
			if (isset($backtrace['function']) === true && isset($backtrace['file']) === true && isset($backtrace['line']) === true)
			{
				if (isset($backtrace['object']) === false || $backtrace['object'] !== $this)
				{
					return $this;
				}

				$this->lastAssertion['file'] = $backtrace['file'];
				$this->lastAssertion['line'] = $backtrace['line'];
			}
		}

		$this->lastAssertion['file'] = null;
		$this->lastAssertion['line'] = null;

		return $this;
	}

	private function addBeforeCall(call $call)
	{
		$this->beforeCalls[] = $call->disableEvaluationChecking();

		return $this;
	}

	private function addAfterCall(call $call)
	{
		$this->afterCalls[] = $call->disableEvaluationChecking();

		return $this;
	}
}
