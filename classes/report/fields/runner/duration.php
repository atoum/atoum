<?php

namespace mageekguy\atoum\report\fields\runner;

use
	mageekguy\atoum\locale,
	mageekguy\atoum\runner,
	mageekguy\atoum\report,
	mageekguy\atoum\observable
;

abstract class duration extends report\field
{
	protected $value = null;

	public function __construct()
	{
		parent::__construct(array(runner::runStop));
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
