<?php

namespace mageekguy\tests\unit;

use \mageekguy\tests\unit;

abstract class reporter implements \mageekguy\tests\unit\observer
{
	protected $locale = null;

	public function __construct(unit\locale $locale = null)
	{
		if ($locale === null)
		{
			$locale = new unit\locale();
		}

		$this->setLocale($locale);
	}

	public function setLocale(unit\locale $locale)
	{
		$this->locale = $locale;
		return $this;
	}
}

?>
