<?php

namespace mageekguy\atoum\test\phpunit\mock;

use mageekguy\atoum;

class definition extends atoum\test\mock\generator
{
	protected $currentMethod;
	protected $currentIndex;
	protected $currentExpect;
	protected $mock;

	protected $assertions = array();

	public function __construct($mock)
	{
		$this->mock = $mock;
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

		if (strpos($expectation, '@:') !== false)
		{
			$expect = trim($expectation, '@:');
			$this->currentIndex = $expect;
		}
		else
		{
			$this->currentExpect = $expectation;
		}

		return $this;
	}

	public function method($name)
	{
		$this->currentMethod = $name;

		$this->assertions[$name][] = array(
			'calls' => $this->currentExpect,
			'args' => null
		);

		return $this;
	}

	public function will($return)
	{
		$mockController = atoum\mock\controller::getForMock($this->mock);

		if ($this->currentIndex !== null)
		{
			$mockController->{$this->currentMethod}[$this->currentIndex] = $return;
		}
		else
		{
			$mockController->{$this->currentMethod} = $return;
		}

		return $this;
	}

	public function with()
	{
		$i = count($this->assertions[$this->currentMethod]) - 1;
		$i = $i < 0 ? 0 : 1;

		$this->assertions[$this->currentMethod][$i]['args'] = func_get_args();

		return $this;
	}

	public function verdict(atoum\test $test)
	{
		$assertions = $this->assertions;
		$this->reset();

		foreach($assertions as $method => $expectations) {
			foreach($expectations as $expect) {
				$assert = $test->mock($this->mock)->call($method);

				if(isset($expect['args'])) {
					$assert = call_user_func_array(array($assert, 'withArguments'), $expect['args']);
				}

				if(isset($expect['calls'])) {
					if(is_string($expect['calls'])) {
						if('>=1' === $expect['calls']) {
							$assert->atLeastOnce();
						}
					} else {
						if(0 >= $expect['calls']) {
							$assert->never();
						} else {
							$assert->exactly($expect['calls']);
						}
					}
				}
			}
		}

		return $this;
	}
}