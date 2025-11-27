<?php

namespace atoum\atoum\report\field;

use atoum\atoum;
use atoum\atoum\report\field;

abstract class decorator extends field
{
    private field $field;

    public function __construct(field $field)
    {
        $this->field = $field;
    }

    public function __toString(): string
    {
        return $this->decorate($this->field->__toString());
    }

    public function setLocale(?atoum\locale $locale = null): static
    {
        $this->field->setLocale($locale);

        return $this;
    }

    public function getLocale(): atoum\locale
    {
        return $this->field->getLocale();
    }

    public function getEvents(): ?array
    {
        return $this->field->getEvents();
    }

    public function canHandleEvent(string $event): bool
    {
        return $this->field->canHandleEvent($event);
    }

    public function handleEvent(string $event, atoum\observable $observable): bool
    {
        return $this->field->handleEvent($event, $observable);
    }

    abstract public function decorate(string $string): string;
}
