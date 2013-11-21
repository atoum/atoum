<?php

namespace mageekguy\atoum\mock\controller;

use
	mageekguy\atoum\mock,
	mageekguy\atoum\test\adapter
;

class invoker extends adapter\invoker
{
	protected $mock = null;

	public function __construct($method, mock\aggregator $mock = null)
	{
		parent::__construct($method);

		$this->mock = $mock;
	}

	public function setMock(mock\aggregator $mock)
	{
		$this->mock = $mock;

		return $this;
	}

	public function getMock()
	{
		return $this->mock;
	}

	public function returnThis()
	{
		$mock = $this->mock;

		return $this->setClosure(function() use ($mock) { return $mock; });
	}
}
