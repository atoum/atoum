<?php

namespace mageekguy\atoum\scripts\svn;

use \mageekguy\atoum;
use \mageekguy\atoum\exceptions;

class builder extends atoum\script
{
	protected $superglobals = null;
	protected $repositoryUrl = null;
	protected $username = null;
	protected $password = null;
	protected $revision = null;
	protected $workingDirectory = null;
	protected $destinationDirectory = null;
	protected $scoreDirectory = null;
	protected $errorsDirectory = null;
	protected $revisionFile = null;
	protected $buildPhar = true;

	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $locale, $adapter);

		if ($this->adapter->extension_loaded('svn') === false)
		{
			throw new exceptions\runtime('PHP extension svn is not available, please install it');
		}

		$this->setSuperglobals(new atoum\superglobals());
	}

	public function buildPhar($boolean)
	{
		$this->buildPhar = $boolean == true;

		return $this;
	}

	public function pharWillBeBuilt()
	{
		return $this->buildPhar;
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

	public function setSuperglobals(atoum\superglobals $superglobals)
	{
		$this->superglobals = $superglobals;

		return $this;
	}

	public function getSuperglobals()
	{
		return $this->superglobals;
	}

	public function setScoreDirectory($path)
	{
		$this->scoreDirectory = (string) $path;

		return $this;
	}

	public function getScoreDirectory()
	{
		return $this->scoreDirectory;
	}

	public function setErrorsDirectory($path)
	{
		$this->errorsDirectory = (string) $path;

		return $this;
	}

	public function getErrorsDirectory()
	{
		return $this->errorsDirectory;
	}

	public function setRevision($revisionNumber)
	{
		$this->revision = (int) $revisionNumber;

		return $this;
	}

	public function setRevisionFile($path)
	{
		$this->revisionFile = (string) $path;

		return $this;
	}

	public function getRevisionFile()
	{
		return $this->revisionFile;
	}

	public function getRevision()
	{
		return $this->revision;
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

		return $this->adapter->svn_log($this->repositoryUrl, $this->getRevision(), \SVN_REVISION_HEAD);
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

		$revision = $this->getRevision();

		if ($revision === null)
		{
			$revisions = $this->getNextRevisionNumbers();

			if (sizeof($revisions) <= 0)
			{
				throw new exceptions\runtime('Unable to retrieve last revision number from repository \'' . $this->repositoryUrl . '\'');
			}

			$this->setRevision(end($revisions));
		}

		if ($this->username !== null)
		{
			$this->adapter->svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, $this->username);

			if ($this->password !== null)
			{
				$this->adapter->svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $this->password);
			}
		}

		if ($this->adapter->svn_checkout($this->repositoryUrl, $this->workingDirectory, $this->getRevision()) === false)
		{
			throw new exceptions\runtime('Unable to checkout repository \'' . $this->repositoryUrl . '\' in working directory \'' . $this->workingDirectory . '\'');
		}

		return $this;
	}

	public function checkUnitTests()
	{
		$noFail = false;

		$this->checkout();

		$descriptors = array(
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		);

		$scoreFile = $this->scoreDirectory === null ? $this->adapter->tempnam($this->adapter->sys_get_temp_dir(), '') : $this->scoreDirectory . DIRECTORY_SEPARATOR . $this->getRevision();

		$command = $this->superglobals->_SERVER['_'] . ' ' . $this->workingDirectory . '/scripts/runner.php -ncc -nr -sf ' . $scoreFile . ' -d ' . $this->workingDirectory . '/tests/units/classes';

		$php = $this->adapter->invoke('proc_open', array($command, $descriptors, & $pipes));

		if ($php === false)
		{
			throw new exceptions\runtime('Unable to execute \'' . $command . '\'');
		}

		$stdOut = $this->adapter->stream_get_contents($pipes[1]);
		$this->adapter->fclose($pipes[1]);

		$stdErr = $this->adapter->stream_get_contents($pipes[2]);
		$this->adapter->fclose($pipes[2]);

		$this->adapter->proc_close($php);

		if ($stdOut != '')
		{
			$this->writeErrorInErrorsDirectory($stdOut);
		}

		if ($stdErr != '')
		{
			$this->writeErrorInErrorsDirectory($stdErr);
		}

		$score = @$this->adapter->file_get_contents($scoreFile);

		if ($score === false)
		{
			throw new exceptions\runtime('Unable to read score from file \'' . $scoreFile . '\'');
		}

		$score = $this->adapter->unserialize($score);

		if ($score === false)
		{
			throw new exceptions\runtime('Unable to unserialize score from file \'' . $scoreFile . '\'');
		}

		if ($score instanceof atoum\score === false)
		{
			throw new exceptions\runtime('Contents of file \'' . $scoreFile . '\' is not a score');
		}

		if ($this->scoreDirectory === null)
		{
			if ($this->adapter->unlink($scoreFile) === false)
			{
				throw new exceptions\runtime('Unable to delete score file \'' . $scoreFile . '\'');
			}
		}

		$noFail = $score->getFailNumber() === 0 && $score->getExceptionNumber() === 0 && $score->getErrorNumber() === 0;

		return $noFail;
	}

	public function build()
	{
		if ($this->destinationDirectory === null)
		{
			throw new exceptions\runtime('Unable to build phar, destination directory is undefined');
		}

		$pharBuilt = false;

		if ($this->revisionFile !== null)
		{
			$revision = @$this->adapter->file_get_contents($this->revisionFile);

			if ($this->adapter->is_numeric($revision) === true)
			{
				$this->setRevision($revision);
			}
		}

		$revisions = $this->getNextRevisionNumbers();

		if (sizeof($revisions) > 0)
		{
			$revision = end($revisions);

			$descriptors = array(
				2 => array('pipe', 'w')
			);

			while (sizeof($revisions) > 0)
			{
				$this->setRevision(array_shift($revisions));

				if ($this->checkUnitTests() === true)
				{
					$command = $this->superglobals->_SERVER['_'] . ' -d phar.readonly=0 -f ' . $this->workingDirectory . '/scripts/phar/generator.php -- -d ' . $this->destinationDirectory;

					$php = $this->adapter->invoke('proc_open', array($this->superglobals->_SERVER['_'] . ' -d phar.readonly=0 -f ' . $this->workingDirectory . '/scripts/phar/generator.php -- -d ' . $this->destinationDirectory, $descriptors, & $pipes));

					if ($php === false)
					{
						throw new exceptions\runtime('Unable to execute \'' . $command . '\'');
					}

					$stdErr = $this->adapter->stream_get_contents($pipes[2]);
					fclose($pipes[2]);

					$this->adapter->proc_close($php);

					if ($stdErr == '')
					{
						$pharBuilt = true;
					}
					else
					{
						$this->writeErrorInErrorsDirectory($stdErr);
					}
				}

				$revisions = $this->getNextRevisionNumbers($revisions);

				if (sizeof($revisions) > 0)
				{
					$revision = end($revisions);
				}
			}

			if ($this->revisionFile !== null && $this->adapter->file_put_contents($this->revisionFile, $revision, \LOCK_EX) === false)
			{
				throw new exceptions\runtime('Unable to save last revision in file \'' . $this->revisionFile . '\'');
			}
		}

		return $pharBuilt;
	}

	public function run(array $arguments = array())
	{
		$this->argumentsParser->addHandler(
			function($script, $argument, $values) {
				if (sizeof($values) != 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script
					->buildPhar(false)
					->help()
				;
			},
			array('-h', '--help')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $directory) {
				if (sizeof($directory) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->setWorkingDirectory(current($directory));
			},
			array('-w', '--working-directory')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $directory) {
				if (sizeof($directory) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->setDestinationDirectory(current($directory));
			},
			array('-d', '--destination-directory')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $url) {
				if (sizeof($url) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->setRepositoryUrl(current($url));
			},
			array('-r', '--repository-url')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $directory) {
				if (sizeof($directory) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->setScoreDirectory(current($directory));
			},
			array('-sd', '--score-directory')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $file) {
				if (sizeof($file) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->setRevisionFile(current($file));
			},
			array('-rf', '--revision-file')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $directory) {
				if (sizeof($directory) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->setErrorsDirectory(current($directory));
			},
			array('-ed', '--errors-directory')
		);

		parent::run($arguments);

		if ($this->buildPhar === true)
		{
			$this->build();
		}
	}

	public function help()
	{
		$this
			->writeMessage(sprintf($this->locale->_('Usage: %s [options]'), $this->getName()) . PHP_EOL)
			->writeMessage($this->locale->_('Available options are:') . PHP_EOL)
		;

		$this->writeLabels(
			array(
				'-h, --help' => $this->locale->_('Display this help'),
				'-rf <file>, --revision-file <file>' => $this->locale->_('Save last revision in <file>'),
				'-sd <file>, --score-directory <directory>' => $this->locale->_('Save score in <directory>'),
				'-r <url>, --repository-url <url>' => $this->locale->_('Url of subversion repository'),
				'-w <directory>, --working-directory <directory>' => $this->locale->_('Checkout file from subversion in <directory>'),
				'-d <directory>, --destination-directory <directory>' => $this->locale->_('Save phar in <directory>'),
				'-ed <directory>, --errors-directory <directory>' => $this->locale->_('Save errors in <directory>')
			)
		);

		return $this;
	}

	public function writeErrorInErrorsDirectory($error)
	{
		if ($this->errorsDirectory !== null)
		{
			$errorFile = $this->errorsDirectory . \DIRECTORY_SEPARATOR . $this->getRevision();

			if ($this->adapter->file_put_contents($errorFile, $error, \LOCK_EX) === false)
			{
				throw new exceptions\runtime('Unable to save error in file \'' . $errorFile . '\'');
			}
		}

		return $this;
	}

	public function getNextRevisionNumbers(array $revisions = array())
	{
		$revision = $this->getRevision();

		foreach ($this->getLogs() as $log)
		{
			if (isset($log['rev']) === true && ($revision === null || $revision < $log['rev']))
			{
				$revisions[] = $log['rev'];
			}
		}

		return $revisions;
	}
}

?>
