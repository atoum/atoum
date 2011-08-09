<?php

namespace mageekguy\atoum\scripts\phar;

require_once(__DIR__ . '/../../../constants.php');

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class generator extends atoum\script
{
	const phar = 'mageekguy.atoum.phar';

	protected $help = false;
	protected $originDirectory = null;
	protected $destinationDirectory = null;
	protected $stubFile = null;

	private $pharInjector = null;
	private $srcIteratorInjector = null;
	private $configurationsIteratorInjector = null;

	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $locale, $adapter);

		$this->pharInjector = function ($name) { return new \phar($name); };
		$this->srcIteratorInjector = function ($directory) { return new \recursiveDirectoryIterator($directory); };
		$this->configurationsIteratorInjector = function ($directory) { return new \recursiveDirectoryIterator($directory); };
	}

	public function setOriginDirectory($directory)
	{
		$originDirectory = $this->cleanPath($directory);

		if ($originDirectory == '')
		{
			throw new exceptions\runtime('Empty origin directory is invalid');
		}
		else if ($this->adapter->is_dir($originDirectory) === false)
		{
			throw new exceptions\runtime('Path \'' . $originDirectory . '\' of origin directory is invalid');
		}
		else if ($this->destinationDirectory !== null && $originDirectory === $this->destinationDirectory)
		{
			throw new exceptions\runtime('Origin directory must be different from destination directory');
		}

		$this->originDirectory = $originDirectory;

		return $this;
	}

	public function getOriginDirectory()
	{
		return $this->originDirectory;
	}

	public function setDestinationDirectory($directory)
	{
		$destinationDirectory = $this->cleanPath($directory);

		if ($destinationDirectory == '')
		{
			throw new exceptions\runtime('Empty destination directory is invalid');
		}
		else if ($this->adapter->is_dir($destinationDirectory) === false)
		{
			throw new exceptions\runtime('Path \'' . $destinationDirectory . '\' of destination directory is invalid');
		}
		else if ($this->originDirectory !== null && $destinationDirectory === $this->originDirectory)
		{
			throw new exceptions\runtime('Destination directory must be different from origin directory');
		}
		else if (strpos($destinationDirectory, $this->originDirectory) === 0)
		{
			throw new exceptions\runtime('Origin directory must not include destination directory');
		}

		$this->destinationDirectory = $destinationDirectory;

		return $this;
	}

	public function setStubFile($stubFile)
	{
		$stubFile = $this->cleanPath($stubFile);

		if ($stubFile == '')
		{
			throw new exceptions\runtime('Stub file is invalid');
		}

		if ($this->adapter->is_file($stubFile) === false)
		{
			throw new exceptions\runtime('Stub file is not a valid file');
		}

		$this->stubFile = $stubFile;

		return $this;
	}

	public function getDestinationDirectory()
	{
		return $this->destinationDirectory;
	}

	public function getStubFile()
	{
		return $this->stubFile;
	}

	public function getPhar($name)
	{
		return $this->pharInjector->__invoke($name);
	}

	public function setPharInjector(\closure $pharInjector)
	{
		$closure = new \reflectionMethod($pharInjector, '__invoke');

		if ($closure->getNumberOfParameters() != 1)
		{
			throw new exceptions\runtime('Phar injector must take one argument');
		}

		$this->pharInjector = $pharInjector;

		return $this;
	}

	public function getSrcIterator($directory)
	{
		return $this->srcIteratorInjector->__invoke($directory);
	}

	public function setSrcIteratorInjector(\closure $srcIteratorInjector)
	{
		$closure = new \reflectionMethod($srcIteratorInjector, '__invoke');

		if ($closure->getNumberOfParameters() != 1)
		{
			throw new exceptions\runtime('Source iterator injector must take one argument');
		}

		$this->srcIteratorInjector = $srcIteratorInjector;

		return $this;
	}

	public function getConfigurationsIterator($directory)
	{
		return $this->configurationsIteratorInjector->__invoke($directory);
	}

	public function setConfigurationsIteratorInjector(\closure $configurationsIteratorInjector)
	{
		$closure = new \reflectionMethod($configurationsIteratorInjector, '__invoke');

		if ($closure->getNumberOfParameters() != 1)
		{
			throw new exceptions\runtime('Configurations iterator injector must take one argument');
		}

		$this->configurationsIteratorInjector = $configurationsIteratorInjector;

		return $this;
	}

	public function run(array $arguments = array())
	{
		$this->help = false;

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
			function($script, $argument, $values) {
				if (sizeof($values) !== 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->setDestinationDirectory($values[0]);
			},
			array('-d', '--directory')
		);

		parent::run($arguments);

		if ($this->help === false)
		{
			$this->generate();
		}

		return $this;
	}

	public function help()
	{
		$this
			->writeMessage(sprintf($this->locale->_('Usage: %s [options]'), $this->getName()) . PHP_EOL)
			->writeMessage($this->locale->_('Available options are:') . PHP_EOL)
		;

		$options = array(
			'-h, --help' => $this->locale->_('Display this help'),
			'-d <dir>, --directory <dir>' => $this->locale->_('Destination directory <dir>')
		);

		$this->writeLabels($options);

		$this->help = true;

		return $this;
	}

	protected function generate()
	{
		if ($this->originDirectory === null)
		{
			throw new exceptions\runtime(sprintf($this->locale->_('Origin directory must be defined'), $this->originDirectory));
		}

		if ($this->destinationDirectory === null)
		{
			throw new exceptions\runtime(sprintf($this->locale->_('Destination directory must be defined'), $this->originDirectory));
		}

		if ($this->stubFile === null)
		{
			throw new exceptions\runtime(sprintf($this->locale->_('Stub file must be defined'), $this->originDirectory));
		}

		if ($this->adapter->is_readable($this->originDirectory) === false)
		{
			throw new exceptions\runtime(sprintf($this->locale->_('Origin directory \'%s\' is not readable'), $this->originDirectory));
		}

		if ($this->adapter->is_writable($this->destinationDirectory) === false)
		{
			throw new exceptions\runtime(sprintf($this->locale->_('Destination directory \'%s\' is not writable'), $this->destinationDirectory));
		}

		if ($this->adapter->is_readable($this->stubFile) === false)
		{
			throw new exceptions\runtime(sprintf($this->locale->_('Stub file \'%s\' is not readable'), $this->stubFile));
		}

		$pharFile = $this->destinationDirectory . DIRECTORY_SEPARATOR . self::phar;

		@$this->adapter->unlink($pharFile);

		$phar = $this->getPhar($pharFile);

		if ($phar instanceof \phar === false)
		{
			throw new exceptions\logic('Phar injector must return a \phar instance');
		}

		$srcIterator = $this->getSrcIterator($this->originDirectory);

		if ($srcIterator instanceof \recursiveDirectoryIterator === false)
		{
			throw new exceptions\logic('Source iterator injector must return a \recursiveDirectoryIterator instance');
		}

		$description = @$this->adapter->file_get_contents($this->originDirectory . DIRECTORY_SEPARATOR . 'ABOUT');

		if ($description === false)
		{
			throw new exceptions\runtime(sprintf($this->locale->_('ABOUT file is missing in \'%s\''), $this->originDirectory));
		}

		$licence = @$this->adapter->file_get_contents($this->originDirectory . DIRECTORY_SEPARATOR . 'COPYING');

		if ($licence === false)
		{
			throw new exceptions\runtime(sprintf($this->locale->_('COPYING file is missing in \'%s\''), $this->originDirectory));
		}

		$stub = @$this->adapter->file_get_contents($this->stubFile);

		if ($stub === false)
		{
			throw new exceptions\runtime(sprintf($this->locale->_('Unable to read stub file \'%s\''), $this->stubFile));
		}

		$phar->setStub($stub);

		$phar->setMetadata(array(
					'version' => atoum\version,
					'author' => atoum\author,
					'support' => atoum\mail,
					'repository' => atoum\repository,
					'description' => $description,
					'licence' => $licence
					)
				);

		$phar->buildFromIterator(new \recursiveIteratorIterator(new atoum\src\iterator\filter($srcIterator)), $this->originDirectory);

		$configurationsIterator = $this->getConfigurationsIterator($phar['resources/configurations']);

		if ($configurationsIterator instanceof \recursiveDirectoryIterator === false)
		{
			throw new exceptions\logic('Configurations iterator injector must return a \recursiveDirectoryIterator instance');
		}

		$configurationsIterator->setFlags(\filesystemIterator::CURRENT_AS_SELF);

		foreach (new \recursiveIteratorIterator($configurationsIterator) as $configurations)
		{
			if ($configurations->current()->isFile() === true)
			{
				$path = $configurations->getSubpathname();

				if (substr($path, -4) === '.php')
				{
					unset($phar['resources/configurations/' . $path]);
				}
			}
		}

		$phar->setSignatureAlgorithm(\phar::SHA1);

		return $this;
	}

	protected function cleanPath($path)
	{
		$path = $this->adapter->realpath((string) $path);

		if ($path === false)
		{
			$path = '';
		}
		else if (DIRECTORY_SEPARATOR == '/' && $path != '/')
		{
			$path = rtrim($path, DIRECTORY_SEPARATOR);
		}

		return $path;
	}
}

?>
