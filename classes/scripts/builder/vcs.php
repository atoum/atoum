<?php

namespace mageekguy\atoum\scripts\builder;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

abstract class vcs
{
	protected $adapter = null;
	protected $repositoryUrl = null;
	protected $revision = null;
	protected $username = null;
	protected $password = null;
	protected $workingDirectory = null;

	public function __construct(atoum\adapter $adapter = null)
	{
		$this->setAdapter($adapter ?: new atoum\adapter());
	}

	public function setAdapter(atoum\adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setWorkingDirectory($workingDirectory)
	{
		$this->workingDirectory = (string) $workingDirectory;

		return $this;
	}

	public function getWorkingDirectory()
	{
		return $this->workingDirectory;
	}

	public function getWorkingDirectoryIterator()
	{
		if ($this->workingDirectory === null)
		{
			throw new exceptions\runtime('Unable to clean working directory because it is undefined');
		}

		return new \recursiveIteratorIterator(new \recursiveDirectoryIterator($this->workingDirectory, \filesystemIterator::KEY_AS_PATHNAME | \filesystemIterator::CURRENT_AS_FILEINFO | \filesystemIterator::SKIP_DOTS), \recursiveIteratorIterator::CHILD_FIRST);
	}

	public function setRepositoryUrl($url)
	{
		$this->repositoryUrl = (string) $url;

		return $this;
	}

	public function getRepositoryUrl()
	{
		return $this->repositoryUrl;
	}

	public function setRevision($revisionNumber)
	{
		$this->revision = (int) $revisionNumber;

		return $this;
	}

	public function getRevision()
	{
		return $this->revision;
	}

	public function setUsername($username)
	{
		$this->username = (string) $username;

		return $this;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function setPassword($password)
	{
		$this->password = (string) $password;

		return $this;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public abstract function getNextRevisions();

	public abstract function exportRepository();

	public function cleanWorkingDirectory()
	{
		foreach ($this->getWorkingDirectoryIterator() as $inode)
		{
			if ($inode->isDir() === false)
			{
				$this->adapter->unlink($inode->getPathname());
			}
			else if (($pathname = $inode->getPathname()) !== $this->workingDirectory)
			{
				$this->adapter->rmdir($pathname);
			}
		}

		return $this;
	}
}
