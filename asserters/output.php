<?php

namespace mageekguy\atoum\asserters;

class output extends \mageekguy\atoum\asserters\string
{
	public function setWith($variable, $charlist = null)
	{
		if ($variable instanceof \closure)
		{
			ob_start();
			$variable();
			$variable = ob_get_clean();
		}

		return parent::setWith($variable, $charlist);
	}
}

?>
