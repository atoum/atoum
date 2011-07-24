<?php

namespace mageekguy\atoum\report\fields\test;

use
	mageekguy\atoum,
	mageekguy\atoum\report
;

abstract class run extends report\fields\test
{
	protected $testClass = null;

	public function getTestClass()
	{
		return $this->testClass;
	}

	public function setWithTest(atoum\test $test, $event = null)
	{
		if ($event === atoum\test::runStart)
		{
			$this->testClass = $test->getClass();
		}

		return $this;
	}
}

?>
