<?php

namespace mageekguy\atoum\asserters;

class sizeof extends \mageekguy\atoum\asserters\integer
{
	public function setWith($variable, $label = null)
	{
		return parent::setWith(sizeof($variable), $label);
	}
}

?>
