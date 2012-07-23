<?php

namespace mageekguy\atoum\iterators\filters\recursives;

use
	mageekguy\atoum
;

class extension extends \recursiveFilterIterator
{
	protected $acceptedExtensions = array();

	public function __construct($mixed, array $acceptedExtensions = array(), atoum\dependencies $dependencies = null)
	{
		if ($mixed instanceof \recursiveIterator)
		{
			parent::__construct($mixed);
		}
		else
		{
			if ($dependencies === null)
			{
				$dependencies = new atoum\dependencies();
			}

			if (isset($dependencies['iterator']) === false)
			{
				$dependencies['iterator'] = new \recursiveDirectoryIterator((string) $mixed);
			}

			if (isset($dependencies['iterator']['directory']) === false)
			{
				$dependencies['iterator']['directory'] = (string) $mixed;
			}

			parent::__construct($dependencies['iterator']());
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
