<?php

namespace mageekguy\atoum\test\mock;

use
	mageekguy\atoum,
	mageekguy\atoum\mock
;

class generator extends mock\generator
{
	protected $test = null;

	public function __construct(atoum\test $test)
	{
		parent::__construct($test->getFactory());

		$this->setTest($test);
	}

	public function __get($property)
	{
		return $this->test->getAssertionManager()->invoke($property);
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
