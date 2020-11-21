<?php

namespace atoum\atoum\report\fields;

use atoum\atoum;
use atoum\atoum\report;

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
