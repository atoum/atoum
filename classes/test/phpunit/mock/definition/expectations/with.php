<?php

namespace mageekguy\atoum\test\phpunit\mock\definition\expectations;

use mageekguy\atoum\asserters;
use mageekguy\atoum\test\phpunit\mock\definition\expectation;

class with implements expectation
{
	protected $value;

	public function __construct()
	{
		$this->value = func_get_args();
	}

	public function verdict(asserters\mock $asserter)
	{
		return $asserter->withArguments($asserter);
	}
}