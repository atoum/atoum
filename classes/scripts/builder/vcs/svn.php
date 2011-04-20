<?php

namespace mageekguy\atoum\scripts\builder\vcs;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

class svn implements atoum\adapter\aggregator
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

	public function getNextRevisions()
	{
		if ($this->repositoryUrl === null)
		{
			throw new exceptions\runtime('Unable to get logs, repository url is undefined');
		}

		$this->adapter->svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true);

		$nextRevisions = array();

		foreach ($this->adapter->svn_log($this->repositoryUrl, $this->revision, \SVN_REVISION_HEAD) as $log)
		{
			if (is_array($log) && isset($log['rev']) === true)
			{
				$nextRevisions[] = $log['rev'];
			}
		}

		return $nextRevisions;
	}

	public function checkoutInDirectory($directory)
	{
		if ($this->repositoryUrl === null)
		{
			throw new exceptions\runtime('Unable to checkout repository, its url is undefined');
		}

		$this->adapter->svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true);

		if ($this->username !== null)
		{
			$this->adapter->svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, $this->username);

			if ($this->password !== null)
			{
				$this->adapter->svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $this->password);
			}
		}

		if ($this->adapter->svn_checkout($this->repositoryUrl, $directory, $this->revision) === false)
		{
			throw new exceptions\runtime('Unable to checkout repository \'' . $this->repositoryUrl . '\' in directory \'' . $directory . '\'');
		}

		return $this;
	}
}
