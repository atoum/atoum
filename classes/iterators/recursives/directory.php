<?php

namespace mageekguy\atoum\iterators\recursives;

use
	mageekguy\atoum,
	mageekguy\atoum\iterators,
	mageekguy\atoum\exceptions
;

class directory implements \iteratorAggregate
{
	protected $path = null;
	protected $innerIterator = null;
	protected $acceptDots = false;
	protected $acceptedExtensions = array('php');

	public function __construct($path = null)
	{
		if ($path !== null)
		{
			$this->setPath($path);
		}
	}

	public function setPath($path)
	{
		$this->path = (string) $path;

		return $this;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getIterator($path = null)
	{
		if ($path !== null)
		{
			$this->setPath($path);
		}
		else if ($this->path === null)
		{
			throw new exceptions\runtime('Path is undefined');
		}

		$factory = $this->createFactory();

		$iterator = $factory->build('recursiveDirectoryIterator', array($this->path));

		if ($this->acceptDots === false)
		{
			$iterator = $factory->build('mageekguy\atoum\iterators\filters\recursives\dot', array($iterator));
		}

		if (sizeof($this->acceptedExtensions) > 0)
		{
			$iterator = $factory->build('mageekguy\atoum\iterators\filters\recursives\extension', array($iterator, $this->acceptedExtensions));
		}

		return $iterator;
	}

	public function setInnerIterator(\recursiveDirectoryIterator $innerIterator)
	{
		$this->innerIterator = $innerIterator;

		return $this;
	}

	public function getInnerIterator()
	{
		return $this->innerIterator;
	}

	public function dotsAreAccepted()
	{
		return $this->acceptDots;
	}

	public function acceptDots()
	{
		$this->acceptDots = true;

		return $this;
	}

	public function refuseDots()
	{
		$this->acceptDots = false;

		return $this;
	}

	public function getAcceptedExtensions()
	{
		return $this->acceptedExtensions;
	}

	public function acceptExtensions(array $extensions)
	{
		array_walk($extensions, function(& $extension) { $extension = self::cleanExtension($extension); });

		$this->acceptedExtensions = $extensions;

		return $this;
	}

	public function acceptAllExtensions()
	{
		return $this->acceptExtensions(array());
	}

	public function refuseExtension($extension)
	{
		$key = array_search(self::cleanExtension($extension), $this->acceptedExtensions);

		if ($key !== false)
		{
			unset($this->acceptedExtensions[$key]);

			$this->acceptedExtensions = array_values($this->acceptedExtensions);
		}

		return $this;
	}

	public function createFactory()
	{
		return new atoum\factory();
	}

	protected static function cleanExtension($extension)
	{
		return trim($extension, '.');
	}
}

?>
