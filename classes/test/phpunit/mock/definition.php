<?php

namespace mageekguy\atoum\test\phpunit\mock;

use mageekguy\atoum;
use mageekguy\atoum\test\phpunit;

class definition extends atoum\test\mock\generator
{
	protected $currentMethod;
	protected $currentIndex;
	protected $currentExpect;
	protected $mock;
	protected $assertions;

	public function __construct($mock)
	{
		$this->mock = $mock;

		$this->reset();
	}

	public function reset()
	{
		$this->assertions = array();

		return $this;
	}

	public function getAssertions()
	{
		return $this->assertions;
	}

	public function expects($expectation)
	{
		$this->currentIndex = null;
		$this->currentMethod = null;
		$this->currentExpect = null;

		if ($expectation instanceof phpunit\mock\definition\expectation)
		{
			$this->currentExpect = $expectation;
		}
		else
		{
			$this->currentIndex = $expectation;
		}

		return $this;
	}

	public function method($name)
	{
		$this->currentMethod = $name;

		if (isset($this->assertions[$name]) === false)
		{
			$this->assertions[$name] = new phpunit\mock\definition\expectations\chain();
		}

		$expectations = new phpunit\mock\definition\expectations\chain();
		if ($this->currentExpect !== null)
		{
			$expectations->addExpectation($this->currentExpect);
		}

		$this->assertions[$name]->addExpectation($expectations);

		return $this;
	}

	public function will(phpunit\mock\definition\call $return)
	{
		$mockController = atoum\mock\controller::getForMock($this->mock);
		$return->define($mockController, $this->currentMethod, $this->currentIndex);

		return $this;
	}

	public function with()
	{
		$this->assertions[$this->currentMethod]->getLastExpectation()->addExpectation(new phpunit\mock\definition\expectations\with(func_get_args()));

		return $this;
	}

	public function verdict(atoum\test $test)
	{
		foreach($this->assertions as $method => $expectations) {
			$assert = $test->mock($this->mock)->call($method);

			$expectations->verdict($assert);
		}

		$this->reset();

		return $this;
	}
}