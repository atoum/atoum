<?php

namespace atoum\atoum\report\fields\test;

use atoum\atoum;
use atoum\atoum\report;
use atoum\atoum\test;

abstract class run extends report\field
{
    protected ?string $testClass = null;

    public function __construct()
    {
        parent::__construct([test::runStart]);
    }

    public function getTestClass(): ?string
    {
        return $this->testClass;
    }

    public function handleEvent(string $event, atoum\observable $observable): bool
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        } else {
            $this->testClass = $observable->getClass();

            return true;
        }
    }
}
