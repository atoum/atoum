<?php

namespace mageekguy\atoum\asserters\mock\call;

use
	mageekguy\atoum\test,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class adapter
{
	protected $mockAsserter = null;
	protected $adapter = null;
	protected $functionName = '';
	protected $arguments = null;

	public function __construct(asserters\mock $mockAsserter, test\adapter $adapter, $functionName)
	{
		$this->mockAsserter = $mockAsserter;
		$this->adapter = $adapter;
		$this->functionName = (string) $functionName;
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

	public function getFunctionName()
	{
		return $this->functionName;
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
		$calls = $this->adapter->getCalls($this->functionName, $this->arguments);

		return $calls === null ? null : key($calls);
	}

	public function getLastCall()
	{
		$calls = $this->adapter->getCalls($this->functionName, $this->arguments);

		return $calls === null ? null : key(array_reverse($calls, true));
	}
}

?>
