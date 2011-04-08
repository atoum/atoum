<?php

namespace mageekguy\atoum\report;

use \mageekguy\atoum;

abstract class field
{
	protected $locale = null;

	public function __construct(atoum\locale $locale = null)
	{
		if ($locale === null)
		{
			$locale = new atoum\locale();
		}

		$this->setLocale($locale);
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
