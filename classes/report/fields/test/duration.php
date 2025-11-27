<?php

namespace atoum\atoum\report\fields\test;

use atoum\atoum\observable;
use atoum\atoum\report;
use atoum\atoum\test;

abstract class duration extends report\field
{
    protected mixed $value = null;

    public function __construct()
    {
        parent::__construct([test::runStop]);
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function handleEvent(string $event, observable $observable): bool
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        } else {
            $this->value = $observable->getScore()->getTotalDuration();

            return true;
        }
    }
}
