<?php

namespace mageekguy\atoum\scripts;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

class tagger extends atoum\script
{
	const defaultVersionPattern = '/\$Rev: [^ ]+ \$/';

	protected $stopRun = false;
	protected $version = null;
	protected $versionPattern = null;
	protected $srcDirectory = null;
	protected $destinationDirectory = null;
	protected $srcIteratorInjector = null;

	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $locale, $adapter);

		$this->setVersionPattern(static::defaultVersionPattern);
	}

	public function getVersion()
	{
		return $this->version;
	}

	public function setVersion($version)
	{
		$this->version = (string) $version;

		return $this;
	}

	public function getVersionPattern()
	{
		return $this->versionPattern;
	}

	public function setVersionPattern($pattern)
	{
		$this->versionPattern = (string) $pattern;

		return $this;
	}

	public function getSrcDirectory()
	{
		return $this->srcDirectory;
	}

	public function setSrcDirectory($directory)
	{
		$this->srcDirectory = rtrim((string) $directory, \DIRECTORY_SEPARATOR);

		if ($this->destinationDirectory === null)
		{
			$this->destinationDirectory = $this->srcDirectory;
		}

		return $this;
	}

	public function getDestinationDirectory()
	{
		return $this->destinationDirectory;
	}

	public function setDestinationDirectory($directory)
	{
		$this->destinationDirectory = rtrim((string) $directory, \DIRECTORY_SEPARATOR);

		return $this;
	}

	public function setSrcIteratorInjector(\closure $srcIteratorInjector)
	{
		$closure = new \reflectionMethod($srcIteratorInjector, '__invoke');

		if ($closure->getNumberOfParameters() != 1)
		{
			throw new exceptions\logic('Src iterator injector must take one argument');
		}

		$this->srcIteratorInjector = $srcIteratorInjector;

		return $this;
	}

	public function getSrcIterator()
	{
		if ($this->srcDirectory === null)
		{
			throw new exceptions\logic('Unable to get files iterator, source directory is undefined');
		}

		if ($this->srcIteratorInjector === null)
		{
			$this->setSrcIteratorInjector(function ($directory) { return new \recursiveIteratorIterator(new atoum\src\iterator\filter(new \recursiveDirectoryIterator($directory))); });
		}

		return $this->srcIteratorInjector->__invoke($this->srcDirectory);
	}

	public function tagVersion()
	{
		if ($this->srcDirectory === null)
		{
			throw new exceptions\logic('Unable to tag, src directory is undefined');
		}

		if ($this->version === null)
		{
			throw new exceptions\logic('Unable to tag, version is undefined');
		}

		$srcIterator = $this->getSrcIterator();

		if ($srcIterator instanceof \iterator === false)
		{
			throw new exceptions\logic('Unable to tag, src iterator injector does not return an iterator');
		}

		foreach ($srcIterator as $path)
		{
			$fileContents = @$this->adapter->file_get_contents($path);

			if ($fileContents === false)
			{
				throw new exceptions\runtime('Unable to tag, path \'' . $path . '\' is unreadable');
			}

			$path = $this->destinationDirectory == $this->srcDirectory ? $path : $this->destinationDirectory . \DIRECTORY_SEPARATOR . substr($path, strlen($this->srcDirectory) + 1);

			$directory = $this->adapter->dirname($path);

			if ($this->adapter->is_dir($directory) === false)
			{
				$this->adapter->mkdir($directory, 0777, true);
			}

			if ($this->adapter->file_put_contents($path, preg_replace(self::defaultVersionPattern, $this->version, $fileContents), \LOCK_EX) === false)
			{
				throw new exceptions\runtime('Unable to tag, path \'' . $path . '\' is unwritable');
			}
		}

		return $this;
	}

	public function run(array $arguments = array())
	{
		$this->argumentsParser->addHandler(
			function($script, $argument, $values) {
				if (sizeof($values) != 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->help();
			},
			array('-h', '--help')
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
			function($script, $argument, $directory) {
				if (sizeof($directory) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->setSrcDirectory(current($directory));
			},
			array('-s', '--src-directory')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $versionPattern) {
				if (sizeof($versionPattern) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->setVersionPattern(current($versionPattern));
			},
			array('-vp', '--version-pattern')
		);

		$this->argumentsParser->addHandler(
			function($script, $argument, $version) {
				if (sizeof($version) != 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->setVersion(current($version));
			},
			array('-v', '--version')
		);

		parent::run($arguments);

		if ($this->stopRun === false)
		{
			$this->tagVersion();
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
				'-v <string>, --version <string>' => $this->locale->_('Use <string> as version value'),
				'-vp <regex>, --version-pattern <regex>' => $this->locale->_('Use <regex> to set version in source files'),
				'-s <directory>, --src-directory <directory>' => $this->locale->_('Use <directory> as source directory'),
				'-d <directory>, --destination-directory <directory>' => $this->locale->_('Save tagged files in <directory>'),
			)
		);

		$this->stopRun = true;

		return $this;
	}
}

?>
