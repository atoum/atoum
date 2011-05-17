<?php

namespace mageekguy\atoum\template;

class iterator extends \ArrayIterator
{
	public function __call($method, $arguments)
	{
		foreach ($this as $child)
		{
			call_user_func_array(array($child, $method), $arguments);
		}

		return $this;
	}

	public function __get($tag)
	{
		$tags = array();

		foreach ($this as $child)
		{
			$tags = array_merge($tags, $child->getByTag($tag));
		}

		return new static($tags);
	}

	public function __set($tag, $value)
	{
		foreach ($this as $child)
		{
			$child->{$tag} = $value;
		}
	}

	public function next()
	{
		parent::next();

		return $this;
	}
}

?>
