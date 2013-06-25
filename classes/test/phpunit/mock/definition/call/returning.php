<?php

namespace mageekguy\atoum\test\phpunit\mock\definition\call;

use mageekguy\atoum\mock\controller;
use mageekguy\atoum\test\phpunit\mock\definition\call;

class returning implements call
{
	protected $value;

	public function __construct($value)
	{
		$this->value = $value;
	}

	public function define(controller $controller, $method, $index = null)
	{
		$controller->{$method}[$index ?: 0] = $this->value;

		return $this;
	}
}