<?php

namespace mageekguy\atoum\report\fields\runner\tests\skipped;

use
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\runner\tests\skipped
;

class cli extends skipped
{
	protected $titlePrompt = null;
	protected $titleColorizer = null;
	protected $methodPrompt = null;
	protected $methodColorizer = null;
	protected $messagePrompt = null;
	protected $messageColorizer = null;

	public function __construct()
	{
		parent::__construct();

		$this
			->setTitlePrompt()
			->setTitleColorizer()
			->setMethodPrompt()
			->setMethodColorizer()
			->setMessageColorizer()
		;
	}

	public function __toString()
	{
		$string = '';

		if ($this->runner !== null)
		{
			$skippedMethods = $this->runner->getScore()->getSkippedMethods();

			$sizeOfSkippedMethod = sizeof($skippedMethods);

			if ($sizeOfSkippedMethod > 0)
			{
				$string .=
					$this->titlePrompt .
					sprintf(
						$this->locale->_('%s:'),
						$this->titleColorizer->colorize(sprintf($this->locale->__('There is %d skipped method', 'There are %d skipped methods', $sizeOfSkippedMethod), $sizeOfSkippedMethod))
					) .
					PHP_EOL
				;

				foreach ($skippedMethods as $skippedMethod)
				{
					$string .= $this->methodPrompt . $this->locale->_('%s: %s', $this->methodColorizer->colorize(sprintf('%s::%s()', $skippedMethod['class'], $skippedMethod['method'])), $this->messageColorizer->colorize($skippedMethod['message'])) . PHP_EOL;
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

	public function setMessageColorizer(colorizer $colorizer = null)
	{
		$this->messageColorizer = $colorizer ?: new colorizer();

		return $this;
	}

	public function getMessageColorizer()
	{
		return $this->messageColorizer;
	}
}
