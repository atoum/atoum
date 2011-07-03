<?php

namespace mageekguy\atoum\report\fields\runner\failures;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer
;

class cli extends report\fields\runner\failures
{
	const defaultTitlePrompt = '> ';
	const defaultMethodPrompt = '=> ';

	protected $titlePrompt = null;
	protected $titleColorizer = null;
	protected $methodPrompt = null;
	protected $methodColorizer = null;

	public function __construct(prompt $titlePrompt = null, colorizer $titleColorizer = null, prompt $methodPrompt = null, colorizer $methodColorizer = null, atoum\locale $locale = null)
	{
		parent::__construct($locale);

		if ($titlePrompt === null)
		{
			$titlePrompt = new prompt(self::defaultTitlePrompt);
		}

		if ($titleColorizer === null)
		{
			$titleColorizer = new colorizer('0;31');
		}

		if ($methodPrompt === null)
		{
			$methodPrompt = new prompt(self::defaultMethodPrompt, new colorizer('0;31'));
		}

		if ($methodColorizer === null)
		{
			$methodColorizer = new colorizer('0;31');
		}

		$this
			->setTitlePrompt($titlePrompt)
			->setTitleColorizer($titleColorizer)
			->setMethodPrompt($methodPrompt)
			->setMethodColorizer($methodColorizer)
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

	public function __toString()
	{
		$string = '';

		if ($this->runner !== null)
		{
			$fails = $this->runner->getScore()->getFailAssertions();

			$numberOfFails = sizeof($fails);

			if ($numberOfFails > 0)
			{
				$string .= $this->titlePrompt . $this->titleColorizer->colorize(sprintf($this->locale->__('There is %d failure:', 'There are %d failures:', $numberOfFails), $numberOfFails)) . PHP_EOL;

				foreach ($fails as $fail)
				{
					$string .= $this->methodPrompt . $this->methodColorizer->colorize($fail['class'] . '::' . $fail['method'] . '():') . PHP_EOL;
					$string .= sprintf($this->locale->_('In file %s on line %d, %s failed : %s'), $fail['file'], $fail['line'], $fail['asserter'], $fail['fail']) . PHP_EOL;
				}
			}
		}

		return $string;
	}
}

?>
