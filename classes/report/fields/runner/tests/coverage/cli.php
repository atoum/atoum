<?php

namespace mageekguy\atoum\report\fields\runner\tests\coverage;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report
;

class cli extends report\fields\runner\tests\coverage
{
	protected $titlePrompt = null;
	protected $classPrompt = null;
	protected $methodPrompt = null;
	protected $titleColorizer = null;
	protected $coverageColorizer = null;

	public function __construct(prompt $titlePrompt = null, prompt $classPrompt = null, prompt $methodPrompt = null, colorizer $titleColorizer = null, colorizer $coverageColorizer = null, locale $locale = null)
	{
		parent::__construct($locale);

		$this
			->setTitlePrompt($titlePrompt ?: new prompt())
			->setClassPrompt($classPrompt ?: new prompt())
			->setMethodPrompt($methodPrompt ?: new prompt())
			->setTitleColorizer($titleColorizer ?: new colorizer())
			->setCoverageColorizer($coverageColorizer ?: new colorizer())
		;
	}

	public function setTitlePrompt(prompt $prompt)
	{
		$this->titlePrompt = $prompt;

		return $this;
	}

	public function getTitlePrompt()
	{
		return $this->titlePrompt;
	}

	public function setClassPrompt($prompt)
	{
		$this->classPrompt = $prompt;

		return $this;
	}

	public function getClassPrompt()
	{
		return $this->classPrompt;
	}

	public function setMethodPrompt($prompt)
	{
		$this->methodPrompt = $prompt;

		return $this;
	}

	public function getMethodPrompt()
	{
		return $this->methodPrompt;
	}

	public function setTitleColorizer(colorizer $colorizer)
	{
		$this->titleColorizer = $colorizer;

		return $this;
	}

	public function getTitleColorizer()
	{
		return $this->titleColorizer;
	}

	public function setCoverageColorizer(colorizer $colorizer)
	{
		$this->coverageColorizer = $colorizer;

		return $this;
	}

	public function getCoverageColorizer()
	{
		return $this->coverageColorizer;
	}

	public function __toString()
	{
		$string = '';


		if ($this->coverage !== null && sizeof($this->coverage) > 0)
		{
			$string .= $this->titlePrompt .
				sprintf(
					$this->locale->_('%s: %s'),
					$this->titleColorizer->colorize($this->locale->_('Code coverage value')),
					$this->coverageColorizer->colorize(sprintf('%3.2f%%', $this->coverage->getValue() * 100.0))
				) .
				PHP_EOL
			;

			foreach ($this->coverage->getMethods() as $class => $methods)
			{
				$classCoverage = $this->coverage->getValueForClass($class);

				if ($classCoverage < 1.0)
				{
					$string .= $this->classPrompt .
						sprintf(
							$this->locale->_('%s: %s'),
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
									$this->locale->_('%s: %s'),
									$this->titleColorizer->colorize(sprintf($this->locale->_('%s::%s()'), $class, $method)),
									$this->coverageColorizer->colorize(sprintf('%3.2f%%', $methodCoverage * 100.0))
								) .
								PHP_EOL
							;
						}
					}
				}
			}
		}

		return $string;
	}
}
