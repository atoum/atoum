<?php

namespace mageekguy\atoum\report\fields\runner\tap;

use mageekguy\atoum\report;
use mageekguy\atoum\runner;

class plan extends report\field
{
    protected $testMethodNumber = 0;

    public function __construct()
    {
        parent::__construct([runner::runStart]);
    }

    public function __toString()
    {
        return ($this->testMethodNumber <= 0 ? '' : '1..' . $this->testMethodNumber . PHP_EOL);
    }

    public function handleEvent($event, \mageekguy\atoum\observable $observable)
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        } else {
            $this->testMethodNumber = $observable->getTestMethodNumber();

            return true;
        }
    }
}
