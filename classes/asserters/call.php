<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\exceptions
;

abstract class call extends atoum\asserter
{
	protected $call;
	protected $arguments;

	public function __call($method, $arguments)
	{
		switch ($method)
		{
			case 'with':
			case 'and':
				return $this->callIsSet();

			case 'arguments':
				$asserter = $this->getArguments();

				if (sizeof($arguments) === 0) {
					return $asserter;
				}

				return $asserter[$arguments[0]];

			default:
				return parent::__call($method, $arguments);
		}
	}

	public function __get($asserter)
	{
		return $this->__call($asserter, array());
	}

	public function setWith($call)
	{
		$this->call = $call;

		if ($this->call instanceof php\call === false)
		{
			$this->fail(sprintf($this->generator->getLocale()->_('%s is not a php call'), $this->getTypeOf($this->call)));
		}

		return $this;
	}

	protected function callIsSet()
	{
		if ($this->call === null)
		{
			throw new exceptions\logic('Called function is undefined');
		}

		return $this;
	}

	public function getCall()
	{
		return ($this->call === null ? null : clone $this->call);
	}

	public function withArguments()
	{
		$this->callIsSet()->call->setArguments(func_get_args())->notIdentical();

		return $this;
	}

	public function withIdenticalArguments()
	{
		$this->callIsSet()->call->setArguments(func_get_args())->identical();

		return $this;
	}

	public function withAnyArguments()
	{
		$this->callIsSet()->call->unsetArguments();

		return $this;
	}

	public function withoutAnyArgument()
	{
		$this->callIsSet()->call->setArguments(array());

		return $this;
	}

	public function withAtLeastArguments(array $arguments)
	{
		$this->callIsSet()->call->setArguments($arguments)->notIdentical();

		return $this;
	}

	public function withAtLeastIdenticalArguments(array $arguments)
	{
		$this->callIsSet()->call->setArguments($arguments)->identical();

		return $this;
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

	public function never($failMessage = null)
	{
		return $this->exactly(0, $failMessage);
	}

	abstract public function getArguments();

	abstract public function exactly($number, $failMessage = null);

	abstract public function atLeastOnce($failMessage = null);
}
