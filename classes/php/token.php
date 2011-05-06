<?php

namespace mageekguy\atoum\php;

use
	\mageekguy\atoum\exceptions
;

class token
{
	protected $tag = '';
	protected $value = '';
	protected $line = 0;

	public function __construct($tag, $value, $line)
	{
		$this->tag = $tag;
		$this->value = $value;
		$this->line = $line;
	}

	public function getTag()
	{
		return $this->tag;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getLine()
	{
		return $this->line;
	}
}

?>
