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
				)
			;

			if (sizeof($this->coverage->getPaths()) > 0)
			{
				$string .= $this->titlePrompt .
					sprintf(
						$this->locale->_('%s: %s'),
						$this->titleColorizer->colorize($this->locale->_('Path coverage value')),
						$this->coverageColorizer->colorize(sprintf('%3.2f%%', $this->coverage->getPathsCoverageValue() * 100.0))
					)
				;
			}

			if (sizeof($this->coverage->getBranches()) > 0)
			{
				$string .= $this->titlePrompt .
					sprintf(
						$this->locale->_('%s: %s'),
						$this->titleColorizer->colorize($this->locale->_('Branch coverage value')),
						$this->coverageColorizer->colorize(sprintf('%3.2f%%', $this->coverage->getBranchesCoverageValue() * 100.0))
					)
				;
			}

			$string .= PHP_EOL;

			if ($this->showMissingCodeCoverage === true)
			{
				foreach ($this->coverage->getMethods() as $class => $methods)
				{
					$classCoverage = $this->coverage->getValueForClass($class);
					$classPathsCoverage = $this->coverage->getPathsCoverageValueForClass($class);
					$classBranchesCoverage = $this->coverage->getBranchesCoverageValueForClass($class);

					if ($classCoverage < 1.0)
					{
						$string .= $this->classPrompt .
							sprintf(
								$this->locale->_('%s: %s%s%s'),
								$this->titleColorizer->colorize($this->locale->_('Class %s', $class)),
								$this->coverageColorizer->colorize(sprintf('%s%3.2f%%', ($classPathsCoverage !== null || $classBranchesCoverage !== null ? 'Line: ' : ''), $classCoverage * 100.0)),
								$classPathsCoverage !== null ? $this->coverageColorizer->colorize(sprintf(' Path: %3.2f%%', $classPathsCoverage * 100.0)) : '',
								$classBranchesCoverage !== null ? $this->coverageColorizer->colorize(sprintf(' Branch: %3.2f%%', $classBranchesCoverage * 100.0)) : ''
							) .
							PHP_EOL
						;

						foreach (array_keys($methods) as $method)
						{
							$methodCoverage = $this->coverage->getValueForMethod($class, $method);
							$methodPathsCoverage = $this->coverage->getPathsCoverageValueForMethod($class, $method);
							$methodBranchesCoverage = $this->coverage->getBranchesCoverageValueForMethod($class, $method);

							if ($methodCoverage < 1.0)
							{
								$string .= $this->methodPrompt .
									sprintf(
										$this->locale->_('%s: %s%s%s'),
										$this->titleColorizer->colorize($this->locale->_('     ::%s()', $method)),
										$this->coverageColorizer->colorize(sprintf('%s%3.2f%%', ($methodPathsCoverage !== null || $methodBranchesCoverage !== null ? 'Line: ' : ''), $methodCoverage * 100.0)),
										$methodPathsCoverage !== null ? $this->coverageColorizer->colorize(sprintf(', Path: %3.2f%%', $methodPathsCoverage * 100.0)) : '',
										$methodBranchesCoverage !== null ? $this->coverageColorizer->colorize(sprintf(', Branch: %3.2f%%', $methodBranchesCoverage * 100.0)) : ''
									) .
									PHP_EOL;
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
