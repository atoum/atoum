<?php

namespace mageekguy\atoum\template\parser;

use
	mageekguy\atoum\exceptions
;

class exception extends exceptions\runtime
{
	protected $errorLine = 0;
	protected $errorOffset = 0;

	public function __construct($message, $errorLine, $errorOffset, $previousException = null)
	{
		parent::__construct($message, 0, $previousException);

		$this->errorLine = $errorLine;
		$this->errorOffset = $errorOffset;
	}

	public function getErrorLine()
	{
		return $this->errorLine;
	}

	public function getErrorOffset()
	{
		return $this->errorOffset;
	}
}
