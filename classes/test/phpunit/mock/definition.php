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

		$this->assertions[] = new phpunit\mock\definition\expectations\call($name);
		if ($this->currentExpect !== null)
		{
			end($this->assertions)->addExpectation($this->currentExpect);
		}

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
		end($this->assertions)->addExpectation(new phpunit\mock\definition\expectations\with(func_get_args()));

		return $this;
	}

	public function verdict(atoum\test $test)
	{
		if (sizeof($this->assertions))
		{
			$asserter = $test->mock($this->mock);

			while(sizeof($this->assertions) > 0) {
				$assertion = array_shift($this->assertions);

				$assertion->verdict($asserter);
			}
		}

		return $this->reset();
	}
}