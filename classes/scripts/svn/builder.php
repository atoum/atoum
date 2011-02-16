<?php

namespace mageekguy\atoum\scripts\svn;

use \mageekguy\atoum;
use \mageekguy\atoum\exceptions;

class builder extends atoum\script
{
	protected $repositoryUrl = null;
	protected $username = null;
	protected $lastRevision = null;

	public function setRepositoryUrl($url)
	{
		$this->repositoryUrl = (string) $url;

		return $this;
	}

	public function getRepositoryUrl()
	{
		return $this->repositoryUrl;
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

	public function setLastRevision($revisionNumber)
	{
		$this->lastRevision = (int) $revisionNumber;

		return $this;
	}

	public function getLastRevision()
	{
		return $this->lastRevision;
	}

	public function getSvnLogs()
	{
		if ($this->repositoryUrl === null)
		{
			throw new exceptions\runtime('Repository url is undefined');
		}

		return $this->adapter->svn_log($this->repositoryUrl, $this->lastRevision, SVN_REVISION_HEAD);
	}

	public function run(array $arguments = array())
	{
		parent::run($arguments);
	}
}

?>
