<?php

namespace mageekguy\atoum\iterators\filters\recursives;

class dot extends \recursiveFilterIterator
{
	public function __construct($mixed, \closure $iteratorFactory = null)
	{
		if ($mixed instanceof \recursiveIterator)
		{
			parent::__construct($mixed);
		}
		else
		{
			parent::__construct(call_user_func($iteratorFactory ?: function($path) { return new \recursiveDirectoryIterator($path); }, (string) $mixed));
		}
	}

	public function accept()
	{
		return (substr($this->getInnerIterator()->current()->getBasename(), 0, 1) != '.');
	}
}
