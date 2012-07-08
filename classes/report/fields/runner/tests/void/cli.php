<?php

namespace mageekguy\atoum\report\fields\runner\tests\void;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\report,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer
;

class cli extends report\fields\runner\tests\void
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

		$this
			->setTitlePrompt($titlePrompt ?: new prompt())
			->setTitleColorizer($titleColorizer ?: new colorizer())
			->setMethodPrompt($methodPrompt ?: new prompt())
			->setMethodColorizer($methodColorizer ?: new colorizer())
			->setOutputPrompt($outputPrompt ?: new prompt())
			->setOutputColorizer($outputColorizer ?: new colorizer())
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

	public function setTitleColorizer(colorizer $colorizer)
	{
		$this->titleColorizer = $colorizer;

		return $this;
	}

	public function getTitleColorizer()
	{
		return $this->titleColorizer;
	}

	public function setMethodPrompt(prompt $prompt)
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

	public function setOutputPrompt(prompt $prompt)
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
			$voidMethods = $this->runner->getScore()->getVoidMethods();

			$sizeOfVoidMethod = sizeof($voidMethods);

			if ($sizeOfVoidMethod > 0)
			{
				$string .=
					$this->titlePrompt .
					sprintf(
						$this->locale->_('%s:'),
						$this->titleColorizer->colorize(sprintf($this->locale->__('There is %d void method', 'There are %d void methods', $sizeOfVoidMethod), $sizeOfVoidMethod))
					) .
					PHP_EOL
				;

				foreach ($voidMethods as $voidMethod)
				{
					$string .= $this->methodPrompt . $this->methodColorizer->colorize(sprintf('%s::%s()', $voidMethod['class'], $voidMethod['method'])) .  PHP_EOL;
				}
			}
		}

		return $string;
	}
}
