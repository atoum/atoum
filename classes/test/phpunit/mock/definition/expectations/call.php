<?php

namespace mageekguy\atoum\test\phpunit\mock\definition\expectations;

use mageekguy\atoum\asserters;
use mageekguy\atoum\test\phpunit\mock\definition\expectation;

class call extends chain
{
	protected $value;

	public function __construct($method, array $expectations = array())
	{
		parent::__construct($expectations);

		$this->value = $method;
	}

	public function verdict(asserters\mock $asserter)
	{
		return parent::verdict($asserter->call($this->value));
	}
}