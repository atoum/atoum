<?php

namespace mageekguy\atoum\php;

use
	\mageekguy\atoum\exceptions
;

class iterator implements \iterator, \countable
{
	protected $key = null;
	protected $size = 0;
	protected $values = array();
	protected $parent = null;

	public function valid()
	{
		return ($this->key !== null && $this->key >= 0 && $this->key < $this->size);
	}

	public function current()
	{
		$value = null;

		if ($this->valid() === true)
		{
			$value = current($this->values);

			if ($value instanceof self)
			{
				$value = $value->current();
			}
		}

		return $value;
	}

	public function key()
	{
		return $this->key < 0 || $this->key >= $this->size ? null : $this->key;
	}

	public function prev()
	{
		if ($this->valid() === true)
		{
			$currentKey = key($this->values);
			$currentValue = current($this->values);

			if ($currentValue instanceof self === false)
			{
				prev($this->values);
				$currentValue = current($this->values);
			}
			else
			{
				$currentValue->prev();

				if ($currentValue->valid() === false)
				{
					prev($this->values);
					$currentValue = current($this->values);
				}
			}

			while ($currentValue instanceof self && sizeof($currentValue) <= 0)
			{
				prev($this->values);
				$currentValue = current($this->values);
			}

			if ($currentValue instanceof self === true && key($this->values) !== $currentKey)
			{
				$currentValue->end();
			}

			$this->key--;
		}

		return $this;
	}

	public function next()
	{
		if ($this->valid() === true)
		{
			$currentKey = key($this->values);
			$currentValue = current($this->values);

			if ($currentValue instanceof self === false)
			{
				next($this->values);
				$currentValue = current($this->values);
			}
			else
			{
				$currentValue->next();

				if ($currentValue->valid() === false)
				{
					next($this->values);
					$currentValue = current($this->values);
				}
			}

			while ($currentValue instanceof self && sizeof($currentValue) <= 0)
			{
				next($this->values);
				$currentValue = current($this->values);
			}

			if ($currentValue instanceof self === true && key($this->values) !== $currentKey)
			{
				$currentValue->rewind();
			}

			$this->key++;
		}

		return $this;
	}

	public function rewind()
	{
		if ($this->size > 0)
		{
			reset($this->values);

			$currentValue = current($this->values);

			while ($currentValue instanceof self && $currentValue->rewind()->valid() == false)
			{
				next($this->values);
				$currentValue = current($this->values);
			}

			$this->key = 0;
		}

		return $this;
	}

	public function end()
	{
		if ($this->size > 0)
		{
			end($this->values);

			$currentValue = current($this->values);

			while ($currentValue instanceof self && $currentValue->end()->valid() == false)
			{
				prev($this->values);
				$currentValue = current($this->values);
			}

			$this->key = $this->size - 1;
		}

		return $this;
	}

	public function append($value)
	{
		$this->values[] = $value;

		if ($this->key === null)
		{
			$this->key = 0;
		}

		$size = 1;

		if ($value instanceof self)
		{
			if ($value->parent !== null)
			{
				throw new exceptions\runtime('Unable to append iterator, it has already a parent');
			}

			$size = sizeof($value->rewind());

			$value->parent = $this;
		}

		$this->size += $size;

		if ($this->parent !== null)
		{
			$this->parent->size += $size;
		}

		return $this;
	}

	public function count()
	{
		return $this->size;
	}
}

?>
