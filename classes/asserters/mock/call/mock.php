<?php

namespace mageekguy\atoum\asserters\mock\call;

use
	mageekguy\atoum,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class mock
{
	protected $mockAsserter = null;
	protected $mockAggregator = null;
	protected $methodName = '';
	protected $arguments = null;

	public function __construct(asserters\mock $mockAsserter, atoum\mock\aggregator $mockAggregator, $methodName)
	{
		$this->mockAsserter = $mockAsserter;
		$this->mockAggregator = $mockAggregator;
		$this->methodName = (string) $methodName;
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

	public function on(atoum\mock\aggregator $mockAggregator)
	{
		$this->mockAggregator = $mockAggregator;

		return $this;
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

?>
