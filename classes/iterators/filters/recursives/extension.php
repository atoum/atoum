<?php

namespace mageekguy\atoum\iterators\filters\recursives;

use
	mageekguy\atoum
;

class extension extends \recursiveFilterIterator
{
	protected $acceptedExtensions = array();

	public function __construct($mixed, array $acceptedExtensions)
	{
		if ($mixed instanceof \recursiveIterator)
		{
			parent::__construct($mixed);
		}
		else
		{
			parent::__construct($this->createFactory()->build('recursiveDirectoryIterator', array((string) $mixed)));
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

	public function createFactory()
	{
		return new atoum\factory();
	}
}

?>
