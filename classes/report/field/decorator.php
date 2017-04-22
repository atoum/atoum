<?php

namespace mageekguy\atoum\report\field;

use mageekguy\atoum;
use mageekguy\atoum\report\field;

abstract class decorator extends field
{
    private $field;

    public function __construct(field $field)
    {
        $this->field = $field;
    }

    public function __toString()
    {
        return $this->decorate($this->field->__toString());
    }

    public function setLocale(atoum\locale $locale = null)
    {
        $this->field->setLocale($locale);

        return $this;
    }

    public function getLocale()
    {
        return $this->field->getLocale();
    }

    public function getEvents()
    {
        return $this->field->getEvents();
    }

    public function canHandleEvent($event)
    {
        return $this->field->canHandleEvent($event);
    }

    public function handleEvent($event, atoum\observable $observable)
    {
        return $this->field->handleEvent($event, $observable);
    }

    abstract public function decorate($string);
}
