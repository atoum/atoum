<?php

namespace mageekguy\atoum\asserters\adapter\call;

use
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class adapter extends php\call
{
	protected $adapterAsserter = null;
	protected $adapter = null;

	public function __construct(asserters\adapter $adapterAsserter, test\adapter $adapter, $function)
	{
		$this->adapterAsserter = $adapterAsserter;
		$this->adapter = $adapter;

		parent::__construct($function);
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

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function withArguments()
	{
		$this->arguments = func_get_args();

		return $this;
	}

	public function getFirstCall()
	{
		$calls = $this->adapter->getCalls($this->function, $this->arguments);

		return ($calls === null ? null : key($calls));
	}

	public function getLastCall()
	{
		$calls = $this->adapter->getCalls($this->function, $this->arguments);

		return ($calls === null ? null : key(array_reverse($calls, true)));
	}
}
