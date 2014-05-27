<?php

namespace mageekguy\atoum\tools\diff;

use
	mageekguy\atoum\tools
;

class decorator
{
	public function decorate(tools\diff $diff)
	{
		$string = '';

		$diff = array_filter($diff->make(), function($value) { return is_array($value) === true; });

		if (sizeof($diff) > 0)
		{
			$string .= '-Expected' . PHP_EOL;
			$string .= '+Actual' . PHP_EOL;

			foreach ($diff as $lineNumber => $diff)
			{
				$lineNumber++;

				$sizeofMinus = sizeof($diff['-']);
				$sizeofPlus = sizeof($diff['+']);

				$string .= '@@ -' . $lineNumber . ($sizeofMinus <= 1 ? '' : ',' . $sizeofMinus) . ' +' . $lineNumber . ($sizeofPlus <= 1 ? '' : ',' . $sizeofPlus) . ' @@' . PHP_EOL;

				array_walk($diff['-'], function(& $value) { $value = ($value == '' ? '' : '-' . $value); });
				$minus = join(PHP_EOL, $diff['-']);

				if ($minus != '')
				{
					$string .= $minus . PHP_EOL;
				}

				array_walk($diff['+'], function(& $value) { $value = ($value == '' ? '' : '+' . $value); });
				$plus = join(PHP_EOL, $diff['+']);

				if ($plus != '')
				{
					$string .= $plus . PHP_EOL;
				}
			}
		}

		return trim($string);
	}
}
