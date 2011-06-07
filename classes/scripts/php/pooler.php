<?php

namespace mageekguy\atoum\scripts\php;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

class pooler extends atoum\script
{
	protected $run = true;
	protected $pool = array();
	protected $phpPath = null;
	protected $workingDirectory = null;

	public function run(array $arguments = array())
	{
		$this->argumentsParser->addHandler(
			function($script, $argument, $values) {
				if (sizeof($values) !== 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->version();
			},
			array('-v', '--version')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $values) {
				if (sizeof($values) !== 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->help();
			},
			array('-h', '--help')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $path) {
				if (sizeof($path) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->setPhpPath(current($path));
			},
			array('-p', '--php')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $directory) {
				if (sizeof($directory) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->setWorkingDirectory(current($directory));
			},
			array('-wd', '--working-directory')
		);

		parent::run($arguments);

		if ($this->run === true)
		{
			$this
				->cleanPool()
				->fillPool()
			;

			$stdin = fopen('php://stdin', 'r');

			$currentPhpId = -1;

			while (feof($stdin) === false)
			{
				switch (trim(fgets($stdin)))
				{
					case 'GET':
						$currentPhpId++;

						if (isset($this->pool[$currentPhpId]) === false)
						{
							echo 'KO' . PHP_EOL;
						}
						else
						{
							echo 'OK' . PHP_EOL;
							echo $currentPhpId . PHP_EOL;
							echo $pool[$currentPhpId][1] . PHP_EOL;
							echo $pool[$currentPhpId][2] . PHP_EOL;
							echo $pool[$currentPhpId][3] . PHP_EOL;
						}
						break;

					case 'KILL':
						$phpId = trim(fgets($stdin));

						if (isset($this->pool[$phpId]) === true)
						{
							proc_close($this->pool[$phpId][0]);

							for ($i = 1; $i <= 3; $i++)
							{
								unlink($this->pool[$phpId][$i]);
							}

							unset($this->pool[$phpId]);

							$this->fillPool();
						}
						break;
				}
			}

			$this->cleanPool();
		}
	}

	public function fillPool()
	{
		if ($this->workingDirectory === null)
		{
			throw new exceptions\runtime('Working directory is undefined');
		}

		if ($this->phpPath === null)
		{
			throw new exceptions\runtime('PHP path is undefined');
		}

		for ($i = sizeof($this->pool); $i < $this->poolSize; $i++)
		{
			$phpStdIn = $this->workingDirectory . DIRECTORY_SEPARATOR . 'stdIn' . $i;
			$phpStdOut = $this->workingDirectory . DIRECTORY_SEPARATOR . 'stdOut' . $i;
			$phpStdErr = $this->workingDirectory . DIRECTORY_SEPARATOR . 'stdErr' . $i;

			$descriptors = array(
				0 => array('file', $phpStdIn, 'r'),
				1 => array('file', $phpStdOut, 'w'),
				2 => array('file', $phpStdErr, 'w')
			);

			$php = proc_open($this->phpPath, $descriptors, $pipes);

			if ($php !== false)
			{
				$this->pool[$i] = array(
					$php,
					$phpStdIn,
					$phpStdOut,
					$phpStdErr
				);
			}
		}

		return $this;
	}

	public function help(array $options = array())
	{
		$this
			->writeMessage(sprintf($this->locale->_('Usage: %s [options]'), $this->getName()) . PHP_EOL)
			->writeMessage($this->locale->_('Available options are:') . PHP_EOL)
		;

		$this->writeLabels(
			array_merge(
				array(
					'-h, --help' => $this->locale->_('Display this help'),
					'-v, --version' => $this->locale->_('Display version'),
					'-p <path/to/php>, -php <path/to/php>' => $this->locale->_('Set php executable path'),
					'-wd <directory>, --working-directory <directory>' => $this->locale->_('Use <directory> as working directory'),
				)
				,
				$options
			)
		);

		$this->run = false;

		return $this;
	}

	public function version()
	{
		$this
			->writeMessage(sprintf($this->locale->_('Atoum version %s by %s (%s)'), atoum\version, atoum\author, atoum\directory) . PHP_EOL)
		;

		$this->run = false;

		return $this;
	}

	public function setPhpPath($path)
	{
		$this->phpPath = (string) $path;

		return $this;
	}

	public function setWorkingDirectory($path)
	{
		$this->workingDirectory = (string) $path;

		return $this;
	}

	public function getWorkingDirectoryIterator()
	{
		return new \recursiveIteratorIterator(new \recursiveDirectoryIterator($this->workingDirectory, \filesystemIterator::KEY_AS_PATHNAME | \filesystemIterator::CURRENT_AS_FILEINFO | \filesystemIterator::SKIP_DOTS), \recursiveIteratorIterator::CHILD_FIRST);
	}

	public function cleanPool()
	{
		foreach ($this->pool as $php)
		{
			proc_close($php[0]);
		}

		$this->cleanWorkingDirectory();

		return $this;
	}

	public function cleanWorkingDirectory()
	{
		foreach ($this->getWorkingDirectoryIterator() as $pathname => $inode)
		{
			if ($inode->isDir() === false)
			{
				$this->adapter->unlink($pathname);
			}
			else if ($pathname !== $this->workingDirectory)
			{
				$this->adapter->rmdir($pathname);
			}
		}

		return $this;
	}
}

?>
