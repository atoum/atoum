<?php

namespace mageekguy\atoum\report\fields\runner\tests;

use mageekguy\atoum;
use mageekguy\atoum\report;
use mageekguy\atoum\runner;

abstract class coverage extends report\field
{
    protected $coverage = null;

    public function __construct()
    {
        parent::__construct([runner::runStop]);
    }

    public function getCoverage()
    {
        return $this->coverage;
    }

    public function handleEvent($event, atoum\observable $observable)
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        } else {
            $this->coverage = $observable->getScore()->getCoverage();

            return true;
        }
    }
}
