<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserters
;

class output extends asserters\string
{
	public function __construct(atoum\asserter\generator $generator)
	{
		parent::__construct($generator);

		$this->setWith(null);
	}

	public function setWith($value = null, $label = null, $charlist = null, $checkType = true)
	{
		if ($value instanceof \closure)
		{
			ob_start();
			$value();
			$value = ob_get_clean();
		}
		else if ($value === null && ob_get_level() > 0)
		{
			$value = ob_get_clean();
			ob_start();
		}

		return parent::setWith($value, $label, $charlist, $checkType);
	}
}
