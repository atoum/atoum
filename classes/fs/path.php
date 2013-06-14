<?php

namespace mageekguy\atoum\fs;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class path
{
	protected $adapter = null;
	protected $drive = '';
	protected $components = '';
	protected $directorySeparator = DIRECTORY_SEPARATOR;

	public function __construct($value, $directorySeparator = null, atoum\adapter $adapter = null)
	{
		$this->directorySeparator = (string) $directorySeparator ?: DIRECTORY_SEPARATOR;

		$this
			->setDriveAndComponents($value)
			->setAdapter($adapter)
		;
	}

	public function __toString()
	{
		$components = $this->components;

		if ($this->directorySeparator === '\\')
		{
			$components = str_replace('/', '\\', $components);
		}

		return $this->drive . $components;
	}

	public function setAdapter(atoum\adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new atoum\adapter();

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function getDirectorySeparator()
	{
		return $this->directorySeparator;
	}

	public function getParentDirectory()
	{
		$parentDirectory = clone $this;
		$parentDirectory->components = dirname($parentDirectory->components);

		return $parentDirectory;
	}

	public function getRealParentDirectory()
	{
		$realParentDirectory = clone $this;
		$realParentDirectory = $realParentDirectory->absolutize();
		$realParentDirectory = $realParentDirectory->getParentDirectory();

		while ($realParentDirectory->exists() === false && self::pathIsRoot($realParentDirectory) === false)
		{
			$realParentDirectory = $realParentDirectory->getParentDirectory();
		}

		if ($realParentDirectory->exists() === false)
		{
			throw new exceptions\runtime('Unable to find real parent directory for \'' . $this . '\'');
		}

		return $realParentDirectory;
	}

	public function relativizeFrom(path $reference)
	{
		$absolutePath = $this->resolve();
		$absoluteReference = $reference->resolve();

		switch (true)
		{
			case $absoluteReference->components === '/':
				$absolutePath->components = '.' . $absolutePath->components;
				break;

			case $absolutePath->components === $absoluteReference->components:
				$absolutePath->components = '.';
				break;

			case $absolutePath->isSubPathOf($absoluteReference):
				$absolutePath->components = './' . substr($absolutePath->components, strlen($absoluteReference->components) + 1);
				break;

			default:
				$relativePath = '';

				while ($absolutePath->isNotSubPathOf($absoluteReference))
				{
					$relativePath .= '../';

					$absoluteReference = $absoluteReference->getParentDirectory();
				}

				$absolutePath->components = static::getComponents($relativePath, '/') . '/' . ltrim(substr($absolutePath->components, strlen($absoluteReference->components)), '/');
		}

		return $absolutePath;
	}

	public function exists()
	{
		return ($this->adapter->file_exists((string) $this) === true);
	}

	public function resolve()
	{
		$resolvedPath = $this;

		if ($resolvedPath->isAbsolute() === false)
		{
			$resolvedPath = $resolvedPath->absolutize();
		}

		$components = array();

		foreach (explode('/', ltrim($resolvedPath->components, '/')) as $component)
		{
			switch ($component)
			{
				case '.':
					break;

				case '..':
					if (sizeof($components) <= 0)
					{
						throw new exceptions\runtime('Unable to resolve path \'' . $this . '\'');
					}

					array_pop($components);
					break;

				default:
					$components[] = $component;
			}
		}

		$resolvedPath->components = '/' . join('/', $components);

		return $resolvedPath;
	}

	public function isSubPathOf(path $path)
	{
		$absoluteThis = $this->resolve();
		$absolutePath = $path->resolve();

		return ($absoluteThis->components !== $absolutePath->components && ($absolutePath->isRoot() === true || strpos($absoluteThis->components, $absolutePath->components . '/') === 0));
	}

	public function isNotSubPathOf(path $path)
	{
		return ($this->isSubPathOf($path) === false);
	}

	public function isRoot()
	{
		return static::pathIsRoot($this->resolve()->components);
	}

	public function absolutize()
	{
		$absolutePath = clone $this;

		if ($absolutePath->isAbsolute() === false)
		{
			$absolutePath->setDriveAndComponents($this->adapter->getcwd() . DIRECTORY_SEPARATOR . $absolutePath->components);
		}

		return $absolutePath;
	}

	public function isAbsolute()
	{
		return static::pathIsAbsolute($this->components);
	}

	protected function setDriveAndComponents($value)
	{
		$drive = null;

		if (preg_match('/^[a-z]:/i', $value, $matches) == true)
		{
			$drive = $matches[0];
			$value = substr($value, 2);
		}

		if ($this->directorySeparator === '\\')
		{
			$value = str_replace('\\', '/', $value);
		}

		$this->drive = $drive;
		$this->components = self::getComponents($value, '/');

		return $this;
	}

	protected static function pathIsRoot($path)
	{
		return ($path === '/');
	}

	protected static function pathIsAbsolute($path)
	{
		return (substr($path, 0, 1) === '/');
	}

	protected static function getComponents($path, $directorySeparator)
	{
		if (static::pathIsRoot($path) === false)
		{
			$path = rtrim($path, $directorySeparator);
		}

		$path = preg_replace('#/{2,}#', '/', $path);

		return $path;
	}
}
