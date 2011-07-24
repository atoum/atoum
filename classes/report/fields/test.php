<?php

namespace mageekguy\atoum\report\fields;

use
	mageekguy\atoum,
	mageekguy\atoum\report
;

abstract class test extends report\field
{
	public abstract function setWithTest(atoum\test $test, $event = null);
}

?>
