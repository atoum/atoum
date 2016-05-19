<?php

namespace mageekguy\atoum\asserters;

class sizeOf extends integer
{
	public function setWith($value)
	{
		return parent::setWith(sizeof($value));
	}
}
