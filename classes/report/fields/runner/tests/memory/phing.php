<?php

namespace mageekguy\atoum\report\fields\runner\tests\memory;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report
;

class phing extends report\fields\runner\tests\memory\cli
{
	public function __toString()
	{
		$title = $this->locale->__('Total test memory usage', 'Total tests memory usage', $this->testNumber);
		$memory = ($this->value === null ? $this->locale->_('unknown') : $this->locale->_('%4.2f Mb', $this->value / 1048576));

		return $this->prompt . $this->locale->_('%s: %s.', $this->titleColorizer->colorize($title), $this->memoryColorizer->colorize($memory));
	}
}
