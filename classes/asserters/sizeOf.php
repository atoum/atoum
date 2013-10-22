<?php

namespace atoum\asserters;

use atoum\asserters;

class sizeOf extends asserters\integer
{
	public function setWith($value, $label = null)
	{
		return parent::setWith(sizeof($value), $label);
	}
}
