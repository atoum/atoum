<?php

namespace mageekguy\atoum\report\fields\runner\failures;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report
;

class string extends report\fields\runner\failures
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
			$fails = $this->runner->getScore()->getFailAssertions();

			$numberOfFails = sizeof($fails);

			if ($numberOfFails > 0)
			{
				$string .= $this->titlePrompt . sprintf($this->locale->__('There is %d failure:', 'There are %d failures:', $numberOfFails), $numberOfFails) . PHP_EOL;

				foreach ($fails as $fail)
				{
					$string .= $this->methodPrompt . $fail['class'] . '::' . $fail['method'] . '():' . PHP_EOL;
					$string .= sprintf($this->locale->_('In file %s on line %d, %s failed : %s'), $fail['file'], $fail['line'], $fail['asserter'], $fail['fail']) . PHP_EOL;
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
