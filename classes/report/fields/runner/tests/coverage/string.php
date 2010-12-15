<?php

namespace mageekguy\atoum\report\fields\runner\tests\coverage;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\runner\tests\coverage
{
	const titlePrompt = '> ';
	const classPrompt = '=> ';
	const methodPrompt = '==> ';

	public function __toString()
	{
		$string = '';

		if ($this->coverage !== null && sizeof($this->coverage) > 0)
		{
			$string .= self::titlePrompt . sprintf($this->locale->_('Code coverage value: %3.2f%%'), $this->coverage->getValue() * 100.0) . PHP_EOL;

			foreach ($this->coverage->getMethods() as $class => $methods)
			{
				$classCoverage = $this->coverage->getValueForClass($class);

				if ($classCoverage < 1.0)
				{
					$string .= self::classPrompt . sprintf($this->locale->_('Class %s: %3.2f%%'), $class, $classCoverage * 100.0) . PHP_EOL;

					foreach (array_keys($methods) as $method)
					{
						$methodCoverage = $this->coverage->getValueForMethod($class, $method);

						if ($methodCoverage < 1.0)
						{
							$string .= self::methodPrompt . sprintf($this->locale->_('%s::%s(): %3.2f%%'), $class, $method, $methodCoverage * 100.0) . PHP_EOL;
						}
					}
				}
			}
		}

		return $string;
	}
}

?>
