<?php

namespace mageekguy\atoum\mock\controller;

use
	mageekguy\atoum\mock
;

class linker
{
	protected $mocks = null;
	protected $controllers = null;

	public function __construct()
	{
		$this->init();
	}

	public function link(mock\controller $controller, mock\aggregator $mock)
	{
		$currentMock = $this->getMock($controller);

		if ($currentMock === null || $currentMock !== $this)
		{
			if ($currentMock !== $this)
			{
				$this->unlink($controller);
			}

			$this->mocks[$controller] = $mock;
			$this->controllers[$mock] = $controller;

			$controller->control($mock);
		}

		return $this;
	}

	public function getController(mock\aggregator $mock)
	{
		return (isset($this->controllers[$mock]) === false ? null : $this->controllers[$mock]);
	}

	public function getMock(mock\controller $controller)
	{
		return (isset($this->mocks[$controller]) === false ? null : $this->mocks[$controller]);
	}

	public function getMocks()
	{
		return $this->mocks;
	}

	public function unlink(mock\controller $controller)
	{
		$mock = $this->getMock($controller);

		if ($mock !== null)
		{
			unset($this->controllers[$mock]);
			unset($this->mocks[$controller]);

			$controller->reset();
		}

		return $this;
	}

	public function reset()
	{
		foreach ($this->mocks as $controller)
		{
			$controller->reset();
		}

		return $this->init();
	}

	public function init()
	{
		$this->mocks = new \splObjectStorage();
		$this->controllers = new \splObjectStorage();

		return $this;
	}
}
