<?php

namespace mageekguy\atoum\report\fields;

use
	mageekguy\atoum,
	mageekguy\atoum\report
;

abstract class runner extends report\field
{
	public abstract function setWithRunner(atoum\runner $runner, $event = null);
}
