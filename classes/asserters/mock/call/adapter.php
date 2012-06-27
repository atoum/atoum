<?php

namespace mageekguy\atoum\asserters\mock\call;

use
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class adapter extends php\call
{
	protected $mockAsserter = null;
	protected $adapter = null;

	public function __construct(asserters\mock $mockAsserter, test\adapter $adapter, $function)
	{
		$this->mockAsserter = $mockAsserter;
		$this->adapter = $adapter;

		parent::__construct($function);
	}

	public function __call($method, $arguments)
	{
		if (method_exists($this->mockAsserter, $method) === false)
		{
			throw new exceptions\logic\invalidArgument('Method ' . get_class($this->mockAsserter) . '::' . $method . '() does not exist');
		}

		return call_user_func_array(array($this->mockAsserter, $method), $arguments);
	}

	public function getMockAsserter()
	{
		return $this->mockAsserter;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function withArguments()
	{
		return parent::setArguments(func_get_args());
	}

	public function getFirstCall()
	{
		$calls = $this->adapter->getCalls($this->function, $this->arguments);

		return $calls === null ? null : key($calls);
	}

	public function getLastCall()
	{
		$calls = $this->adapter->getCalls($this->function, $this->arguments);

		return $calls === null ? null : key(array_reverse($calls, true));
	}
}
