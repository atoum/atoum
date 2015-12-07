<?php

namespace mageekguy\atoum\test\phpunit\mock\definition\call;

use mageekguy\atoum\mock\controller;
use mageekguy\atoum\test\phpunit\mock\definition\call;

class throwing implements call
{
	protected $exception;

	public function __construct(\exception $exception)
	{
		$this->exception = $exception;
	}

	public function define(controller $controller, $method, $index = null)
	{
		$controller->{$method}[$index ?: 0]->throw = $this->exception;

		return $this;
	}
}