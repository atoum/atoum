<?php

namespace atoum\atoum\report\fields;

use atoum\atoum;
use atoum\atoum\report;

abstract class runner extends report\field
{
    abstract public function setWithRunner(atoum\runner $runner, $event = null);
}
