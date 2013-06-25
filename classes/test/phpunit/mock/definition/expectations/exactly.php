<?php

namespace mageekguy\atoum\test\phpunit\mock\definition\expectations;

use mageekguy\atoum\asserters;
use mageekguy\atoum\test\phpunit\mock\definition\expectation;

class exactly implements expectation
{
	protected $value;

	public function __construct($value)
	{
		$this->value = $value;
	}

	public function verdict(asserters\mock $asserter)
	{
		return $asserter->exactly($this->value);
	}
}