<?php

namespace mageekguy\atoum\asserters;

use
	\mageekguy\atoum\asserter,
	\mageekguy\atoum\exceptions
;

class testedClass extends phpClass
{
	public function __construct(asserter\generator $generator)
	{
		parent::__construct($generator);
		parent::setWith($generator->getTest()->getTestedClassName());
	}

	public function setWith($class, $label = null)
	{
		throw new exceptions\logic\badMethodCall('Unable to call method ' . __METHOD__ . '()');
	}
}

?>
