<?php

namespace mageekguy\atoum\cli;

class colorizer
{
	protected $foreground = null;
	protected $background = null;

	public function __construct($foreground = null, $background = null)
	{
		if ($foreground !== null)
		{
			$this->setForeground($foreground);
		}

		if ($background !== null)
		{
			$this->setBackground($background);
		}
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
		if ($this->foreground !== null || $this->background !== null)
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

?>
