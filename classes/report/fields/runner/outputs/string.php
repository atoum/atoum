<?php

namespace mageekguy\atoum\report\fields\runner\outputs;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report
;

class string extends report\fields\runner\outputs
{
	const defaultTitlePrompt = '> ';
	const defaultMethodPrompt = '=> ';

	protected $titlePrompt = '';
	protected $methodPrompt = '';

	public function __construct(atoum\locale $locale = null, $titlePrompt = null, $methodPrompt = null)
	{
		parent::__construct($locale);

		if ($titlePrompt === null)
		{
			$titlePrompt = static::defaultTitlePrompt;
		}

		if ($methodPrompt === null)
		{
			$methodPrompt = static::defaultMethodPrompt;
		}

		$this
			->setTitlePrompt($titlePrompt)
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

		if ($this->runner !== null)
		{
			$outputs = $this->runner->getScore()->getOutputs();

			$sizeOfOutputs = sizeof($outputs);

			if ($sizeOfOutputs > 0)
			{
				$string .= $this->titlePrompt . sprintf($this->locale->__('There is %d output:', 'There are %d outputs:', $sizeOfOutputs), $sizeOfOutputs) . PHP_EOL;

				foreach ($outputs as $output)
				{
					$string .= $this->methodPrompt . $output['class'] . '::' . $output['method'] . '():' . PHP_EOL;

					foreach (explode(PHP_EOL, rtrim($output['value'])) as $line)
					{
						$string .= $line . PHP_EOL;
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
