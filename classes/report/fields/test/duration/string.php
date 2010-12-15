<?php

namespace mageekguy\atoum\report\fields\test\duration;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\test\duration
{
	const titlePrompt = '=> ';

	public function __toString()
	{
		$string = self::titlePrompt;

		if ($this->value === null)
		{
			$string .= $this->locale->_('Test duration: unknown.');
		}
		else
		{
			$string .= sprintf($this->locale->__('Test duration: %4.2f second.', 'Test duration: %4.2f seconds.', $this->value), $this->value);
		}

		$string .= PHP_EOL;

		return $string;
	}
}

?>
