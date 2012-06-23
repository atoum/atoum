<?php

namespace atoum\report\fields\runner;

use
	atoum\runner,
	atoum\locale,
	atoum\observable,
	atoum\report\field
;

abstract class outputs extends field
{
	protected $runner = null;

	public function __construct(locale $locale = null)
	{
		parent::__construct(array(runner::runStop), $locale);
	}

	public function getRunner()
	{
		return $this->runner;
	}

	public function handleEvent($event, observable $observable)
	{
		if (parent::handleEvent($event, $observable) === false)
		{
			return false;
		}
		else
		{
			$this->runner = $observable;

			return true;
		}
	}
}
