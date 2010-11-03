<?php

namespace mageekguy\atoum\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class run extends report\fields\test
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

	public function toString()
	{
		return
			$this->testClass === null ?
			$this->locale->_('There is currently no test running.') :
			sprintf($this->locale->_('Run %s...'), $this->testClass)
		;
	}
}

?>
