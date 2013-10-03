<?php

namespace mageekguy\atoum\asserters\adapter\call;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class mock extends php\call
{
	protected $callAsserter = null;

	public function __construct(asserters\call\adapter $callAsserter, atoum\mock\aggregator $mockAggregator, $function)
	{
		$this->callAsserter = $callAsserter;

		parent::__construct($function, null, $mockAggregator);
	}

	public function __call($method, $arguments)
	{
		return call_user_func_array(array($this->callAsserter, $method), $arguments);
	}

	public function getCallAsserter()
	{
		return $this->callAsserter;
	}

	public function withArguments()
	{
		$this->arguments = func_get_args();

		return $this;
	}

	public function getFirstCall()
	{
		$calls = $this->object->getMockController()->getCalls($this->function, $this->arguments);

		return ($calls === null ? null : key($calls));
	}

	public function getLastCall()
	{
		$calls = $this->object->getMockController()->getCalls($this->function, $this->arguments);

		return ($calls === null ? null : key(array_reverse($calls, true)));
	}
}
