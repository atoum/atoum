<?php

namespace mageekguy\atoum\iterators\filters\recursives;

class extension extends \recursiveFilterIterator
{
	protected $acceptedExtensions = array();

	public function __construct($mixed, array $acceptedExtensions = array(), \closure $iteratorFactory = null)
	{
		if ($mixed instanceof \recursiveIterator)
		{
			parent::__construct($mixed);
		}
		else
		{
			parent::__construct(call_user_func($iteratorFactory ?: function($path) { return new \recursiveDirectoryIterator($path); }, (string) $mixed));
		}

		$this->setAcceptedExtensions($acceptedExtensions);
	}

	public function setAcceptedExtensions(array $extensions)
	{
		array_walk($extensions, function(& $extension) { $extension = trim($extension, '.'); });

		$this->acceptedExtensions = $extensions;

		return $this;
	}

	public function getAcceptedExtensions()
	{
		return $this->acceptedExtensions;
	}

	public function accept()
	{
		$path = basename((string) $this->getInnerIterator()->current());

		$extension = pathinfo($path, PATHINFO_EXTENSION);

		return ($extension == '' || in_array($extension, $this->acceptedExtensions) === true);
	}

	public function getChildren()
	{
		return new self($this->getInnerIterator()->getChildren(), $this->acceptedExtensions);
	}
}
