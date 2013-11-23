<?php

namespace mageekguy\atoum\test\asserter;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\test\assertion
;

class generator extends asserter\generator
{
	protected $test = null;

	public function __construct(atoum\test $test, asserter\resolver $resolver = null, assertion\aliaser $aliaser = null)
	{
		parent::__construct($test->getLocale(), $resolver, $aliaser);

		$this->test = $test;
	}

	public function __get($property)
	{
		return $this->test->__get($property);
	}

	public function __call($method, $arguments)
	{
		return $this->test->__call($method, $arguments);
	}

	public function setTest(atoum\test $test)
	{
		$this->test = $test;

		return $this->setLocale($test->getLocale());
	}

	public function getTest()
	{
		return $this->test;
	}

	public function getAsserterInstance($asserter, array $arguments = array(), atoum\test $test = null)
	{
		return parent::getAsserterInstance($asserter, $arguments, $test ?: $this->test);
	}
}
