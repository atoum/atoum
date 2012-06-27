<?php

namespace mageekguy\atoum\asserters\mock\call;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class mock extends php\call
{
	protected $mockAsserter = null;

	public function __construct(asserters\mock $mockAsserter, atoum\mock\aggregator $mockAggregator, $function)
	{
		$this->mockAsserter = $mockAsserter;

		parent::__construct($function, null, $mockAggregator);
	}

	public function __call($function, $arguments)
	{
		if (method_exists($this->mockAsserter, $function) === false)
		{
			throw new exceptions\logic\invalidArgument('Method ' . get_class($this->mockAsserter) . '::' . $function . '() does not exist');
		}

		return call_user_func_array(array($this->mockAsserter, $function), $arguments);
	}

	public function getMockAsserter()
	{
		return $this->mockAsserter;
	}

	public function withArguments()
	{
		return parent::setArguments(func_get_args());
	}

	public function on(atoum\mock\aggregator $mockAggregator)
	{
		return $this->setObject($mockAggregator);
	}

	public function getFirstCall()
	{
		$calls = $this->getObject()->getMockController()->getCalls($this->function, $this->arguments);

		return $calls === null ? null : key($calls);
	}

	public function getLastCall()
	{
		$calls = $this->getObject()->getMockController()->getCalls($this->function, $this->arguments);

		return $calls === null ? null : key(array_reverse($calls, true));
	}
}
