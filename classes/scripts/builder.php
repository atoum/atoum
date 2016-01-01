<?php

namespace mageekguy\atoum\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\scripts\phar,
	mageekguy\atoum\scripts\builder
;

class builder extends atoum\script\configurable
{
	const defaultConfigFile = '.builder.php';
	const defaultUnitTestRunnerScript = 'scripts/runner.php';
	const defaultPharGeneratorScript = 'scripts/phar/generator.php';

	private   $lockResource = null;

	protected $php = null;
	protected $vcs = null;
	protected $taggerEngine = null;
	protected $revision = null;
	protected $version = null;
	protected $unitTestRunnerScript = null;
	protected $pharGeneratorScript = null;
	protected $workingDirectory = null;
	protected $destinationDirectory = null;
	protected $scoreDirectory = null;
	protected $errorsDirectory = null;
	protected $revisionFile = null;
	protected $runFile = null;
	protected $pharCreationEnabled = true;
	protected $checkUnitTests = true;
	protected $reportTitle = null;
	protected $runnerConfigurationFiles = array();

	public function __construct($name, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $adapter);

		$this
			->setVcs()
			->setPhp()
			->setUnitTestRunnerScript(self::defaultUnitTestRunnerScript)
			->setPharGeneratorScript(self::defaultPharGeneratorScript)
		;
	}

	public function setVcs(builder\vcs $vcs = null)
	{
		$this->vcs = $vcs ?: new builder\vcs\svn();

		return $this;
	}

	public function getVcs()
	{
		return $this->vcs;
	}

	public function setTaggerEngine(atoum\scripts\tagger\engine $engine)
	{
		$this->taggerEngine = $engine;

		return $this;
	}

	public function getTaggerEngine()
	{
		return $this->taggerEngine;
	}

	public function setPhp(atoum\php $php = null)
	{
		$this->php = $php ?: new atoum\php();

		return $this;
	}

	public function getPhp()
	{
		return $this->php;
	}

	public function setPhpPath($path)
	{
		$this->php->setBinaryPath($path);

		return $this;
	}

	public function getPhpPath()
	{
		return $this->php->getBinaryPath();
	}

	public function getRunnerConfigurationFiles()
	{
		return $this->runnerConfigurationFiles;
	}

	public function addRunnerConfigurationFile($file)
	{
		$this->runnerConfigurationFiles[] = (string) $file;

		return $this;
	}

	public function enablePharCreation()
	{
		$this->pharCreationEnabled = true;

		return $this;
	}

	public function disablePharCreation()
	{
		$this->pharCreationEnabled = false;

		return $this;
	}

	public function pharCreationIsEnabled()
	{
		return $this->pharCreationEnabled;
	}

	public function disableUnitTestChecking()
	{
		$this->checkUnitTests = false;

		return $this;
	}

	public function enableUnitTestChecking()
	{
		$this->checkUnitTests = true;

		return $this;
	}

	public function unitTestCheckingIsEnabled()
	{
		return $this->checkUnitTests;
	}

	public function setVersion($version)
	{
		$this->version = (string) $version;

		return $this;
	}

	public function getVersion()
	{
		return $this->version;
	}

	public function setScoreDirectory($path)
	{
		$this->scoreDirectory = static::cleanDirectoryPath($path);

		return $this;
	}

	public function getScoreDirectory()
	{
		return $this->scoreDirectory;
	}

	public function setErrorsDirectory($path)
	{
		$this->errorsDirectory = static::cleanDirectoryPath($path);

		return $this;
	}

	public function getErrorsDirectory()
	{
		return $this->errorsDirectory;
	}

	public function setDestinationDirectory($path)
	{
		$this->destinationDirectory = static::cleanDirectoryPath($path);

		return $this;
	}

	public function getDestinationDirectory()
	{
		return $this->destinationDirectory;
	}

	public function setWorkingDirectory($path)
	{
		$this->workingDirectory = static::cleanDirectoryPath($path);

		return $this;
	}

	public function getWorkingDirectory()
	{
		return $this->workingDirectory;
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

	public function setReportTitle($title)
	{
		$this->reportTitle = (string) $title;

		return $this;
	}

	public function getReportTitle()
	{
		return $this->reportTitle;
	}

	public function setUnitTestRunnerScript($path)
	{
		$this->unitTestRunnerScript = (string) $path;

		return $this;
	}

	public function getUnitTestRunnerScript()
	{
		return $this->unitTestRunnerScript;
	}

	public function setPharGeneratorScript($path)
	{
		$this->pharGeneratorScript = (string) $path;

		return $this;
	}

	public function getPharGeneratorScript()
	{
		return $this->pharGeneratorScript;
	}

	public function setRunFile($path)
	{
		$this->runFile = $path;

		return $this;
	}

	public function getRunFile()
	{
		return $this->runFile !== null ? $this->runFile : $this->adapter->sys_get_temp_dir() . \DIRECTORY_SEPARATOR . md5(get_class($this));
	}

	public function checkUnitTests()
	{
		$status = true;

		if ($this->checkUnitTests === true)
		{
			if ($this->workingDirectory === null)
			{
				throw new exceptions\logic('Unable to check unit tests, working directory is undefined');
			}

			$this->vcs
				->setWorkingDirectory($this->workingDirectory)
				->exportRepository()
			;

			$this->php
				->reset()
				->addOption('-f', $this->workingDirectory . \DIRECTORY_SEPARATOR . $this->unitTestRunnerScript)
				->addArgument('-ncc')
				->addArgument('-d', $this->workingDirectory . \DIRECTORY_SEPARATOR . 'tests' . \DIRECTORY_SEPARATOR . 'units' . \DIRECTORY_SEPARATOR . 'classes')
				->addArgument('-p', $this->php->getBinaryPath())
			;

			$scoreFile = $this->scoreDirectory === null ? $this->adapter->tempnam($this->adapter->sys_get_temp_dir(), '') : $this->scoreDirectory . DIRECTORY_SEPARATOR . $this->vcs->getRevision();

			$this->php->addArgument('-sf', $scoreFile);

			if ($this->reportTitle !== null)
			{
				$this->php->addArgument('-drt',  sprintf($this->reportTitle, '%1$s', '%2$s', '%3$s', $this->vcs->getRevision()));
			}

			foreach ($this->runnerConfigurationFiles as $runnerConfigurationFile)
			{
				$this->php->addArgument('-c',  $runnerConfigurationFile);
			}

			try
			{
				$exitCode = $this->php->run()->getExitCode();

				if ($exitCode > 0)
				{
					switch ($exitCode)
					{
						case 126:
						case 127:
							throw new exceptions\runtime('Unable to find \'' . $this->php->getBinaryPath() . '\' or it is not executable');

						default:
							throw new exceptions\runtime($this->php . ' failed with exit code \'' . $exitCode . '\': ' . $this->php->getStderr());
					}
				}

				$stdErr = $this->php->getStdErr();

				if ($stdErr != '')
				{
					throw new exceptions\runtime($stdErr);
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

				$status = $score->getFailNumber() === 0 && $score->getExceptionNumber() === 0 && $score->getErrorNumber() === 0;
			}
			catch (\exception $exception)
			{
				$this->writeErrorInErrorsDirectory($exception->getMessage());

				$status = false;
			}

			if ($this->scoreDirectory === null)
			{
				if ($this->adapter->unlink($scoreFile) === false)
				{
					throw new exceptions\runtime('Unable to delete score file \'' . $scoreFile . '\'');
				}
			}
		}

		return $status;
	}

	public function createPhar($version = null)
	{
		$pharBuilt = true;

		if ($this->pharCreationEnabled === true)
		{
			if ($this->destinationDirectory === null)
			{
				throw new exceptions\logic('Unable to create phar, destination directory is undefined');
			}

			if ($this->workingDirectory === null)
			{
				throw new exceptions\logic('Unable to create phar, working directory is undefined');
			}

			if ($this->revisionFile !== null)
			{
				$revision = trim(@$this->adapter->file_get_contents($this->revisionFile));

				if (is_numeric($revision) === true)
				{
					$this->vcs->setRevision($revision);
				}
			}

			$revisions = $this->vcs->getNextRevisions();

			while (sizeof($revisions) > 0)
			{
				$revision = array_shift($revisions);

				$this->vcs->setRevision($revision);

				try
				{
					if ($this->checkUnitTests() === true)
					{
						if ($this->checkUnitTests === false)
						{
							$this->vcs
								->setWorkingDirectory($this->workingDirectory)
								->exportRepository()
							;
						}

						if ($this->taggerEngine !== null)
						{
							$this->taggerEngine
								->setSrcDirectory($this->workingDirectory)
								->setVersion($version !== null ? $version : 'nightly-' . $revision . '-' . $this->adapter->date('YmdHi'))
								->tagVersion()
							;
						}

						$this->php
							->reset()
							->addOption('-d', 'phar.readonly=0')
							->addOption('-f', $this->workingDirectory . DIRECTORY_SEPARATOR . $this->pharGeneratorScript)
							->addArgument('-d', $this->destinationDirectory)
						;

						if ($this->php->run()->getExitCode() > 0)
						{
							throw new exceptions\runtime('Unable to run ' . $this->php . ': ' . $this->php->getStdErr());
						}
					}
				}
				catch (\exception $exception)
				{
					$pharBuilt = false;

					$this->writeErrorInErrorsDirectory($exception->getMessage());
				}

				if ($this->revisionFile !== null && $this->adapter->file_put_contents($this->revisionFile, $revision, \LOCK_EX) === false)
				{
					throw new exceptions\runtime('Unable to save last revision in file \'' . $this->revisionFile . '\'');
				}

				$revisions = $this->vcs->getNextRevisions();
			}
		}

		return $pharBuilt;
	}

	public function writeErrorInErrorsDirectory($error)
	{
		if ($this->errorsDirectory !== null)
		{
			$revision = $this->vcs === null ? null : $this->vcs->getRevision();

			if ($revision === null)
			{
				throw new exceptions\logic('Revision is undefined');
			}

			$errorFile = $this->errorsDirectory . \DIRECTORY_SEPARATOR . $revision;

			if ($this->adapter->file_put_contents($errorFile, $error, \LOCK_EX | \FILE_APPEND) === false)
			{
				throw new exceptions\runtime('Unable to save error in file \'' . $errorFile . '\'');
			}
		}

		return $this;
	}

	protected function includeConfigFile($path, \closure $callback = null)
	{
		if ($callback === null)
		{
			$builder = $this;

			$callback = function($path) use ($builder) { include_once($path); };
		}

		return parent::includeConfigFile($path, $callback);
	}

	protected function setArgumentHandlers()
	{
		$builder = $this;

		return parent::setArgumentHandlers()
			->addArgumentHandler(
				function($script, $argument, $files) use ($builder) {
					if (sizeof($files) <= 0)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					foreach ($files as $file)
					{
						if (file_exists($file) === false)
						{
							throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Runner configuration file path \'%s\' is invalid'), $file));
						}

						if (is_readable($file) === false)
						{
							throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Unable to read runner configuration file \'%s\''), $file));
						}

						$script->addRunnerConfigurationFile($file);
					}
				},
				array('-rc', '--runner-configuration-files'),
				'<file>',
				 $this->locale->_('Use <file> as configuration file for runner')
			)
			->addArgumentHandler(
				function($script, $argument, $path) {
					if (sizeof($path) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setPhpPath(current($path));
				},
				array('-p', '--php'),
				'<path>',
				$this->locale->_('Path to PHP binary')
			)
			->addArgumentHandler(
				function($script, $argument, $directory) {
					if (sizeof($directory) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setWorkingDirectory(current($directory));
				},
				array('-w', '--working-directory'),
				'<directory>',
				$this->locale->_('Checkout file from repository in <directory>')
			)
			->addArgumentHandler(
				function($script, $argument, $directory) {
					if (sizeof($directory) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setDestinationDirectory(current($directory));
				},
				array('-d', '--destination-directory'),
				'<directory>',
				$this->locale->_('Save phar in <directory>')

			)
			->addArgumentHandler(
				function($script, $argument, $directory) {
					if (sizeof($directory) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setScoreDirectory(current($directory));
				},
				array('-sd', '--score-directory'),
				'<directory>',
				$this->locale->_('Save score in <directory>')
			)
			->addArgumentHandler(
				function($script, $argument, $directory) {
					if (sizeof($directory) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setErrorsDirectory(current($directory));
				},
				array('-ed', '--errors-directory'),
				'<directory>',
				$this->locale->_('Save errors in <directory>')
			)
			->addArgumentHandler(
				function($script, $argument, $url) {
					if (sizeof($url) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->getVcs()->setRepositoryUrl(current($url));
				},
				array('-r', '--repository-url'),
				'<url>',
				$this->locale->_('Url of repository')
			)
			->addArgumentHandler(
				function($script, $argument, $file) {
					if (sizeof($file) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setRevisionFile(current($file));
				},
				array('-rf', '--revision-file'),
				'<file>',
				$this->locale->_('Save last revision in <file>')
			)
			->addArgumentHandler(
				function($script, $argument, $version) {
					if (sizeof($version) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setVersion(current($version));
				},
				array('-v', '--version'),
				'<string>',
				$this->locale->_('Version <string> will be used as version name')
			)
			->addArgumentHandler(
				function($script, $argument, $unitTestRunnerScript) {
					if (sizeof($unitTestRunnerScript) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setUnitTestRunnerScript(current($unitTestRunnerScript));
				},
				array('-utrs', '--unit-test-runner-script')
			)
			->addArgumentHandler(
				function($script, $argument, $pharGeneratorScript) {
					if (sizeof($pharGeneratorScript) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setPharGeneratorScript(current($pharGeneratorScript));
				},
				array('-pgs', '--phar-generator-script')
			)
			->addArgumentHandler(
				function($script, $argument, $reportTitle) {
					if (sizeof($reportTitle) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setReportTitle(current($reportTitle));
				},
				array('-rt', '--report-title')
			)
		;
	}

	final protected function lock()
	{
		$runFile = $this->getRunFile();
		$pid = trim(
			@$this->adapter->file_get_contents($runFile)
		);

		$pid_exists = is_numeric($pid);

		if ($pid_exists !== false && $this->adapter->function_exists('posix_kill'))
		{
			$pid_exists = $this->adapter->posix_kill($pid, 0);
		}

		if ($pid_exists !== false)
		{
			throw new exceptions\runtime($this->locale->_('A process has locked run file \'%s\'', $runFile));
		}

		$this->lockResource = @$this->adapter->fopen($runFile, 'w+');

		if ($this->lockResource === false)
		{
			throw new exceptions\runtime($this->locale->_('Unable to open run file \'%s\'', $runFile));
		}

		if ($this->adapter->flock($this->lockResource, \LOCK_EX | \LOCK_NB) === false)
		{
			throw new exceptions\runtime($this->locale->_('Unable to get exclusive lock on run file \'%s\'', $runFile));
		}

		$this->adapter->fwrite($this->lockResource, $this->adapter->getmypid());

		return true;
	}

	final protected function unlock()
	{
		if ($this->lockResource !== null)
		{
			$this->adapter->fclose($this->lockResource);

			@$this->adapter->unlink($this->getRunFile());
		}
	}

	protected function doRun()
	{
		if ($this->pharCreationEnabled === true && $this->lock())
		{
			try
			{
				$this->createPhar($this->version);
			}
			catch (\Exception $exception)
			{
				$this->unlock();

				throw $exception;
			}

			$this->unlock();
		}

		return $this;
	}

	protected function cleanDirectoryPath($path)
	{
		return rtrim($path, DIRECTORY_SEPARATOR);
	}
}
