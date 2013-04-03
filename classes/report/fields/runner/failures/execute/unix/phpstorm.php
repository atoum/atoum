<?php

namespace mageekguy\atoum\report\fields\runner\failures\execute\unix;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\failures
;

class phpstorm extends failures\execute
{
	public function getCommand()
	{
		return parent::getCommand() . ' --line %2$d %1$s &> /dev/null &';
	}
}
