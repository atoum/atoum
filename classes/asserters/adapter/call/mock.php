<?php

namespace mageekguy\atoum\asserters\adapter\call;

use
	mageekguy\atoum,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class mock
{
	protected $adapterAsserter = null;
	protected $mockAggregator = null;
	protected $methodName = '';
	protected $arguments = null;

	public function __construct(asserters\adapter $adapterAsserter, atoum\mock\aggregator $mockAggregator, $methodName)
	{
		$this->adapterAsserter = $adapterAsserter;
		$this->mockAggregator = $mockAggregator;
		$this->methodName = (string) $methodName;
	}

	public function __call($method, $arguments)
	{
		if (method_exists($this->adapterAsserter, $method) === false)
		{
			throw new exceptions\logic\invalidArgument('Method ' . get_class($this->adapterAsserter) . '::' . $method . '() does not exist');
		}

		return call_user_func_array(array($this->adapterAsserter, $method), $arguments);
	}

	public function getAdapterAsserter()
	{
		return $this->adapterAsserter;
	}

	public function getMockAggregator()
	{
		return $this->mockAggregator;
	}

	public function getMethodName()
	{
		return $this->methodName;
	}

	public function withArguments()
	{
		$this->arguments = func_get_args();

		return $this;
	}

	public function getArguments()
	{
		return $this->arguments;
	}

	public function getFirstCall()
	{
		$calls = $this->mockAggregator->getMockController()->getCalls($this->methodName, $this->arguments);

		return $calls === null ? null : key($calls);
	}

	public function getLastCall()
	{
		$calls = $this->mockAggregator->getMockController()->getCalls($this->methodName, $this->arguments);

		return $calls === null ? null : key(array_reverse($calls, true));
	}
}
