<?php

namespace atoum\report\fields\test\run;

use
	atoum,
	atoum\locale,
	atoum\cli\prompt,
	atoum\cli\colorizer,
	atoum\report
;

class phing extends report\fields\test\run\cli
{
    public function __toString()
    {
		return $this->prompt .
		(
			$this->testClass === null
			?
			$this->colorizer->colorize($this->locale->_('There is currently no test running.'))
			:
			sprintf($this->locale->_('%s : '), $this->colorizer->colorize($this->testClass))
		);
	}
}
