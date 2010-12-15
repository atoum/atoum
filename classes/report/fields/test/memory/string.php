<?php

namespace mageekguy\atoum\report\fields\test\memory;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\test\memory
{
	const titlePrompt = '=> ';

	public function __toString()
	{
		$string = self::titlePrompt;

		if ($this->value === null)
		{
			$string .= $this->locale->_('Memory usage: unknown.');
		}
		else
		{
			$string .= sprintf($this->locale->_('Memory usage: %4.2f Mb.'), $this->value / 1048576);
		}

		$string .= PHP_EOL;

		return $string;
	}
}

?>
