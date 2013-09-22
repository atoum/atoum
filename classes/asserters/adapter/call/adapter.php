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
	protected $callAsserter = null;
	protected $adapter = null;

	public function __construct(asserters\call\adapter $callAsserter, test\adapter $adapter, $function)
	{
		$this->callAsserter = $callAsserter;
		$this->adapter = $adapter;

		parent::__construct($function);
	}

	public function __call($method, $arguments)
	{
		return call_user_func_array(array($this->callAsserter, $method), $arguments);
	}

	public function getCallAsserter()
	{
		return $this->callAsserter;
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
