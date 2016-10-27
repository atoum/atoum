<?php

namespace mageekguy\atoum\report\fields;

use mageekguy\atoum;
use mageekguy\atoum\report;

abstract class runner extends report\field
{
    abstract public function setWithRunner(atoum\runner $runner, $event = null);
}
