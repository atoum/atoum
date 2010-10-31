<?php

namespace mageekguy\atoum\report\fields;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

abstract class test extends report\field
{
	public function setWithTest(atoum\test $test)
	{
		return parent::setWithRunner($test);
	}
}

?>
