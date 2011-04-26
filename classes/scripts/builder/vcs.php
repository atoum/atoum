<?php

namespace mageekguy\atoum\scripts\builder;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

abstract class vcs implements atoum\adapter\aggregator
{
	protected $adapter = null;
	protected $repositoryUrl = null;
	protected $revision = null;
	protected $username = null;
	protected $password = null;

	public function __construct(atoum\adapter $adapter = null)
	{
		if ($adapter === null)
		{
			$adapter = new atoum\adapter();
		}

		$this->setAdapter($adapter);

		if ($this->adapter->extension_loaded('svn') === false)
		{
			throw new exceptions\runtime('PHP extension svn is not available, please install it');
		}
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

	public abstract function exportRepository($directory);
}
