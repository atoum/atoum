<?php

namespace atoum\asserters;

class sizeOf extends \atoum\asserters\integer
{
	public function setWith($value, $label = null)
	{
		return parent::setWith(sizeof($value), $label);
	}
}
