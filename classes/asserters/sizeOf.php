<?php

namespace mageekguy\atoum\asserters;

class sizeOf extends \mageekguy\atoum\asserters\integer
{
	public function setWith($value, $label = null)
	{
		return parent::setWith(sizeof($value), $label);
	}
}
