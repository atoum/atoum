<?php

namespace mageekguy\atoum\asserters;

class sizeOf extends integer
{
	public function setWith($value, $label = null)
	{
		return parent::setWith(sizeof($value), $label);
	}
}
