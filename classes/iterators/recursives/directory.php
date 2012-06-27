<?php

namespace mageekguy\atoum\iterators\recursives;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class directory implements \iteratorAggregate
{
	protected $path = null;
	protected $acceptDots = false;
	protected $acceptedExtensions = array('php');

	public function __construct($path = null, atoum\factory $factory = null)
	{
		if ($path !== null)
		{
			$this->setPath($path);
		}

		$this->setFactory($factory ?: new atoum\factory());
	}

	public function setPath($path)
	{
		$this->path = (string) $path;

		return $this;
	}

	public function setFactory(atoum\factory $factory)
	{
		$this->factory = $factory;

		return $this;
	}

	public function getFactory()
	{
		return $this->factory;
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

		$iterator = $this->factory->build('recursiveDirectoryIterator', array($this->path));

		if ($this->acceptDots === false)
		{
			$iterator = $this->factory->build('mageekguy\atoum\iterators\filters\recursives\dot', array($iterator));
		}

		if (sizeof($this->acceptedExtensions) > 0)
		{
			$iterator = $this->factory->build('mageekguy\atoum\iterators\filters\recursives\extension', array($iterator, $this->acceptedExtensions));
		}

		return $iterator;
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
		$this->acceptedExtensions = array();

		foreach ($extensions as $extension)
		{
			$this->acceptedExtensions[] = self::cleanExtension($extension);
		}

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

	protected static function cleanExtension($extension)
	{
		return trim($extension, '.');
	}
}
