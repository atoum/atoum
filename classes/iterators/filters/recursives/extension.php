<?php

namespace mageekguy\atoum\iterators\filters\recursives;

use
	mageekguy\atoum
;

class extension extends \recursiveFilterIterator
{
	protected $acceptedExtensions = array();

	public function __construct($mixed, array $acceptedExtensions, atoum\factory $factory = null)
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
		$acceptedExtensions = array();

		foreach ($this->acceptedExtensions as $acceptedExtension)
		{
			$acceptedExtensions[] = substr($acceptedExtension, 1);
		}

		return $acceptedExtensions;
	}

	public function accept()
	{
		foreach ($this->acceptedExtensions as $acceptedExtension)
		{
			if (substr(basename((string) $this->getInnerIterator()->current()), - strlen($acceptedExtensions)) === $acceptedExtension)
			{
				return true;
			}
		}

		return false;
	}

	public function createFactory()
	{
		return new atoum\factory();
	}
}

?>
