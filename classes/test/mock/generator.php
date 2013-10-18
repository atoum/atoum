<?php

namespace mageekguy\atoum\test\mock;

use
	mageekguy\atoum,
	mageekguy\atoum\mock
;

class generator extends mock\generator
{
	protected $test = null;
	protected $getMethod = false;

	public function __construct(atoum\test $test)
	{
		parent::__construct();

		$this->setTest($test);
	}

	public function __get($property)
	{
		if ($this->getMethod === false)
		{
			return $this->test->getAssertionManager()->invoke($property);
		}
		else
		{
			$method = parent::__get($property);

			$this->getAssertion();

			return $method;
		}
	}

	public function getMethod()
	{
		$this->getMethod = true;

		return $this;
	}

	public function getAssertion()
	{
		$this->getMethod = false;

		return $this;
	}

	public function __call($method, array $arguments)
	{
		return $this->test->getAssertionManager()->invoke($method, $arguments);
	}

	public function setTest(atoum\test $test)
	{
		$this->test = $test;

		return $this;
	}

	public function getTest()
	{
		return $this->test;
	}
}
