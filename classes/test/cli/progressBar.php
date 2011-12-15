<?php

namespace mageekguy\atoum\test\cli;

use
	mageekguy\atoum
;

class progressBar extends atoum\cli\progressBar
{
	public function __construct(atoum\test $test, atoum\cli $cli = null)
	{
		parent::__construct(sizeof($test), $cli);
	}
}

?>
