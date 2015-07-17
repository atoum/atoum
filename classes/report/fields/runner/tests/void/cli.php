<?php

namespace mageekguy\atoum\report\fields\runner\tests\void;

use
	mageekguy\atoum,
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

	public function __construct()
	{
		parent::__construct();

		$this
			->setTitlePrompt()
			->setTitleColorizer()
			->setMethodPrompt()
			->setMethodColorizer()
		;
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
					$string .= $this->methodPrompt . $this->methodColorizer->colorize(sprintf('%s::%s()', $voidMethod['class'], $voidMethod['method'])) . PHP_EOL;
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
}
