<?php

namespace mageekguy\atoum\php\tokenizer;

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

	public function __toString()
	{
		return (string) ($this->value ?: $this->tag);
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
