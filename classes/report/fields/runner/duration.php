<?php

namespace atoum\report\fields\runner;

use
	atoum\locale,
	atoum\runner,
	atoum\report,
	atoum\observable
;

abstract class duration extends report\field
{
	protected $value = null;

	public function __construct(locale $locale = null)
	{
		parent::__construct(array(runner::runStop), $locale);
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
			$this->value = $observable->getRunningDuration();

			return true;
		}
	}
}
