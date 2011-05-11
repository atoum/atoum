<?php

namespace mageekguy\atoum\php\tokenizer;

use
	\mageekguy\atoum\exceptions,
	\mageekguy\atoum\php\tokenizer\iterator
;

class token extends iterator\value
{
	protected $key = 0;
	protected $tag = '';
	protected $string = null;
	protected $line = null;

	public function __construct($tag, $string = null, $line = null)
	{
		$this->tag = $tag;
		$this->string = $string;
		$this->line = $line;
	}

	public function __toString()
	{
		return (string) ($this->string ?: $this->tag);
	}

	public function count()
	{
		return 1;
	}

	public function getTag()
	{
		return $this->tag;
	}

	public function getString()
	{
		return $this->string;
	}

	public function getLine()
	{
		return $this->line;
	}

	public function key()
	{
		return $this->key;
	}

	public function current()
	{
		return $this;
	}

	public function rewind()
	{
		$this->key = 0;

		return $this;
	}

	public function end()
	{
		$this->key = 0;

		return $this;
	}

	public function valid()
	{
		return ($this->key === 0);
	}

	public function next()
	{
		if ($this->valid() === true)
		{
		$this->key = null;
		}

		return $this;
	}

	public function prev()
	{
		if ($this->valid() === true)
		{
			$this->key--;
		}

		return $this;
	}

	public function append(iterator\value $value)
	{
		throw new exceptions\logic('Unable to append something to an instance of class ' . get_class($this));
	}

	public function seek($key)
	{
		if ($key != 0)
		{
			$this->key = null;
		}

		return $this;
	}

	public function getParent()
	{
		return null;
	}

	public function getValue()
	{
		return $this->getString();
	}
}

?>
