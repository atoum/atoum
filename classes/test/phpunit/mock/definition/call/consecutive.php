<?php

namespace mageekguy\atoum\test\phpunit\mock\definition\call;

use mageekguy\atoum\mock\controller;
use mageekguy\atoum\test\phpunit\mock\definition\call;

class consecutive extends returning
{
	public function __construct(array $values)
	{
		parent::__construct($values);
	}

	public function define(controller $controller, $method, $from = null)
	{
		$from = $from ?: 1;

		foreach ($this->value as $value)
		{
			$controller->{$method}[$from++] = $value;
		}

		return $this;
	}
}