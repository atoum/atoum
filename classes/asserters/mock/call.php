<?php

namespace mageekguy\atoum\asserters\mock;

use
	mageekguy\atoum\mock,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class call
{
	protected $mockAsserter = null;
	protected $mockAggregator = null;
	protected $methodName = '';
	protected $arguments = null;

	public function __construct(asserters\mock $mockAsserter, mock\aggregator $mockAggregator, $methodName)
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

	public function on(mock\aggregator $mockAggregator)
	{
		$this->mockAggregator = $mockAggregator;

		return $this;
	}
}

?>
