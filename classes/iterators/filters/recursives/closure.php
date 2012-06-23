<?php

namespace mageekguy\atoum\iterators\filters\recursives;

class closure extends \recursiveFilterIterator
{
	protected $closures = array();

	public function __construct(\iterator $iterator, \closure $closure = null)
	{
		parent::__construct($iterator);

		if ($closure !== null)
		{
			$this->addClosure($closure);
		}
	}

	public function addClosure(\closure $closure)
	{
		$this->closures[] = $closure;

		return $this;
	}

	public function getClosures()
	{
		return $this->closures;
	}

	public function accept()
	{
		foreach ($this->closures as $closure)
		{
			if ($closure($this->current(), $this->key()) === false)
			{
				return false;
			}
		}

		return true;
	}
}
