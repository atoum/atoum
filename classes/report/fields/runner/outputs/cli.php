<?php

namespace mageekguy\atoum\report\fields\runner\outputs;

use
	\mageekguy\atoum,
	\mageekguy\atoum\locale,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\report\fields\runner\outputs
;

class cli extends outputs
{
	protected $titlePrompt = null;
	protected $titleColorizer = null;
	protected $methodPrompt = null;
	protected $methodColorizer = null;
	protected $outputPrompt = null;
	protected $outputColorizer = null;

	public function __construct(prompt $titlePrompt = null, colorizer $titleColorizer = null, prompt $methodPrompt = null, colorizer $methodColorizer = null, prompt $outputPrompt = null, colorizer $outputColorizer = null, locale $locale = null)
	{
		parent::__construct($locale);

		if ($titlePrompt !== null)
		{
			$this->setTitlePrompt($titlePrompt);
		}

		if ($titleColorizer !== null)
		{
			$this->setTitleColorizer($titleColorizer);
		}

		if ($methodPrompt !== null)
		{
			$this->setMethodPrompt($methodPrompt);
		}

		if ($methodColorizer !== null)
		{
			$this->setMethodColorizer($methodColorizer);
		}

		if ($outputPrompt !== null)
		{
			$this->setOutputPrompt($outputPrompt);
		}

		if ($outputColorizer !== null)
		{
			$this->setOutputColorizer($outputColorizer);
		}
	}

	public function setTitlePrompt($prompt)
	{
		$this->titlePrompt = $prompt;

		return $this;
	}

	public function getTitlePrompt()
	{
		return $this->titlePrompt;
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

	public function setMethodPrompt($prompt)
	{
		$this->methodPrompt = $prompt;

		return $this;
	}

	public function getMethodPrompt()
	{
		return $this->methodPrompt;
	}

	public function setMethodColorizer(colorizer $colorizer)
	{
		$this->methodColorizer = $colorizer;

		return $this;
	}

	public function getMethodColorizer()
	{
		return $this->methodColorizer;
	}

	public function setOutputPrompt($prompt)
	{
		$this->outputPrompt = $prompt;

		return $this;
	}

	public function getOutputPrompt()
	{
		return $this->outputPrompt;
	}

	public function setOutputColorizer(colorizer $colorizer)
	{
		$this->outputColorizer = $colorizer;

		return $this;
	}

	public function getOutputColorizer()
	{
		return $this->outputColorizer;
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
				$string .= $this->titlePrompt . $this->colorize(sprintf($this->locale->__('There is %d output:', 'There are %d outputs:', $sizeOfOutputs), $sizeOfOutputs), $this->titleColorizer) . PHP_EOL;

				foreach ($outputs as $output)
				{
					$string .= $this->methodPrompt . $this->colorize($output['class'] . '::' . $output['method'] . '():', $this->methodColorizer) . PHP_EOL;

					foreach (explode(PHP_EOL, rtrim($output['value'])) as $line)
					{
						$string .= $this->outputPrompt . $this->colorize($line, $this->outputColorizer) . PHP_EOL;
					}
				}
			}
		}

		return $string;
	}

	private function colorize($string, $colorizer)
	{
		return $colorizer === null ? $string : $colorizer->colorize($string);
	}
}

?>
