<?php

namespace atoum\atoum\report\fields\runner\tests;

use atoum\atoum;
use atoum\atoum\report;
use atoum\atoum\runner;

abstract class memory extends report\field
{
    protected $value = null;
    protected $testNumber = null;

    public function __construct()
    {
        parent::__construct([runner::runStop]);
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getTestNumber()
    {
        return $this->testNumber;
    }

    public function handleEvent($event, atoum\observable $observable)
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
