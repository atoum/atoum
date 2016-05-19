<?php

namespace mageekguy\atoum\asserters\adapter;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\variable,
	mageekguy\atoum\asserters\adapter\call\exceptions
;

abstract class call extends atoum\asserter
{
	protected $adapter = null;
	protected $call = null;
	protected $identicalCall = false;
	protected $beforeCalls = array();
	protected $afterCalls = array();
	protected $trace = array('file' => null, 'line' => null);
	protected $manager = null;

	public function __construct(asserter\generator $generator = null, variable\analyzer $analyzer = null, atoum\locale $locale = null)
	{
		parent::__construct($generator, $analyzer, $locale);

		$this->setCall();
	}

	public function __get($property)
	{
		if (is_numeric($property) === true)
		{
			return $this->exactly($property);
		}
		else switch (strtolower($property))
		{
			case 'once':
			case 'twice':
			case 'thrice':
			case 'never':
			case 'atleastonce':
			case 'wascalled':
			case 'wasnotcalled':
				return $this->{$property}();

			default:
				return parent::__get($property);
		}
	}

	public function setManager(call\manager $manager)
	{
		$this->manager = $manager;

		return $this;
	}

	public function setCall(test\adapter\call $call = null)
	{
		if ($call === null)
		{
			$call = new test\adapter\call();
		}

		if ($this->call !== null)
		{
			$call->copy($this->call);
		}

		$this->call = $call;

		return $this;
	}

	public function getCall()
	{
		return clone $this->call;
	}

	public function disableEvaluationChecking()
	{
		return $this->removeFromManager();
	}

	public function getLastAssertionFile()
	{
		return $this->trace['file'];
	}

	public function getLastAssertionLine()
	{
		return $this->trace['line'];
	}

	public function reset()
	{
		if ($this->adapter !== null)
		{
			$this->adapter->resetCalls();
		}

		return $this;
	}

	public function setWithTest(test $test)
	{
		$this->setManager($test->getAsserterCallManager());

		return parent::setWithTest($test);
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
			$this->fail($this->_('%s is not a test adapter', $this->getTypeOf($this->adapter)));
		}

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function before(call $call)
	{
		$this->setTrace();

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
		$this->setTrace();

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
		$this->removeFromManager();

		if ($this->countBeforeAndAfterCalls() >= 1)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is called 0 time', $this->call) . $this->getCallsAsString());
		}

		return $this;
	}

	public function exactly($number, $failMessage = null)
	{
		$callsNumber = $this->removeFromManager()->countBeforeAndAfterCalls();

		if ((int) $number != $number)
		{
			throw new atoum\exceptions\logic\invalidArgument('Argument 1 of ' . __FUNCTION__ . ' must be an integer');
		}

		if ($callsNumber == $number)
		{
			$this->pass();
		}
		else
		{
			if ($failMessage === null)
			{
				$failMessage = $this->__('%s is called %d time instead of %d', '%s is called %d times instead of %d', $callsNumber, $this->call, $callsNumber, $number);

				if (sizeof($this->beforeCalls) > 0)
				{
					$beforeCalls = array();

					foreach ($this->beforeCalls as $asserter)
					{
						$beforeCalls[] = (string) $asserter->getCall();
					}

					$failMessage = $this->_('%s before %s', $failMessage, join(', ', $beforeCalls));
				}

				if (sizeof($this->afterCalls) > 0)
				{
					$afterCalls = array();

					foreach ($this->afterCalls as $asserter)
					{
						$afterCalls[] = (string) $asserter->getCall();
					}

					$failMessage = $this->_('%s after %s', $failMessage, join(', ', $afterCalls));
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

	public function getFunction()
	{
		return $this->call->getFunction();
	}

	public function getArguments()
	{
		return $this->adapterIsSet()->call->getArguments();
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

	protected function countBeforeAndAfterCalls()
	{
		$calls = $this->callIsSet()->adapter->getCalls($this->call, $this->identicalCall);

		if (sizeof($calls) > 0 && (sizeof($this->beforeCalls) > 0 || sizeof($this->afterCalls) > 0))
		{
			foreach ($this->beforeCalls as $asserter)
			{
				$pass = false;

				foreach ($calls->getTimeline() as $position => $call)
				{
					$hasAfterCalls = $asserter->hasAfterCalls($position);

					if ($hasAfterCalls === false)
					{
						$calls->removeCall($call, $position);
					}
					else if ($pass === false)
					{
						$pass = $hasAfterCalls;
					}
				}

				if ($pass === false)
				{
					$this->fail($this->_('%s is not called before %s', $this->call, $asserter->getCall()));
				}
			}

			foreach ($this->afterCalls as $asserter)
			{
				$pass = false;

				foreach ($calls->getTimeline() as $position => $call)
				{
					$hasPreviousCalls = $asserter->hasPreviousCalls($position);

					if ($hasPreviousCalls === false)
					{
						$calls->removeCall($call, $position);
					}
					else if ($pass === false)
					{
						$pass = $hasPreviousCalls;
					}
				}

				if ($pass === false)
				{
					$this->fail($this->_('%s is not called after %s', $this->call, $asserter->getCall()));
				}
			}
		}

		return sizeof($calls);
	}

	protected function setFunction($function)
	{
		$this
			->adapterIsSet()
			->setTrace()
			->addToManager()
			->call
				->setFunction($function)
				->unsetArguments()
		;

		$this->beforeCalls = array();
		$this->afterCalls = array();

		return $this;
	}

	protected function setArguments(array $arguments)
	{
		$this
			->adapterIsSet()
			->callIsSet()
			->setTrace()
			->call
				->setArguments($arguments)
		;

		$this->identicalCall = false;

		return $this;
	}

	protected function unsetArguments()
	{
		$this
			->adapterIsSet()
			->callIsSet()
			->setTrace()
			->call
				->unsetArguments()
		;

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
			$calls = $this->adapter->getCallsEqualTo($this->call->unsetArguments());

			$string = (sizeof($calls) <= 0 ? '' : PHP_EOL . rtrim($calls));
		}

		return $string;
	}

	protected function setTrace()
	{
		foreach (debug_backtrace() as $trace)
		{
			if (isset($trace['function']) === true && isset($trace['file']) === true && isset($trace['line']) === true)
			{
				if (isset($trace['object']) === false || $trace['object'] !== $this)
				{
					return $this;
				}

				$this->trace['file'] = $trace['file'];
				$this->trace['line'] = $trace['line'];
			}
		}

		$this->trace['file'] = null;
		$this->trace['line'] = null;

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

	private function addToManager()
	{
		if ($this->manager !== null)
		{
			$this->manager->add($this);
		}

		return $this;
	}

	private function removeFromManager()
	{
		if ($this->manager !== null)
		{
			$this->manager->remove($this);
		}

		return $this;
	}
}
