<?php

namespace mageekguy\atoum\report\fields\runner\tests;

use mageekguy\atoum\observable;
use mageekguy\atoum\report;
use mageekguy\atoum\runner;

abstract class skipped extends report\field
{
    protected $runner = null;

    public function __construct()
    {
        parent::__construct([runner::runStop]);
    }

    public function getRunner()
    {
        return $this->runner;
    }

    public function handleEvent($event, observable $observable)
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        } else {
            $this->runner = $observable;

            return true;
        }
    }
}
