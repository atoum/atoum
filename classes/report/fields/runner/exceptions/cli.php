<?php

namespace mageekguy\atoum\report\fields\runner\exceptions;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report
;

class cli extends report\fields\runner\exceptions
{
	protected $titlePrompt = null;
	protected $titleColorizer = null;
	protected $methodPrompt = null;
	protected $methodColorizer = null;
	protected $exceptionPrompt = null;
	protected $exceptionColorizer = null;

	public function __construct(prompt $titlePrompt = null, colorizer $titleColorizer = null, prompt $methodPrompt = null, colorizer $methodColorizer = null, prompt $exceptionPrompt = null, colorizer $exceptionColorizer = null, locale $locale = null)
	{
		parent::__construct($locale);

		$this
			->setTitlePrompt($titlePrompt ?: new prompt())
			->setTitleColorizer($titleColorizer ?: new colorizer())
			->setMethodPrompt($methodPrompt ?: new prompt())
			->setMethodColorizer($methodColorizer ?: new colorizer())
			->setExceptionPrompt($exceptionPrompt ?: new prompt())
			->setExceptionColorizer($exceptionColorizer ?: new colorizer())
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

	public function setExceptionPrompt($prompt)
	{
		$this->exceptionPrompt = $prompt;

		return $this;
	}

	public function getExceptionPrompt()
	{
		return $this->exceptionPrompt;
	}

	public function setExceptionColorizer(colorizer $colorizer)
	{
		$this->exceptionColorizer = $colorizer;

		return $this;
	}

	public function getExceptionColorizer()
	{
		return $this->exceptionColorizer;
	}

	public function __toString()
	{
		$string = '';

		if ($this->runner !== null)
		{
			$exceptions = $this->runner->getScore()->getExceptions();

			$sizeOfErrors = sizeof($exceptions);

			if ($sizeOfErrors > 0)
			{
				$string .=
					$this->titlePrompt .
					sprintf(
						$this->locale->_('%s:'),
						$this->colorizeTitle(sprintf($this->locale->__('There is %d exception', 'There are %d exceptions', $sizeOfErrors), $sizeOfErrors))
					) .
					PHP_EOL
				;

				$class = null;
				$method = null;

				foreach ($exceptions as $exception)
				{
					if ($exception['class'] !== $class || $exception['method'] !== $method)
					{
						$string .=
							$this->methodPrompt .
							sprintf(
								$this->locale->_('%s:'),
								$this->colorizeMethod($exception['class'] . '::' . $exception['method'] . '()')
							) .
							PHP_EOL
						;

						$class = $exception['class'];
						$method = $exception['method'];
					}

					$string .=
						$this->exceptionPrompt .
						sprintf(
							$this->locale->_('%s:'),
							$this->colorizeException(sprintf($this->locale->_('Exception throwed in file %s on line %d'), $exception['file'], $exception['line']))
						) .
						PHP_EOL
					;

					foreach (explode(PHP_EOL, rtrim($exception['value'])) as $line)
					{
						$string .= $this->exceptionPrompt . $line . PHP_EOL;
					}
				}
			}
		}

		return $string;
	}

	private function colorizeTitle($title)
	{
		return $this->titleColorizer === null ? $title : $this->titleColorizer->colorize($title);
	}

	private function colorizeMethod($method)
	{
		return $this->methodColorizer === null ? $method : $this->methodColorizer->colorize($method);
	}

	private function colorizeException($exception)
	{
		return $this->exceptionColorizer === null ? $exception : $this->exceptionColorizer->colorize($exception);
	}
}
