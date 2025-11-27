<?php

namespace atoum\atoum\report\fields\runner\tap;

use atoum\atoum\report;
use atoum\atoum\runner;

class plan extends report\field
{
    protected int $testMethodNumber = 0;

    public function __construct()
    {
        parent::__construct([runner::runStart]);
    }

    public function __toString(): string
    {
        return ($this->testMethodNumber <= 0 ? '' : '1..' . $this->testMethodNumber . PHP_EOL);
    }

    public function handleEvent(string $event, \atoum\atoum\observable $observable): bool
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        } else {
            $this->testMethodNumber = $observable->getTestMethodNumber();

            return true;
        }
    }
}
