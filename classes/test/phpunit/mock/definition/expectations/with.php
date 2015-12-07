<?php

namespace mageekguy\atoum\test\phpunit\mock\definition\expectations;

use mageekguy\atoum\asserters;
use mageekguy\atoum\test\phpunit\mock\definition\expectation;

class with implements expectation
{
	protected $value;

	public function __construct(array $arguments)
	{
		$this->value = $arguments;
	}

	public function verdict(asserters\mock $asserter)
	{
		return call_user_func_array(array($asserter, 'withArguments'), $this->value);
	}
}