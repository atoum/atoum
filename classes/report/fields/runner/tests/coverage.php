<?php

namespace mageekguy\atoum\report\fields\runner\tests;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class coverage extends report\fields\runner
{
	const titlePrompt = '> ';
	const classPrompt = '=> ';
	const methodPrompt = '==> ';

	protected $coverage = null;

	public function setWithRunner(atoum\runner $runner, $event = null)
	{
		if ($event === atoum\runner::runStop)
		{
			$this->coverage = $runner->getScore()->getCoverage();
		}

		return $this;
	}

	public function getCoverage()
	{
		return $this->coverage;
	}

	public function toString()
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
