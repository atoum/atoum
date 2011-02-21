<?php

namespace mageekguy\atoum\scripts\svn;

use \mageekguy\atoum;
use \mageekguy\atoum\exceptions;

class builder extends atoum\script
{
	protected $repositoryUrl = null;
	protected $username = null;
	protected $lastRevision = null;
	protected $workingDirectory = null;
	protected $destinationDirectory = null;

	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $locale, $adapter);

		if ($this->adapter->extension_loaded('svn') === false)
		{
			throw new exceptions\runtime('PHP extension svn is not available, please install it');
		}
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

	public function setDestinationDirectory($path)
	{
		$this->destinationDirectory = (string) $path;

		return $this;
	}

	public function getDestinationDirectory()
	{
		return $this->destinationDirectory;
	}

	public function setWorkingDirectory($path)
	{
		$this->workingDirectory = (string) $path;

		return $this;
	}

	public function getWorkingDirectory()
	{
		return $this->workingDirectory;
	}

	public function getLogs()
	{
		if ($this->repositoryUrl === null)
		{
			throw new exceptions\runtime('Unable to get logs, repository url is undefined');
		}

		return $this->adapter->svn_log($this->repositoryUrl, $this->lastRevision, SVN_REVISION_HEAD);
	}

	public function checkout()
	{
		if ($this->repositoryUrl === null)
		{
			throw new exceptions\runtime('Unable to checkout repository, its url is undefined');
		}

		if ($this->workingDirectory === null)
		{
			throw new exceptions\runtime('Unable to checkout repository, working directory is undefined');
		}

		if ($this->adapter->svn_checkout($this->repositoryUrl, $this->workingDirectory, $this->lastRevision === null ? SVN_REVISION_HEAD : $this->lastRevision) === false)
		{
			throw new exceptions\runtime('Unable to checkout repository \'' . $this->repositoryUrl . '\' in working directory \'' . $this->workingDirectory . '\'');
		}

		return $this;
	}

	public function checkUnitTests()
	{
		$this->checkout();

		$descriptors = array(
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		);

		$tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'svnbuilder.txt';

		$php = proc_open($_SERVER['_'] . ' ' . $this->workingDirectory . '/scripts/runner.php -ss ' . $tmpFile . ' -d ' . $this->workingDirectory . '/tests/units/classes', $descriptors, $pipes);

		if ($php !== false)
		{
			$stdOut = stream_get_contents($pipes[1]);
			fclose($pipes[1]);

			$stdErr = stream_get_contents($pipes[2]);
			fclose($pipes[2]);

			$returnValue = proc_close($php);

			if ($stdErr != '')
			{
				throw new exceptions\runtime($stdErr);
			}

			if ($stdOut !== '')
			{
				throw new exceptions\runtime($stdOut);
			}
		}

		/*
		$runnerClassFile = $this->workingDirectory . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'runner.php';

		if ($this->adapter->is_file($runnerClassFile) === true && is_readable($runnerClassFile) === true)
		{
			require_once($this->workingDirectory . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'runner.php');

			$runner = new atoum\runner();

			$unitTestsDirectory = $this->workingDirectory . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'units' . DIRECTORY_SEPARATOR . 'classes';

			if ($this->adapter->is_dir($unitTestsDirectory) === true && $this->adapter->is_readable($unitTestsDirectory) === true)
			{
				foreach (new \recursiveIteratorIterator(new atoum\runners\directory\filter(new \recursiveDirectoryIterator($directory))) as $file)
				{
					require_once($file->getPathname());
				}
			}

			$score = $runner->run()->getScore();
		}
		*/

		return $this;
	}

	public function run(array $arguments = array())
	{
		$this->argumentsParser->addHandler(
			function($script, $argument, $directory) {
				if (sizeof($directory) <= 0 || sizeof($directory) > 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->setWorkingDirectory(current($directory));
			},
			array('-w', '--working-directory')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $directory) {
				if (sizeof($directory) <= 0 || sizeof($directory) > 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->setDestinationDirectory(current($directory));
			},
			array('-d', '--destination-directory')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $url) {
				if (sizeof($url) <= 0 || sizeof($url) > 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->setRepositoryUrl(current($url));
			},
			array('-r', '--repository-url')
		);

		parent::run($arguments);

		$this->checkUnitTests();
	}
}

?>
