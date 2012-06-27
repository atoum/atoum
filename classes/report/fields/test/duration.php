<?php

namespace mageekguy\atoum\report\fields\test;

use
	mageekguy\atoum\test,
	mageekguy\atoum\locale,
	mageekguy\atoum\report,
	mageekguy\atoum\observable
;

abstract class duration extends report\field
{
	protected $value = null;

	public function __construct(locale $locale = null)
	{
		parent::__construct(array(test::runStop), $locale);
	}

	public function getValue()
	{
		return $this->value;
	}

	public function handleEvent($event, observable $observable)
	{
		if (parent::handleEvent($event, $observable) === false)
		{
			return false;
		}
		else
		{
			$this->value = $observable->getScore()->getTotalDuration();

			return true;
		}
	}
}
