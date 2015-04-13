<?php

namespace mageekguy\atoum\script;

use
	mageekguy\atoum,
	mageekguy\atoum\includer,
	mageekguy\atoum\exceptions
;

abstract class configurable extends atoum\script
{
	const defaultConfigFile = '.config.php';

	protected $includer = null;
	protected $configFiles = array();

	public function __construct($name, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $adapter);

		$this->setIncluder();
	}

	public function setIncluder(atoum\includer $includer = null)
	{
		$this->includer = $includer ?: new atoum\includer();

		return $this;
	}

	public function getIncluder()
	{
		return $this->includer;
	}

	public function getConfigFiles()
	{
		return $this->configFiles;
	}

	public function useConfigFile($path)
	{
		return $this->includeConfigFile($path);
	}

	public function useDefaultConfigFiles($startDirectory = null)
	{
		if ($startDirectory === null)
		{
			$startDirectory = $this->getDirectory();
		}

		$configFilesFound = $this->configFiles;

		foreach (self::getSubDirectoryPath($startDirectory) as $directory)
		{
			try
			{
				$this->useConfigFile($directory . static::defaultConfigFile);
			}
			catch (atoum\includer\exception $exception) {}
		}

		if ($configFilesFound === $this->configFiles)
		{
			$workingDirectory = $this->adapter->getcwd();

			if ($workingDirectory !== $startDirectory)
			{
				foreach (self::getSubDirectoryPath($workingDirectory) as $directory)
				{
					try
					{
						$this->useConfigFile($directory . static::defaultConfigFile);
					}
					catch (atoum\includer\exception $exception) {}
				}
			}
		}

		return $this;
	}

	public function run(array $arguments = array())
	{
		$this->useDefaultConfigFiles();

		return parent::run($arguments);
	}

	public static function getSubDirectoryPath($directory, $directorySeparator = null)
	{
		$directorySeparator = $directorySeparator ?: DIRECTORY_SEPARATOR;

		$paths = array();

		if ($directory != '')
		{
			if ($directory == $directorySeparator)
			{
				$paths[] = $directory;
			}
			else
			{
				$directory = rtrim($directory, $directorySeparator);

				$path = '';

				foreach (explode($directorySeparator, $directory) as $subDirectory)
				{
					$path .= $subDirectory . $directorySeparator;

					$paths[] = $path;
				}
			}
		}

		return $paths;
	}

	protected function setArgumentHandlers()
	{
		parent::setArgumentHandlers()
			->addArgumentHandler(
					function($script, $argument, $values) {
						if (sizeof($values) !== 0)
						{
							throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
						}

						$script->help();
					},
					array('-h', '--help'),
					null,
					$this->locale->_('Display this help')
				)
			->addArgumentHandler(
					function($script, $argument, $files) {
						if (sizeof($files) <= 0)
						{
							throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
						}

						foreach ($files as $path)
						{
							try
							{
								$script->useConfigFile($path);
							}
							catch (includer\exception $exception)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Configuration file \'%s\' does not exist'), $path));
							}
						}
					},
					array('-c', '--configurations'),
					'<file>...',
					$this->locale->_('Use all configuration files <file>'),
					PHP_INT_MAX
				)
		;

		return $this;
	}

	protected function includeConfigFile($path, \closure $callback = null)
	{
		if ($callback === null)
		{
			$script = $this;

			$callback = function($path) use ($script) { include_once($path); };
		}

		try
		{
			$this->includer->resetErrors()->includePath($path, $callback);

			$this->configFiles[] = $path;
		}
		catch (atoum\includer\exception $exception)
		{
			throw new atoum\includer\exception(sprintf($this->getLocale()->_('Unable to find configuration file \'%s\''), $path));
		}

		$firstError = $this->includer->getFirstError();

		if ($firstError !== null)
		{
			list($error, $message, $file, $line) = $firstError;

			throw new exceptions\runtime($message . ' in ' . $path . ' at line ' . $line, $error);
		}

		return $this;
	}
}
