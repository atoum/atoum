<?php

namespace atoum\report\fields\test\memory;

use
	atoum,
	atoum\locale,
	atoum\cli\prompt,
	atoum\cli\colorizer,
	atoum\report
;

class phing extends report\fields\test\memory\cli
{
	public function __toString()
	{
		return $this->prompt .
			sprintf(
				 $this->locale->_('%1$s'),
				 $this->memoryColorizer->colorize($this->value === null ? $this->locale->_('unknown') : sprintf($this->locale->_('%4.2f Mb'), $this->value / 1048576))
			)
		;
	}
}
