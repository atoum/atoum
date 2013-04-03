<?php

namespace mageekguy\atoum\report\fields\runner\tests\uncompleted;

use
	mageekguy\atoum,
	mageekguy\atoum\report,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer
;

class cli extends report\fields\runner\tests\uncompleted
{
	protected $titlePrompt = null;
	protected $titleColorizer = null;
	protected $methodPrompt = null;
	protected $methodColorizer = null;
	protected $outputPrompt = null;
	protected $outputColorizer = null;

	public function __construct()
	{
		parent::__construct();

		$this
			->setTitlePrompt()
			->setTitleColorizer()
			->setMethodPrompt()
			->setMethodColorizer()
			->setOutputPrompt()
			->setOutputColorizer()
		;
	}

	public function __toString()
	{
		$string = '';

		if ($this->runner !== null)
		{
			$uncompletedMethods = $this->runner->getScore()->getUncompletedMethods();

			$sizeOfUncompletedMethod = sizeof($uncompletedMethods);

			if ($sizeOfUncompletedMethod > 0)
			{
				$string .=
					$this->titlePrompt .
					sprintf(
						$this->locale->_('%s:'),
						$this->titleColorizer->colorize(sprintf($this->locale->__('There is %d uncompleted method', 'There are %d uncompleted methods', $sizeOfUncompletedMethod), $sizeOfUncompletedMethod))
					) .
					PHP_EOL
				;

				foreach ($uncompletedMethods as $uncompletedMethod)
				{
					$string .=
						$this->methodPrompt .
						sprintf(
							$this->locale->_('%s:'),
							$this->methodColorizer->colorize(sprintf('%s::%s() with exit code %d', $uncompletedMethod['class'], $uncompletedMethod['method'], $uncompletedMethod['exitCode']))
						) .
						PHP_EOL
					;

					$lines = explode(PHP_EOL, trim($uncompletedMethod['output']));

					$string .= $this->outputPrompt . 'output(' . strlen($uncompletedMethod['output']) . ') "' . array_shift($lines);

					foreach ($lines as $line)
					{
						$string .= PHP_EOL . $this->outputPrompt . $line;
					}

					$string .= '"' . PHP_EOL;
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

	public function setTitleColorizer(colorizer $colorizer = null)
	{
		$this->titleColorizer = $colorizer ?: new colorizer();

		return $this;
	}

	public function getTitleColorizer()
	{
		return $this->titleColorizer;
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

	public function setMethodColorizer(colorizer $colorizer = null)
	{
		$this->methodColorizer = $colorizer ?: new colorizer();

		return $this;
	}

	public function getMethodColorizer()
	{
		return $this->methodColorizer;
	}

	public function setOutputPrompt(prompt $prompt = null)
	{
		$this->outputPrompt = $prompt ?: new prompt();

		return $this;
	}

	public function getOutputPrompt()
	{
		return $this->outputPrompt;
	}

	public function setOutputColorizer(colorizer $colorizer = null)
	{
		$this->outputColorizer = $colorizer ?: new colorizer();

		return $this;
	}

	public function getOutputColorizer()
	{
		return $this->outputColorizer;
	}
}
