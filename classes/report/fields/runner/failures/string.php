<?php

namespace mageekguy\atoum\report\fields\runner\failures;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\runner\failures
{
	const titlePrompt = '> ';
	const methodPrompt = '=> ';

	public function __toString()
	{
		$string = '';

		if ($this->runner !== null)
		{
			$fails = $this->runner->getScore()->getFailAssertions();

			$numberOfFails = sizeof($fails);

			if ($numberOfFails > 0)
			{
				$string .= self::titlePrompt . sprintf($this->locale->__('There is %d failure:', 'There are %d failures:', $numberOfFails), $numberOfFails) . PHP_EOL;

				foreach ($fails as $fail)
				{
					$string .= self::methodPrompt . $fail['class'] . '::' . $fail['method'] . '():' . PHP_EOL;
					$string .= sprintf($this->locale->_('In file %s on line %d, %s failed : %s'), $fail['file'], $fail['line'], $fail['asserter'], $fail['fail']) . PHP_EOL;
				}
			}
		}

		return $string;
	}
}

?>
