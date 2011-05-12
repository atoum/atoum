<?php

namespace mageekguy\atoum\php\tokenizer\iterator;

abstract class value implements \iterator, \countable
{
	public abstract function __toString();
	public abstract function prev();
	public abstract function end();
	public abstract function append(value $value);
	public abstract function getValue();
	public abstract function setParent(value $parent);
	public abstract function getParent();
	public abstract function seek($key);
}

?>
