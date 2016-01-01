<?php

namespace mageekguy\atoum\report\fields\test\run;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report
;

class phing extends report\fields\test\run\cli
{
	public function __toString()
	{
		return $this->prompt . ($this->testClass === null ? $this->colorizer->colorize($this->locale->_('There is currently no test running.')) : $this->locale->_('%s : ', $this->colorizer->colorize($this->testClass)));
	}
}
