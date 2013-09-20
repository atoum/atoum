<?php

namespace mageekguy\atoum\cli;

use
	mageekguy\atoum,
	mageekguy\atoum\writer
;

class colorizer implements writer\decorator
{
	protected $cli = null;
	protected $pattern = null;
	protected $foreground = null;
	protected $background = null;

	public function __construct($foreground = null, $background = null, atoum\cli $cli = null)
	{
		if ($foreground !== null)
		{
			$this->setForeground($foreground);
		}

		if ($background !== null)
		{
			$this->setBackground($background);
		}

		$this->setCli($cli);
	}

	public function setCli(atoum\cli $cli = null)
	{
		$this->cli = $cli ?: new atoum\cli();

		return $this;
	}

	public function getCli()
	{
		return $this->cli;
	}

	public function setPattern($pattern)
	{
		$this->pattern = $pattern;

		return $this;
	}

	public function getPattern()
	{
		return $this->pattern;
	}

	public function setForeground($foreground)
	{
		$this->foreground = (string) $foreground;

		return $this;
	}

	public function getForeground()
	{
		return $this->foreground;
	}

	public function setBackground($background)
	{
		$this->background = (string) $background;

		return $this;
	}

	public function getBackground()
	{
		return $this->background;
	}

	public function colorize($string)
	{
		if ($this->cli->isTerminal() === true && ($this->foreground !== null || $this->background !== null))
		{
			$pattern = $this->pattern ?: '/^(.*)$/';

			$replace = '\1';

			if ($this->background !== null || $this->foreground !== null)
			{
				if ($this->background !== null)
				{
					$replace = "\033[" . $this->background . 'm' . $replace;
				}

				if ($this->foreground !== null)
				{
					$replace = "\033[" . $this->foreground . 'm' . $replace;
				}

				$replace .= "\033[0m";
			}

			$string = preg_replace($pattern, $replace, $string);
		}

		return $string;
	}

	public function decorate($string)
	{
		return $this->colorize($string);
	}
}
