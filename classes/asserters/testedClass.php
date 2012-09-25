<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class testedClass extends asserters\phpClass
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
