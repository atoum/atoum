<?php

namespace mageekguy\atoum\report\fields\test\run;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\test\run
{
	const titlePrompt = '> ';

	public function __toString()
	{
		$string = self::titlePrompt;

		if ($this->testClass === null)
		{
			$string .= $this->locale->_('There is currently no test running.');
		}
		else
		{
			$string .= sprintf($this->locale->_('Run %s...'), $this->testClass);
		}

		$string .= PHP_EOL;

		return $string;
	}
}

?>
