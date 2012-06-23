<?php

namespace mageekguy\atoum\cli;

use
	mageekguy\atoum
;

class colorizer
{
	protected $cli = null;
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

		$this->setCli($cli ?: new atoum\cli());
	}

	public function setCli(atoum\cli $cli)
	{
		$this->cli = $cli;

		return $this;
	}

	public function getCli()
	{
		return $this->cli;
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
			if ($this->background !== null)
			{
				$string = "\033[" . $this->background . 'm' . $string;
			}

			if ($this->foreground !== null)
			{
				$string = "\033[" . $this->foreground . 'm' . $string;
			}

			$string .= "\033[0m";
		}

		return $string;
	}
}
