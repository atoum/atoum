<?php

namespace mageekguy\atoum\test\phpunit\mock\definition\expectations;

use mageekguy\atoum\asserters;
use mageekguy\atoum\test\phpunit\mock\definition\expectation;

class chain implements expectation
{
	protected $expectations;

	public function __construct(array $expectations = array())
	{
		$this->expectations = $expectations;
	}

	public function addExpectation(expectation $expectation)
	{
		$this->expectations[] = $expectation;

		return $this;
	}

	public function verdict(asserters\mock $asserter)
	{
		foreach ($this->expectations as $expectation)
		{
			$asserter = $expectation->verdict($asserter);
		}

		return $asserter;
	}
}