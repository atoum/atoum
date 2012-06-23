<?php

namespace atoum\asserters;

use
	atoum,
	atoum\asserter,
	atoum\exceptions
;

class testedClass extends phpClass
{
	public function setWith($class)
	{
		throw new exceptions\logic\badMethodCall('Unable to call method ' . __METHOD__ . '()');
	}

	public function setWithTest(atoum\test $test)
	{
		parent::setWith($test->getTestedClassName());

		return parent::setWithTest($test);
	}
}
