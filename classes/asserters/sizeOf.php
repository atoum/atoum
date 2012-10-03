<?php

namespace mageekguy\atoum\asserters;

use mageekguy\atoum\asserters;

class sizeOf extends asserters\integer
{
	public function setWith($value, $label = null)
	{
		return parent::setWith(sizeof($value), $label);
	}
}
