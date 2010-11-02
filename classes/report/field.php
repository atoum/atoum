<?php

namespace mageekguy\atoum\report;

use \mageekguy\atoum;

abstract class field
{
	protected $event = null;

	public function __construct(atoum\locale $locale = null)
	{
		if ($locale === null)
		{
			$locale = new atoum\locale();
		}

		$this->locale = $locale;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function getEvent()
	{
		return $this->event;
	}

	public function setEvent($event)
	{
		$this->event = $event;

		return $this;
	}

	public function toString()
	{
		return '';
	}
}

?>
