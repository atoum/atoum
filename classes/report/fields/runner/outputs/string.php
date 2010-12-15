<?php

namespace mageekguy\atoum\report\fields\runner\outputs;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\runner\outputs
{
	const titlePrompt = '> ';
	const methodPrompt = '=> ';

	public function __toString()
	{
		$string = '';

		if ($this->runner !== null)
		{
			$outputs = $this->runner->getScore()->getOutputs();

			$sizeOfOutputs = sizeof($outputs);

			if ($sizeOfOutputs > 0)
			{
				$string .= self::titlePrompt . sprintf($this->locale->__('There is %d output:', 'There are %d outputs:', $sizeOfOutputs), $sizeOfOutputs) . PHP_EOL;

				foreach ($outputs as $output)
				{
					$string .= self::methodPrompt . $output['class'] . '::' . $output['method'] . '():' . PHP_EOL;

					foreach (explode(PHP_EOL, rtrim($output['value'])) as $line)
					{
						$string .= $line . PHP_EOL;
					}
				}
			}
		}

		return $string;
	}
}

?>
