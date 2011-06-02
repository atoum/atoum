<?php

namespace mageekguy\atoum\template;

use
	\mageekguy\atoum
;

class iterator implements \iterator, \countable
{
	protected $tags = array();

	public function addTag($tag, atoum\template $template)
	{
		$children = $template->getChildren();

		while (($child = array_shift($children)) !== null)
		{
			if ($child->getTag() === $tag)
			{
				$this->tags[] = $child;
			}

			$children = array_merge($child->getChildren(), $children);
		}

		return $this;
	}

	public function __get($tag)
	{
		$iterator = new self();

		foreach ($this->tags as $innerTag)
		{
			$iterator->addTag($tag, $innerTag);
		}

		return $iterator;
	}

	public function rewind()
	{
		reset($this->tags);

		return $this;
	}

	public function valid()
	{
		return (key($this->tags) !== null);
	}

	public function current()
	{
		return current($this->tags) ?: null;
	}

	public function key()
	{
		return key($this->tags);
	}

	public function next()
	{
		next($this->tags);

		return $this;
	}

	public function count()
	{
		return sizeof($this->tags);
	}
}

?>
