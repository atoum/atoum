<?php

namespace mageekguy\atoum\report\fields\runner\tests\coverage;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report
;

class phing extends report\fields\runner\tests\coverage\cli
{
	protected $showMissingCodeCoverage = true;

	public function __toString()
	{
		$string = '';

		if ($this->coverage !== null && sizeof($this->coverage) > 0)
		{
			$string .= $this->titlePrompt .
				sprintf(
					$this->locale->_('%s : %s'),
					$this->titleColorizer->colorize($this->locale->_('Code coverage value')),
					$this->coverageColorizer->colorize(sprintf('%3.2f%%', $this->coverage->getValue() * 100.0))
				) .
				PHP_EOL
			;

			if ($this->showMissingCodeCoverage === true)
			{
				foreach ($this->coverage->getMethods() as $class => $methods)
				{
					$classCoverage = $this->coverage->getValueForClass($class);

					if ($classCoverage < 1.0)
					{
						$string .= $this->classPrompt .
							sprintf(
								$this->locale->_('%s : %s'),
								$this->titleColorizer->colorize(sprintf($this->locale->_('Class %s'), $class)),
								$this->coverageColorizer->colorize(sprintf('%3.2f%%', $classCoverage * 100.0))
							) .
							PHP_EOL
						;

						foreach (array_keys($methods) as $method)
						{
							$methodCoverage = $this->coverage->getValueForMethod($class, $method);

							if ($methodCoverage < 1.0)
							{
								$string .= $this->methodPrompt .
									sprintf(
										$this->locale->_('%s : %s'),
										$this->titleColorizer->colorize(sprintf($this->locale->_('     ::%s()'), $method)),
										$this->coverageColorizer->colorize(sprintf('%3.2f%%', $methodCoverage * 100.0))
									) .
									PHP_EOL
								;
							}
						}
					}
				}
			}
		}

		return $string;
	}

	public function showMissingCodeCoverage()
	{
		$this->showMissingCodeCoverage = true;

		return $this;
	}

	public function hideMissingCodeCoverage()
	{
		$this->showMissingCodeCoverage = false;

		return $this;
	}

	public function missingCodeCoverageIsShowed()
	{
		return $this->showMissingCodeCoverage;
	}
}
