<?php

namespace mageekguy\atoum\report;

use \mageekguy\atoum;

abstract class field
{
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

	abstract public function __toString();

}

?>
