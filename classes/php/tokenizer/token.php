<?php

namespace mageekguy\atoum\php\tokenizer;

use
	mageekguy\atoum\exceptions,
	mageekguy\atoum\php\tokenizer\iterator
;

class token extends iterator\value
{
	protected $key = 0;
	protected $tag = '';
	protected $string = null;
	protected $line = null;

	public function __construct($tag, $string = null, $line = null, iterator\value $parent = null)
	{
		$this->tag = $tag;
		$this->string = $string;
		$this->line = $line;

		if ($parent !== null)
		{
			$this->setParent($parent);
		}
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
		return $this->key === 0 ? 0 : null;
	}

	public function current()
	{
		return $this->key !== 0 ? null : $this;
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
			$this->key = null;
		}

		return $this;
	}

	public function append(iterator\value $value)
	{
		throw new exceptions\logic(__METHOD__ . '() is unavailable');
	}

	public function seek($key)
	{
		if ($key != 0)
		{
			$this->key = null;
		}
		else
		{
			$this->key = 0;
		}

		return $this;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function getValue()
	{
		return $this->getString();
	}
}
