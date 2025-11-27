<?php

namespace atoum\atoum\report\fields\runner\tests;

use atoum\atoum;
use atoum\atoum\report;
use atoum\atoum\runner;

abstract class memory extends report\field
{
    protected mixed $value = null;
    protected ?int $testNumber = null;

    public function __construct()
    {
        parent::__construct([runner::runStop]);
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getTestNumber(): ?int
    {
        return $this->testNumber;
    }

    public function handleEvent(string $event, atoum\observable $observable): bool
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        } else {
            $this->value = $observable->getScore()->getTotalMemoryUsage();
            $this->testNumber = $observable->getTestNumber();

            return true;
        }
    }
}
