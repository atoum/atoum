<?php

namespace mageekguy\atoum\report\fields\runner\exceptions;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report
;

class string extends report\fields\runner\exceptions
{
	const defaultTitlePrompt = '> ';
	const defaultMethodPrompt = '=> ';
	const defaultExceptionPrompt = '==> ';

	protected $titlePrompt = '';
	protected $methodPrompt = '';
	protected $exceptionPrompt = '';

	public function __construct(atoum\locale $locale = null, $titlePrompt = null, $methodPrompt = null, $exceptionPrompt = null)
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

		if ($exceptionPrompt === null)
		{
			$exceptionPrompt = static::defaultExceptionPrompt;
		}

		$this
			->setTitlePrompt($titlePrompt)
			->setMethodPrompt($methodPrompt)
			->setExceptionPrompt($exceptionPrompt)
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

	public function setExceptionPrompt($prompt)
	{
		return $this->setPrompt($this->exceptionPrompt, $prompt);
	}

	public function getExceptionPrompt()
	{
		return $this->exceptionPrompt;
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
				$string .= self::defaultTitlePrompt . sprintf($this->locale->__('There is %d exception:', 'There are %d exceptions:', $sizeOfErrors), $sizeOfErrors) . PHP_EOL;

				$class = null;
				$method = null;

				foreach ($exceptions as $exception)
				{
					if ($exception['class'] !== $class || $exception['method'] !== $method)
					{
						$string .= self::defaultMethodPrompt . $exception['class'] . '::' . $exception['method'] . '():' . PHP_EOL;

						$class = $exception['class'];
						$method = $exception['method'];
					}

					$string .= self::defaultExceptionPrompt . sprintf($this->locale->_('Exception throwed in file %s on line %d:'), $exception['file'], $exception['line']) . PHP_EOL;

					foreach (explode(PHP_EOL, rtrim($exception['value'])) as $line)
					{
						$string .= $line . PHP_EOL;
					}
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
