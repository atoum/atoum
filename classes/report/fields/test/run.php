<?php

namespace atoum\report\fields\test;

use
	atoum,
	atoum\test,
	atoum\report
;

abstract class run extends report\field
{
	protected $testClass = null;

	public function __construct()
	{
		parent::__construct(array(test::runStart));
	}

	public function getTestClass()
	{
		return $this->testClass;
	}

	public function handleEvent($event, atoum\observable $observable)
	{
		if (parent::handleEvent($event, $observable) === false)
		{
			return false;
		}
		else
		{
			$this->testClass = $observable->getClass();

			return true;
		}
	}
}
