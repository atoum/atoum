<?php

namespace mageekguy\atoum\report\fields\runner\tests\coverage;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report
;

class cli extends report\fields\runner\tests\coverage
{
	const defaultTitlePrompt = '> ';
	const defaultClassPrompt = '=> ';
	const defaultMethodPrompt = '==> ';

	protected $titlePrompt = '';
	protected $classPrompt = '';
	protected $methodPrompt = '';

	public function __construct(atoum\locale $locale = null, $titlePrompt = null, $classPrompt = null, $methodPrompt = null)
	{
		parent::__construct($locale);

		if ($titlePrompt === null)
		{
			$titlePrompt = static::defaultTitlePrompt;
		}

		if ($classPrompt === null)
		{
			$classPrompt = static::defaultClassPrompt;
		}

		if ($methodPrompt === null)
		{
			$methodPrompt = static::defaultMethodPrompt;
		}

		$this
			->setTitlePrompt($titlePrompt)
			->setClassPrompt($classPrompt)
			->setMethodPrompt($methodPrompt)
		;
	}

	public function setTitlePrompt($prompt)
	{
		return $this->setPrompt($this->titlePrompt, $prompt);
	}

	public function getTitlePrompt()
	{
		return $this->titlePrompt;
	}

	public function setClassPrompt($prompt)
	{
		return $this->setPrompt($this->classPrompt, $prompt);
	}

	public function getClassPrompt()
	{
		return $this->classPrompt;
	}

	public function setMethodPrompt($prompt)
	{
		return $this->setPrompt($this->methodPrompt, $prompt);
	}

	public function getMethodPrompt()
	{
		return $this->methodPrompt;
	}

	public function __toString()
	{
		$string = '';

		if ($this->coverage !== null && sizeof($this->coverage) > 0)
		{
			$string .= $this->titlePrompt . sprintf($this->locale->_('Code coverage value: %3.2f%%'), $this->coverage->getValue() * 100.0) . PHP_EOL;

			foreach ($this->coverage->getMethods() as $class => $methods)
			{
				$classCoverage = $this->coverage->getValueForClass($class);

				if ($classCoverage < 1.0)
				{
					$string .= $this->classPrompt . sprintf($this->locale->_('Class %s: %3.2f%%'), $class, $classCoverage * 100.0) . PHP_EOL;

					foreach (array_keys($methods) as $method)
					{
						$methodCoverage = $this->coverage->getValueForMethod($class, $method);

						if ($methodCoverage < 1.0)
						{
							$string .= $this->methodPrompt . sprintf($this->locale->_('%s::%s(): %3.2f%%'), $class, $method, $methodCoverage * 100.0) . PHP_EOL;
						}
					}
				}
			}
		}

		return $string;
	}

	protected function setPrompt(& $property, $prompt)
	{
		$property = (string) $prompt;

		return $this;
	}
}

?>
