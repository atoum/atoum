<?php

namespace mageekguy\atoum\report\fields\test;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\report
;

abstract class memory extends report\field
{
	protected $value = null;

	public function __construct()
	{
		parent::__construct(array(test::runStop));
	}

	public function getValue()
	{
		return $this->value;
	}

	public function handleEvent($event, atoum\observable $observable)
	{
		if (parent::handleEvent($event, $observable) === false)
		{
			return false;
		}
		else
		{
			$this->value = $observable->getScore()->getTotalMemoryUsage();

			return true;
		}
	}
}
