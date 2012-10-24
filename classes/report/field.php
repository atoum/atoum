<?php

namespace mageekguy\atoum\report;

use
	mageekguy\atoum
;

abstract class field
{
	protected $events = array();
	protected $locale = null;

	public function __construct(array $events = null)
	{
		$this->events = $events;

		$this->setLocale();
	}

	public function setLocale(atoum\locale $locale = null)
	{
		$this->locale = $locale ?: new atoum\locale();

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function getEvents()
	{
		return $this->events;
	}

	public function canHandleEvent($event)
	{
		return ($this->events === null ? true : in_array($event, $this->events));
	}

	public function handleEvent($event, atoum\observable $observable)
	{
		return $this->canHandleEvent($event);
	}

	abstract public function __toString();
}
