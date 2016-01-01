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

	public function __construct()
	{
		parent::__construct();

		$this
			->setTitlePrompt()
			->setTitleColorizer()
			->setMethodPrompt()
			->setMethodColorizer()
			->setExceptionPrompt()
			->setExceptionColorizer()
		;
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
							$this->colorizeException($this->locale->_('An exception has been thrown in file %s on line %d', $exception['file'], $exception['line']))
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

	public function setExceptionPrompt(prompt $prompt = null)
	{
		$this->exceptionPrompt = $prompt ?: new prompt();

		return $this;
	}

	public function getExceptionPrompt()
	{
		return $this->exceptionPrompt;
	}

	public function setExceptionColorizer(colorizer $colorizer = null)
	{
		$this->exceptionColorizer = $colorizer ?: new colorizer();

		return $this;
	}

	public function getExceptionColorizer()
	{
		return $this->exceptionColorizer;
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
