<?php

namespace mageekguy\atoum\report\fields;

use mageekguy\atoum;
use mageekguy\atoum\report;

abstract class event extends report\field
{
    protected $observable = null;
    protected $event = null;

    public function getObservable()
    {
        return $this->observable;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function handleEvent($event, atoum\observable $observable)
    {
        if (parent::handleEvent($event, $observable) === false) {
            $this->observable = null;
            $this->event = null;

            return false;
        } else {
            $this->observable = $observable;
            $this->event = $event;

            return true;
        }
    }
}
