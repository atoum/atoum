<?php

namespace mageekguy\atoum\cli;

use
	mageekguy\atoum,
	mageekguy\atoum\writer
;

class clear implements writer\decorator
{
	protected $cli = null;

	public function __construct(atoum\cli $cli = null)
	{
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

	public function decorate($string)
	{
		return ($this->cli->isTerminal() === false ? PHP_EOL : "\033[1K\r") . $string;
	}
}
