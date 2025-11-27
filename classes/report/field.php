<?php

namespace atoum\atoum\report;

use atoum\atoum;

abstract class field
{
    protected ?array $events = [];
    protected ?atoum\locale $locale = null;

    public function __construct(?array $events = null)
    {
        $this->events = $events;

        $this->setLocale();
    }

    public function setLocale(?atoum\locale $locale = null): static
    {
        $this->locale = $locale ?: new atoum\locale();

        return $this;
    }

    public function getLocale(): atoum\locale
    {
        return $this->locale;
    }

    public function getEvents(): ?array
    {
        return $this->events;
    }

    public function canHandleEvent(string $event): bool
    {
        return ($this->events === null ? true : in_array($event, $this->events));
    }

    public function handleEvent(string $event, atoum\observable $observable): bool
    {
        return $this->canHandleEvent($event);
    }

    abstract public function __toString(): string;
}
