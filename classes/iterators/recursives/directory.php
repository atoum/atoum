<?php

namespace mageekguy\atoum\iterators\recursives;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\iterators\filters
;

class directory implements \iteratorAggregate
{
	protected $path = null;
	protected $acceptDots = false;
	protected $acceptedExtensions = array('php');
	protected $dependencies = null;

	public function __construct($path = null, atoum\dependencies $dependencies = null)
	{
		if ($path !== null)
		{
			$this->setPath($path);
		}

		$this->setDependencies($dependencies ?: new atoum\dependencies());
	}

	public function setPath($path)
	{
		$this->path = (string) $path;

		return $this;
	}

	public function setDependencies(atoum\dependencies $dependencies)
	{
		$this->dependencies = $dependencies;

		if (isset($this->dependencies['iterator']) === false)
		{
			$this->dependencies['iterator'] = function($dependencies) { return new \recursiveDirectoryIterator($dependencies['directory']()); };
		}

		if (isset($this->dependencies['filters\dot']) === false)
		{
			$this->dependencies['filters\dot'] = function($dependencies) { return new filters\recursives\dot($dependencies['iterator']()); };
		}

		if (isset($this->dependencies['filters\extension']) === false)
		{
			$this->dependencies['filters\extension'] = function($dependencies) { return new filters\recursives\extension($dependencies['iterator'](), $dependencies['extensions']()); };
		}

		return $this;
	}

	public function getDependencies()
	{
		return $this->dependencies;
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

		$iterator = $this->dependencies['iterator'](array('directory' => $this->path));

		if ($this->acceptDots === false)
		{
			$iterator = $this->dependencies['filters\dot'](array('iterator' => $iterator));
		}

		if (sizeof($this->acceptedExtensions) > 0)
		{
			$iterator = $this->dependencies['filters\extension'](array('iterator' => $iterator, 'extensions' => $this->acceptedExtensions));
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
