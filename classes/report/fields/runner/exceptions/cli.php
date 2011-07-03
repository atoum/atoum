<?php

namespace mageekguy\atoum\report\fields\runner\exceptions;

use
	\mageekguy\atoum,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\report
;

class cli extends report\fields\runner\exceptions
{
	const defaultTitlePrompt = '> ';
	const defaultMethodPrompt = '=> ';
	const defaultExceptionPrompt = '==> ';

	protected $titlePrompt = null;
	protected $titleColorizer = null;
	protected $methodPrompt = null;
	protected $methodColorizer = null;
	protected $exceptionPrompt = null;
	protected $exceptionColorizer = null;

	public function __construct(prompt $titlePrompt = null, colorizer $titleColorizer = null, prompt $methodPrompt = null, colorizer $methodColorizer = null, prompt $exceptionPrompt = null, colorizer $exceptionColorizer = null, atoum\locale $locale = null)
	{
		parent::__construct($locale);

		if ($titlePrompt === null)
		{
			$titlePrompt = new prompt(self::defaultTitlePrompt);
		}

		if ($titleColorizer === null)
		{
			$titleColorizer = new colorizer('0;35');
		}

		if ($methodPrompt === null)
		{
			$methodPrompt = new prompt(self::defaultMethodPrompt, new colorizer('0;35'));
		}

		if ($methodColorizer === null)
		{
			$methodColorizer = new colorizer('0;35');
		}

		if ($exceptionPrompt === null)
		{
			$exceptionPrompt = new prompt(self::defaultExceptionPrompt, new colorizer('0;35'));
		}

		if ($exceptionColorizer === null)
		{
			$exceptionColorizer = new colorizer('0;35');
		}

		$this
			->setTitlePrompt($titlePrompt)
			->setTitleColorizer($titleColorizer)
			->setMethodPrompt($methodPrompt)
			->setMethodColorizer($methodColorizer)
			->setExceptionPrompt($exceptionPrompt)
			->setExceptionColorizer($exceptionColorizer)
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
				$string .= $this->titlePrompt . $this->titleColorizer->colorize(sprintf($this->locale->__('There is %d exception:', 'There are %d exceptions:', $sizeOfErrors), $sizeOfErrors)) . PHP_EOL;

				$class = null;
				$method = null;

				foreach ($exceptions as $exception)
				{
					if ($exception['class'] !== $class || $exception['method'] !== $method)
					{
						$string .= $this->methodPrompt . $this->methodColorizer->colorize($exception['class'] . '::' . $exception['method'] . '():') . PHP_EOL;

						$class = $exception['class'];
						$method = $exception['method'];
					}

					$string .= $this->exceptionPrompt . $this->exceptionColorizer->colorize(sprintf($this->locale->_('Exception throwed in file %s on line %d:'), $exception['file'], $exception['line'])) . PHP_EOL;

					foreach (explode(PHP_EOL, rtrim($exception['value'])) as $line)
					{
						$string .= $line . PHP_EOL;
					}
				}
			}
		}

		return $string;
	}
}

?>
