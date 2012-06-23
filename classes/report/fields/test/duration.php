<?php

namespace atoum\report\fields\test;

use
	atoum\test,
	atoum\locale,
	atoum\report,
	atoum\observable
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
