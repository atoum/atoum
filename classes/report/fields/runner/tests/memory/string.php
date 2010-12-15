<?php

namespace mageekguy\atoum\report\fields\runner\tests\memory;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\runner\tests\memory
{
	const titlePrompt = '> ';

	public function __toString()
	{
		$string = self::titlePrompt;

		if ($this->value === null)
		{
			$string .= $this->locale->_('Total test memory usage: unknown.');
		}
		else
		{
			$string .= sprintf($this->locale->__('Total test memory usage: %4.2f Mb.', 'Total tests memory usage: %4.2f Mb.', $this->testNumber), $this->value / 1048576);
		}

		$string .= PHP_EOL;

		return $string;
	}
}

?>
