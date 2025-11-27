<?php

namespace atoum\atoum\report\fields\runner;

use atoum\atoum\observable;
use atoum\atoum\report;
use atoum\atoum\runner;

abstract class duration extends report\field
{
    protected ?float $value = null;

    public function __construct()
    {
        parent::__construct([runner::runStop]);
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function handleEvent(string $event, observable $observable): bool
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        } else {
            $this->value = $observable->getRunningDuration();

            return true;
        }
    }
}
