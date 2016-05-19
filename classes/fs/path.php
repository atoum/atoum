<?php

namespace mageekguy\atoum\fs;

use
	mageekguy\atoum\fs\path\exception
;

class path
{
	protected $drive = '';
	protected $components = '';
	protected $directorySeparator = DIRECTORY_SEPARATOR;

	public function __construct($value, $directorySeparator = null)
	{
		$this
			->setDriveAndComponents($value)
			->directorySeparator = (string) $directorySeparator ?: DIRECTORY_SEPARATOR
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

	public function getDirectorySeparator()
	{
		return $this->directorySeparator;
	}

	public function relativizeFrom(path $reference)
	{
		$this->resolve();

		$resolvedReferencePath = $reference->getResolvedPath();

		$this->drive = null;

		switch (true)
		{
			case $this->components === '/':
				$this->components = '.' . $this->components;
				break;

			case $this->components === $resolvedReferencePath->components:
				$this->components = '.';
				break;

			case $this->isSubPathOf($resolvedReferencePath):
				$this->components = './' . ltrim(substr($this->components, strlen($resolvedReferencePath->components)), '/');
				break;

			default:
				$relativePath = '';

				while ($this->isNotSubPathOf($resolvedReferencePath))
				{
					$relativePath .= '../';

					$resolvedReferencePath = $resolvedReferencePath->getParentDirectoryPath();
				}

				$this->components = static::getComponents($relativePath) . '/' . ltrim(substr($this->components, strlen($resolvedReferencePath->components)), '/');
		}

		return $this;
	}

	public function exists()
	{
		return (file_exists((string) $this) === true);
	}

	public function resolve()
	{
		if ($this->isAbsolute() === false)
		{
			$this->absolutize();
		}

		$components = array();

		foreach (explode('/', ltrim($this->components, '/')) as $component)
		{
			switch ($component)
			{
				case '.':
					break;

				case '..':
					if (sizeof($components) <= 0)
					{
						throw new exception('Unable to resolve path \'' . $this . '\'');
					}

					array_pop($components);
					break;

				default:
					$components[] = $component;
			}
		}

		$this->components = '/' . join('/', $components);

		return $this;
	}

	public function isSubPathOf(path $path)
	{
		$this->resolve();

		$resolvedPath = $path->getResolvedPath();

		return ($this->components !== $resolvedPath->components && ($resolvedPath->isRoot() === true || strpos($this->components, $resolvedPath->components . '/') === 0));
	}

	public function isNotSubPathOf(path $path)
	{
		return ($this->isSubPathOf($path) === false);
	}

	public function isRoot()
	{
		return static::pathIsRoot($this->getResolvedPath()->components);
	}

	public function isAbsolute()
	{
		return static::pathIsAbsolute($this->components);
	}

	public function absolutize()
	{
		if ($this->isAbsolute() === false)
		{
			$this->setDriveAndComponents(getcwd() . DIRECTORY_SEPARATOR . $this->components);
		}

		return $this;
	}

	public function getRealPath()
	{
		$absolutePath = $this->getAbsolutePath();

		$files = '';
		$realPath = realpath((string) $absolutePath);

		if ($realPath === false)
		{
			while ($realPath === false && $absolutePath->isRoot() === false)
			{
				$files = '/' . basename((string) $absolutePath) . $files;
				$absolutePath = $absolutePath->getParentDirectoryPath();
				$realPath = realpath((string) $absolutePath);
			}
		}

		if ($realPath === false)
		{
			throw new exception('Unable to get real path for \'' . $this . '\'');
		}

		return $absolutePath->setDriveAndComponents($realPath . $files);
	}

	public function getParentDirectoryPath()
	{
		$parentDirectory = clone $this;
		$parentDirectory->components = self::getComponents(dirname($parentDirectory->components));

		return $parentDirectory;
	}

	public function getRealParentDirectoryPath()
	{
		$realParentDirectoryPath = $this->getParentDirectoryPath();

		while ($realParentDirectoryPath->exists() === false && $realParentDirectoryPath->isRoot() === false)
		{
			$realParentDirectoryPath = $realParentDirectoryPath->getParentDirectoryPath();
		}

		if ($realParentDirectoryPath->exists() === false)
		{
			throw new exception('Unable to find real parent directory for \'' . $this . '\'');
		}

		return $realParentDirectoryPath;
	}

	public function getRelativePathFrom(path $reference)
	{
		$clone = clone $this;

		return $clone->relativizeFrom($reference);
	}

	public function getResolvedPath()
	{
		$clone = clone $this;

		return $clone->resolve();
	}

	public function getAbsolutePath()
	{
		$clone = clone $this;

		return $clone->absolutize();
	}

	public function createParentDirectory()
	{
		$parentDirectory = $this->getParentDirectoryPath();

		if (file_exists((string) $parentDirectory) === false && @mkdir($parentDirectory, 0777, true) === false)
		{
			throw new exception('Unable to create directory \'' . $parentDirectory . '\'');
		}

		return $this;
	}

	public function putContents($data)
	{
		if (@file_put_contents($this->createParentDirectory(), $data) === false)
		{
			throw new exception('Unable to put data \'' . $data . '\' in file \'' . $this . '\'');
		}

		return $this;
	}

	protected function setDriveAndComponents($value)
	{
		$drive = null;

		if (preg_match('#^[a-z]:#i', $value, $matches) == true)
		{
			$drive = $matches[0];
			$value = substr($value, 2);
		}

		$this->drive = $drive;
		$this->components = self::getComponents($value);

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

	protected static function getComponents($path)
	{
		$path = str_replace('\\', '/', $path);

		if (static::pathIsRoot($path) === false)
		{
			$path = rtrim($path, '/');
		}

		return preg_replace('#/{2,}#', '/', $path);
	}
}
