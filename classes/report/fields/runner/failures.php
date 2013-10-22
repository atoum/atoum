<?php

namespace atoum\report\fields\runner;

use
	atoum\locale,
	atoum\runner,
	atoum\report,
	atoum\observable
;

abstract class failures extends report\field
{
	protected $runner = null;

	public function __construct()
	{
		parent::__construct(array(runner::runStop));
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
