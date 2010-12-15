<?php

namespace mageekguy\atoum\report\fields\runner\tests\duration;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\runner\tests\duration
{
	const titlePrompt = '> ';

	public function __toString()
	{
		$string = self::titlePrompt;

		if ($this->value === null)
		{
			$string .= $this->locale->__('Total test duration: unknown.', 'Total tests duration: unknown.', $this->testNumber);
		}
		else
		{
			$string .= sprintf(
				$this->locale->__('Total test duration: %s.', 'Total tests duration: %s.', $this->testNumber),
				sprintf(
					$this->locale->__('%4.2f second', '%4.2f seconds', $this->value),
					$this->value
				)
			);
		}
		
		$string .= PHP_EOL;

		return $string;
	}
}

?>
