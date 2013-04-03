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

	public function __construct()
	{
		parent::__construct();

		$this
			->setTitlePrompt()
			->setClassPrompt()
			->setMethodPrompt()
			->setTitleColorizer()
			->setCoverageColorizer()
		;
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

	public function setTitlePrompt(prompt $prompt = null)
	{
		$this->titlePrompt = $prompt ?: new prompt();

		return $this;
	}

	public function getTitlePrompt()
	{
		return $this->titlePrompt;
	}

	public function setClassPrompt(prompt $prompt = null)
	{
		$this->classPrompt = $prompt ?: new prompt();

		return $this;
	}

	public function getClassPrompt()
	{
		return $this->classPrompt;
	}

	public function setMethodPrompt(prompt $prompt = null)
	{
		$this->methodPrompt = $prompt ?: new prompt();

		return $this;
	}

	public function getMethodPrompt()
	{
		return $this->methodPrompt;
	}

	public function setTitleColorizer(colorizer $colorizer = null)
	{
		$this->titleColorizer = $colorizer ?: new colorizer();

		return $this;
	}

	public function getTitleColorizer()
	{
		return $this->titleColorizer;
	}

	public function setCoverageColorizer(colorizer $colorizer = null)
	{
		$this->coverageColorizer = $colorizer ?: new colorizer();

		return $this;
	}

	public function getCoverageColorizer()
	{
		return $this->coverageColorizer;
	}
}
