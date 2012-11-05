<?php

namespace mageekguy\atoum\scripts\builder\vcs;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\scripts\builder
;

class svn extends builder\vcs
{
	public function __construct(atoum\adapter $adapter = null)
	{
		parent::__construct($adapter);
	}

	public function getNextRevisions()
	{
		if ($this->repositoryUrl === null)
		{
			throw new exceptions\runtime('Unable to get logs, repository url is undefined');
		}

		if ($this->adapter->extension_loaded('svn') === false)
		{
			throw new exceptions\runtime('PHP extension svn is not available, please install it');
		}

		$this->adapter->svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true);

		$nextRevisions = array();

		foreach ($this->adapter->svn_log($this->repositoryUrl, $this->revision ?: 1, \SVN_REVISION_HEAD) as $log)
		{
			if (is_array($log) && isset($log['rev']) === true && $log['rev'] != $this->revision)
			{
				$nextRevisions[] = $log['rev'];
			}
		}

		return $nextRevisions;
	}

	public function exportRepository()
	{
		if ($this->repositoryUrl === null)
		{
			throw new exceptions\runtime('Unable to export repository, repository url is undefined');
		}

		if ($this->workingDirectory === null)
		{
			throw new exceptions\runtime('Unable to export repository, working directory is undefined');
		}

		if ($this->adapter->extension_loaded('svn') === false)
		{
			throw new exceptions\runtime('PHP extension svn is not available, please install it');
		}

		$this
			->cleanWorkingDirectory()
			->adapter->svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true)
		;

		if ($this->username !== null)
		{
			$this->adapter->svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, $this->username);

			if ($this->password !== null)
			{
				$this->adapter->svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $this->password);
			}
		}

		if ($this->adapter->svn_checkout($this->repositoryUrl, $this->workingDirectory, $this->revision) === false)
		{
			throw new exceptions\runtime('Unable to checkout repository \'' . $this->repositoryUrl . '\' in directory \'' . $this->workingDirectory . '\'');
		}

		return $this;
	}
}
