<?php

namespace mageekguy\atoum\scripts\builder\vcs;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions,
	\mageekguy\atoum\scripts\builder
;

class svn extends builder\vcs implements atoum\adapter\aggregator
{
	protected $directoryIteratorInjector = null;

	public function setDirectoryIteratorInjector(\closure $directoryIteratorInjector)
	{
		$closure = new \reflectionMethod($directoryIteratorInjector, '__invoke');

		if ($closure->getNumberOfParameters() != 1)
		{
			throw new exceptions\logic('Directory iterator injector must take one argument');
		}

		$this->directoryIteratorInjector = $directoryIteratorInjector;

		return $this;
	}

	public function getDirectoryIterator($directory)
	{
		if ($this->directoryIteratorInjector === null)
		{
			$this->setDirectoryIteratorInjector(function($directory) { return new \directoryIterator($directory); });
		}

		return $this->directoryIteratorInjector->__invoke($directory);
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
			if (is_array($log) && isset($log['rev']) === true && $log['rev'] != $this->revision)
			{
				$nextRevisions[] = $log['rev'];
			}
		}

		return $nextRevisions;
	}

	public function exportRepository($directory)
	{
		if ($this->repositoryUrl === null)
		{
			throw new exceptions\runtime('Unable to export repository, repository url is undefined');
		}

		$this
//			->deleteContents($path)
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

		if ($this->adapter->svn_checkout($this->repositoryUrl, $directory, $this->revision) === false)
		{
			throw new exceptions\runtime('Unable to checkout repository \'' . $this->repositoryUrl . '\' in directory \'' . $directory . '\'');
		}

		return $this;
	}

	public function deleteDirectoryContents($path)
	{
		foreach ($this->getDirectoryIterator($path) as $inode)
		{
			$path = $inode->getPathname();

			if ($inode->isDot() === false)
			{
				if ($inode->isDir() === false)
				{
					$this->adapter->unlink($path);
				}
				else
				{
					$this->deleteDirectoryContents($path);
					$this->adapter->rmdir($path);
				}
			}
		}

		return $this;
	}
}
