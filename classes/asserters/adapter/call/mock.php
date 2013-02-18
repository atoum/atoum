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
	protected $adapterAsserter = null;

	public function __construct(asserters\adapter $adapterAsserter, atoum\mock\aggregator $mockAggregator, $function)
	{
		$this->adapterAsserter = $adapterAsserter;

		parent::__construct($function, null, $mockAggregator);
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
