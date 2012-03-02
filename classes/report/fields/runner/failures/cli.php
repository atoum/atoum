<?php

namespace mageekguy\atoum\report\fields\runner\failures;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\runner
;

class cli extends runner\failures
{
	protected $titlePrompt = null;
	protected $titleColorizer = null;
	protected $methodPrompt = null;
	protected $methodColorizer = null;

	public function __construct(prompt $titlePrompt = null, colorizer $titleColorizer = null, prompt $methodPrompt = null, colorizer $methodColorizer = null, locale $locale = null)
	{
		parent::__construct($locale);

		$this
			->setTitlePrompt($titlePrompt ?: new prompt())
			->setTitleColorizer($titleColorizer ?: new colorizer())
			->setMethodPrompt($methodPrompt ?: new prompt())
			->setMethodColorizer($methodColorizer ?: new colorizer())
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
				$string .=
					$this->titlePrompt .
					sprintf(
						$this->locale->_('%s:'),
						$this->titleColorizer->colorize(sprintf($this->locale->__('There is %d failure', 'There are %d failures', $numberOfFails), $numberOfFails))
					) .
					PHP_EOL
				;

				foreach ($fails as $fail)
				{
					$string .=
						$this->methodPrompt .
						sprintf(
							'%s:',
							$this->methodColorizer->colorize(($fail['class'] . '::' . $fail['method'] . '()'))
						) .
						PHP_EOL
					;

					switch (true)
					{
						case $fail['case'] === null && $fail['dataSetKey'] === null:
							$string .= sprintf($this->locale->_('In file %s on line %d, %s failed: %s'), $fail['file'], $fail['line'], $fail['asserter'], $fail['fail']);
							break;

						case $fail['case'] === null && $fail['dataSetKey'] !== null:
							$string .= sprintf($this->locale->_('In file %s on line %d, %s failed for data set #%s: %s'), $fail['file'], $fail['line'], $fail['asserter'], $fail['dataSetKey'], $fail['fail']);
							break;

						case $fail['case'] !== null && $fail['dataSetKey'] === null:
							$string .= sprintf($this->locale->_('In file %s on line %d in case \'%s\', %s failed: %s'), $fail['file'], $fail['line'], $fail['case'], $fail['asserter'], $fail['fail']);
							break;

						case $fail['case'] !== null && $fail['dataSetKey'] !== null:
							$string .= sprintf($this->locale->_('In file %s on line %d in case \'%s\', %s failed for data set #%s: %s'), $fail['file'], $fail['line'], $fail['case'],  $fail['asserter'], $fail['dataSetKey'], $fail['fail']);
							break;
					}

					$string .= PHP_EOL;
				}
			}
		}

		return $string;
	}
}

?>
