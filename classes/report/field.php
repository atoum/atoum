<?php

namespace mageekguy\atoum\report;

use \mageekguy\atoum;

abstract class field
{
	protected $locale = null;

	public function __construct(atoum\locale $locale = null)
	{
		$this->setLocale($locale ?: new atoum\locale());
	}

	public function setLocale(atoum\locale $locale)
	{
		$this->locale = $locale;

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	abstract public function __toString();
}

?>
