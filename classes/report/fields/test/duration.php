<?php

namespace mageekguy\atoum\report\fields\test;

use mageekguy\atoum\test;
use mageekguy\atoum\report;
use mageekguy\atoum\observable;

abstract class duration extends report\field
{
    protected $value = null;

    public function __construct()
    {
        parent::__construct(array(test::runStop));
    }

    public function getValue()
    {
        return $this->value;
    }

    public function handleEvent($event, observable $observable)
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        } else {
            $this->value = $observable->getScore()->getTotalDuration();

            return true;
        }
    }
}
