<?php

namespace atoum\atoum\report\fields\runner\tests;

use atoum\atoum;
use atoum\atoum\report;
use atoum\atoum\runner;

abstract class coverage extends report\field
{
    protected mixed $coverage = null;

    public function __construct()
    {
        parent::__construct([runner::runStop]);
    }

    public function getCoverage(): mixed
    {
        return $this->coverage;
    }

    public function handleEvent(string $event, atoum\observable $observable): bool
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        } else {
            $this->coverage = $observable->getScore()->getCoverage();

            return true;
        }
    }
}
