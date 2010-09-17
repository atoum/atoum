<?php

namespace mageekguy\atoum\asserters;

class sizeof extends \mageekguy\atoum\asserters\integer
{
	public function setWith($variable)
	{
		return parent::setWith(sizeof($variable));
	}
}

?>
