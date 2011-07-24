<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters
;

class output extends asserters\string
{
	public function setWith($value, $label = null, $charlist = null, $checkType = true)
	{
		if ($value instanceof \closure)
		{
			ob_start();
			$value();
			$value = ob_get_clean();
		}

		return parent::setWith($value, $label, $charlist, $checkType);
	}
}

?>
