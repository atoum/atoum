<?php

namespace mageekguy\atoum\php\tokenizer\iterator;

use
	mageekguy\atoum\exceptions
;

abstract class value implements \iterator, \countable
{
	protected $parent = null;

	public function setParent(value $parent)
	{
		if ($this->parent !== null)
		{
			throw new exceptions\runtime('Parent is already set');
		}

		$parent->append($this);

		return $this;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function getRoot()
	{
		$root = null;

		$parent = $this->getParent();

		while ($parent !== null)
		{
			$root = $parent;

			$parent = $parent->getParent();
		}

		return $root;
	}

	public abstract function __toString();
	public abstract function prev();
	public abstract function end();
	public abstract function append(value $value);
	public abstract function getValue();
	public abstract function seek($key);
}
