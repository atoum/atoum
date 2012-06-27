<?php

namespace mageekguy\atoum\asserters\adapter\call;

use
	mageekguy\atoum\test,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class adapter
{
	protected $adapterAsserter = null;
	protected $adapter = null;
	protected $functionName = '';
	protected $arguments = null;

	public function __construct(asserters\adapter $adapterAsserter, test\adapter $adapter, $functionName)
	{
		$this->adapterAsserter = $adapterAsserter;
		$this->adapter = $adapter;
		$this->functionName = (string) $functionName;
	}

	public function __call($method, $arguments)
	{
		if (method_exists($this->adapterAsserter, $method) === false)
		{
			throw new exceptions\logic\invalidArgument('Method ' . get_class($this->adapterAsserter) . '::' . $method . '() does not exist');
		}

		return call_user_func_array(array($this->adapterAsserter, $method), $arguments);
	}

	public function getMockAsserter()
	{
		return $this->adapterAsserter;
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
