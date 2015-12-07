<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\phpunit\asserter,
	mageekguy\atoum\test\phpunit\asserter\selector
;

class assertEmpty extends asserter
{
	protected $asserterSelector;

	public function __construct(atoum\asserter\generator $generator = null)
	{
		parent::__construct($generator);

		$this->setAsserterSelector();
	}

	public function setAsserterSelector(selector $selector = null)
	{
		$this->asserterSelector = $selector ?: new selector\emptyness();
	}

	public function getAsserterSelector()
	{
		return $this->asserterSelector;
	}

	public function setWithArguments(array $arguments)
	{
		parent::setWithArguments($arguments);

		$asserter = $this->asserterSelector->selectFor($arguments[0]);

		try
		{
			$asserter->isEmpty();

			$this->pass();
		}
		catch(atoum\asserter\exception $exception)
		{
			$this->fail(isset($arguments[1]) ? $arguments[1] : $exception->getMessage());
		}

		return $this;
	}

	protected function checkArguments(array $arguments, $expectedNumber = null)
	{
		return parent::checkArguments($arguments, $expectedNumber ?: 1);
	}
} 