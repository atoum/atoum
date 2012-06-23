<?php

namespace atoum\report\fields;

use
	atoum,
	atoum\report
;

abstract class runner extends report\field
{
	public abstract function setWithRunner(atoum\runner $runner, $event = null);
}
