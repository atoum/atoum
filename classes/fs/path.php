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
	protected $directorySeparator = '';

	public function __construct($value, $directorySeparator = DIRECTORY_SEPARATOR, atoum\adapter $adapter = null)
	{
		$this->setAdapter($adapter);

		$this->directorySeparator = (string) $directorySeparator;

		list($this->drive, $value) = static::getDriveAndComponents($value);

		if ($this->directorySeparator === '\\')
		{
			$value = str_replace('\\', '/', $value);
		}

		$this->components = static::getComponents($value, '/');
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

		if ($this->isRoot() === false)
		{
			$parentDirectory->components = dirname($parentDirectory->components);
		}

		return $parentDirectory;
	}

	public function relativizeFrom(self $reference)
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

	public function resolve()
	{
		$absoluteComponents = $this->adapter->realpath($this->drive . $this->components);

		if ($absoluteComponents === false)
		{
			throw new exceptions\runtime('Unable to resolve \'' . $this . '\'');
		}

		$absolutePath = clone $this;

		list($absolutePath->drive, $absolutePath->components) = static::getDriveAndComponents($absoluteComponents);

		return $absolutePath;
	}

	public function isSubPathOf(self $path)
	{
		$absoluteThis = $this->resolve();
		$absolutePath = $path->resolve();

		return ($absoluteThis->components !== $absolutePath->components && ($absolutePath->isRoot() === true || strpos($absoluteThis->components, $absolutePath->components . '/') === 0));
	}

	public function isNotSubPathOf(self $path)
	{
		return ($this->isSubPathOf($path) === false);
	}

	public function isRoot()
	{
		return static::pathIsRoot($this->resolve()->components);
	}

	public function isAbsolute()
	{
		return static::pathIsAbsolute($this->components);
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

		return $path;
	}

	protected static function getDriveAndComponents($value)
	{
		$drive = null;

		if (preg_match('/^[a-z]:/i', $value, $matches) == true)
		{
			$drive = $matches[0];
			$value = substr($value, 2);
		}

		return array($drive, $value);
	}
}
