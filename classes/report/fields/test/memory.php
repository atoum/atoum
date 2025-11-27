<?php

namespace atoum\atoum\report\fields\test;

use atoum\atoum;
use atoum\atoum\report;
use atoum\atoum\test;

abstract class memory extends report\field
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

    public function handleEvent(string $event, atoum\observable $observable): bool
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        } else {
            $this->value = $observable->getScore()->getTotalMemoryUsage();

            return true;
        }
    }
}
