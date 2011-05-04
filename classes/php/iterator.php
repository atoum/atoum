<?php

namespace mageekguy\atoum\php;

class iterator implements \iterator, \countable
{
	protected $parent = null;
	protected $key = null;
	protected $size = 0;
	protected $values = array();

	public function valid()
	{
		return (key($this->values) !== null);
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
		return $this->key;
	}

	public function next()
	{
		if ($this->valid() === true)
		{
			$currentValue = current($this->values);

			if ($currentValue instanceof self === false)
			{
				next($this->values);

				if ($this->valid() === false)
				{
					$this->key = null;
				}
				else
				{
					$this->key++;
				}
			}
			else if ($currentValue->next()->valid() === true)
			{
				$this->key++;
			}
			else
			{
				next($this->values);

				if ($this->valid() === false)
				{
					$this->key = null;
				}
				else
				{
					$this->key++;

					$currentValue = current($this->values);

					if ($currentValue instanceof self)
					{
						$currentValue->rewind();
					}
				}
			}
		}

		return $this;
	}

	public function rewind()
	{
		reset($this->values);

		$this->key = key($this->values);

		return $this;
	}

	public function append($value)
	{
		$this->values[] = $value;

		$size = 1;

		if ($value instanceof self)
		{
			$size = sizeof($value);
			$value->parent = $this;
		}

		$this->size += $size;

		if ($this->parent !== null)
		{
			$this->parent->size += $size;
		}

		if ($this->key === null)
		{
			end($this->values);

			$this->key = $this->size - 1;
		}

		return $this;
	}

	public function count()
	{
		return $this->size;
	}
}

?>
