<?php

namespace mageekguy\atoum\template;

class iterator extends \ArrayIterator
{
	public function __call($function, $arguments)
	{
		foreach ($this as $child)
		{
			call_user_func_array(array($child, $function), $arguments);
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

		return new self($tags);
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
