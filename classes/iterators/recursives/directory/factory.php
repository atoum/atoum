<?php

namespace mageekguy\atoum\iterators\recursives\directory;

use
	mageekguy\atoum\iterators\filters
;

class factory implements \iteratorAggregate
{
	protected $dotFilterFactory = null;
	protected $iteratorFactory = null;
	protected $acceptDots = false;
	protected $extensionFilterFactory = null;
	protected $acceptedExtensions = array('php');

	public function __construct(\closure $iteratorFactory = null, \closure $dotFilterFactory = null, \closure $extensionFilterFactory = null)
	{
		$this
			->setIteratorFactory($iteratorFactory)
			->setDotFilterFactory($dotFilterFactory)
			->setExtensionFilterFactory($extensionFilterFactory)
		;
	}

	public function setIteratorFactory(\closure $factory = null)
	{
		$this->iteratorFactory = $factory ?: function($path) { return new \recursiveDirectoryIterator($path); };

		return $this;
	}

	public function getIteratorFactory()
	{
		return $this->iteratorFactory;
	}

	public function setDotFilterFactory(\closure $factory = null)
	{
		$this->dotFilterFactory = $factory ?: function($iterator) { return new filters\recursives\dot($iterator); };

		return $this;
	}

	public function getDotFilterFactory()
	{
		return $this->dotFilterFactory;
	}

	public function setExtensionFilterFactory(\closure $factory = null)
	{
		$this->extensionFilterFactory = $factory ?: function($iterator, $extensions) { return new filters\recursives\extension($iterator, $extensions); };

		return $this;
	}

	public function getExtensionFilterFactory()
	{
		return $this->extensionFilterFactory;
	}

	public function getIterator($path)
	{
		$iterator = call_user_func($this->iteratorFactory, $path);

		if ($this->acceptDots === false)
		{
			$iterator = call_user_func($this->dotFilterFactory, $iterator);
		}

		if (sizeof($this->acceptedExtensions) > 0)
		{
			$iterator = call_user_func($this->extensionFilterFactory, $iterator, $this->acceptedExtensions);
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
