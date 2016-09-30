<?php

namespace mageekguy\atoum\scripts\tagger;

use
	mageekguy\atoum,
	mageekguy\atoum\adapter,
	mageekguy\atoum\exceptions
;

class engine
{
	const defaultVersionPattern = '/\$Rev: ([^ %]+) \$/';
	const defaultChangelogName = 'CHANGELOG.md';
	const defaultChangelogHeader = '# `dev-master`';

	protected $adapter = null;
	protected $version = null;
	protected $versionPattern = null;
	protected $changelogName = null;
	protected $changelogHeader = null;
	protected $srcDirectory = null;
	protected $srcIteratorInjector = null;
	protected $destinationDirectory = null;

	public function __construct(atoum\adapter $adapter = null)
	{
		if ($adapter === null)
		{
			$adapter = new adapter();
		}

		$this
			->setAdapter($adapter)
			->setVersionPattern(static::defaultVersionPattern)
			->setChangelogName(static::defaultChangelogName)
			->setChangelogHeader(static::defaultChangelogHeader)
		;
	}

	public function setAdapter(adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function getVersion()
	{
		return $this->version;
	}

	public function setVersion($version)
	{
		$this->version = (string) $version;

		return $this;
	}

	public function getVersionPattern()
	{
		return $this->versionPattern;
	}

	public function setVersionPattern($pattern)
	{
		$this->versionPattern = (string) $pattern;

		return $this;
	}

	public function setChangelogName($name)
	{
		$this->changelogName = (string) $name;

		return $this;
	}

	public function setChangelogHeader($header)
	{
		$this->changelogHeader = (string) $header;

		return $this;
	}

	public function getSrcDirectory()
	{
		return $this->srcDirectory;
	}

	public function setSrcDirectory($directory)
	{
		$this->srcDirectory = rtrim((string) $directory, \DIRECTORY_SEPARATOR);

		if ($this->destinationDirectory === null)
		{
			$this->destinationDirectory = $this->srcDirectory;
		}

		return $this;
	}

	public function getDestinationDirectory()
	{
		return $this->destinationDirectory;
	}

	public function setDestinationDirectory($directory)
	{
		$this->destinationDirectory = rtrim((string) $directory, \DIRECTORY_SEPARATOR);

		return $this;
	}

	public function setSrcIteratorInjector(\closure $srcIteratorInjector)
	{
		$closure = new \reflectionMethod($srcIteratorInjector, '__invoke');

		if ($closure->getNumberOfParameters() != 1)
		{
			throw new exceptions\logic('Src iterator injector must take one argument');
		}

		$this->srcIteratorInjector = $srcIteratorInjector;

		return $this;
	}

	public function getSrcIterator()
	{
		if ($this->srcDirectory === null)
		{
			throw new exceptions\logic('Unable to get files iterator, source directory is undefined');
		}

		if ($this->srcIteratorInjector === null)
		{
			$this->setSrcIteratorInjector(function($directory) { return new \recursiveIteratorIterator(new atoum\iterators\filters\recursives\dot($directory)); });
		}

		return $this->srcIteratorInjector->__invoke($this->srcDirectory);
	}

	public function tagVersion()
	{
		if ($this->srcDirectory === null)
		{
			throw new exceptions\logic('Unable to tag, src directory is undefined');
		}

		if ($this->version === null)
		{
			throw new exceptions\logic('Unable to tag, version is undefined');
		}

		$srcIterator = $this->getSrcIterator();

		if ($srcIterator instanceof \iterator === false)
		{
			throw new exceptions\logic('Unable to tag, src iterator injector does not return an iterator');
		}

		foreach ($srcIterator as $path)
		{
			$fileContents = @$this->adapter->file_get_contents($path);

			if ($fileContents === false)
			{
				throw new exceptions\runtime('Unable to tag, path \'' . $path . '\' is not readable');
			}

			$path = ($this->destinationDirectory == $this->srcDirectory ? $path : $this->destinationDirectory . \DIRECTORY_SEPARATOR . substr($path, strlen($this->srcDirectory) + 1));

			$directory = $this->adapter->dirname($path);

			if ($this->adapter->is_dir($directory) === false)
			{
				$this->adapter->mkdir($directory, 0777, true);
			}

			if ($this->adapter->file_put_contents($path, preg_replace($this->versionPattern, $this->version, $fileContents), \LOCK_EX) === false)
			{
				throw new exceptions\runtime('Unable to tag, path \'' . $path . '\' is not writable');
			}
		}

		return $this;
	}

	public function tagChangelog()
	{
		$changelogPath = $this->srcDirectory . DIRECTORY_SEPARATOR . $this->changelogName;

		if ($this->adapter->file_exists($changelogPath) === true)
		{
			$changelogContents = @$this->adapter->file_get_contents($changelogPath);

			if ($changelogContents === false)
			{
				throw new exceptions\runtime('Unable to tag, path \'' . $changelogPath . '\' is not readable');
			}

			if (preg_match($this->versionPattern, $this->version, $matches))
			{
				$changelogContents = preg_replace('/^' . preg_quote($this->changelogHeader, '/') . '$/m', '# ' . (isset($matches[1]) ? $matches[1] : $this->version) . ' - ' . $this->adapter->date('Y-m-d'), $changelogContents);
				
				if ($this->adapter->file_put_contents($changelogPath, $this->changelogHeader . PHP_EOL . PHP_EOL . $changelogContents, \LOCK_EX) === false)
				{
					throw new exceptions\runtime('Unable to tag, path \'' . $changelogPath . '\' is not writable');
				}
			}

		}

		return $this;
	}
}
