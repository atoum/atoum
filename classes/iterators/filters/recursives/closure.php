<?php

namespace mageekguy\atoum\iterators\filters\recursives;

class closure extends \recursiveFilterIterator
{
	protected $closures = array();

	public function __construct(\recursiveIterator $iterator, $closure = null)
	{
		parent::__construct($iterator);

		if ($closure !== null)
		{
			foreach ((array) $closure as $c)
				$this->addClosure($c);
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
			if ($closure($this->current(), $this->key(), $this->getInnerIterator()) === false)
			{
				return false;
			}
		}

		return true;
	}

	public function getChildren()
	{
		return new static(
			$this->getInnerIterator()->getChildren(),
			$this->closures
		);
	}
}
