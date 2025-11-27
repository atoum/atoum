<?php

namespace atoum\atoum\report\fields;

use atoum\atoum;
use atoum\atoum\report;

abstract class event extends report\field
{
    protected ?atoum\observable $observable = null;
    protected ?string $event = null;

    public function getObservable(): ?atoum\observable
    {
        return $this->observable;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function handleEvent(string $event, atoum\observable $observable): bool
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
