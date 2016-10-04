<?php

namespace mageekguy\atoum\scripts\phar;

require_once __DIR__ . '/../../../constants.php';

use
	mageekguy\atoum,
	mageekguy\atoum\iterators,
	mageekguy\atoum\exceptions
;

class generator extends atoum\script
{
	const phar = 'atoum.phar';

	protected $originDirectory = null;
	protected $destinationDirectory = null;
	protected $stubFile = null;
	protected $pharFactory = null;

	public function __construct($name, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $adapter);

		$this->setPharFactory();
	}

	public function setPharFactory(\closure $factory = null)
	{
		$this->pharFactory = $factory ?: function($path) { return new \phar($path); };

		return $this;
	}

	public function getPharFactory()
	{
		return $this->pharFactory;
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

	protected function doRun()
	{
		if ($this->originDirectory === null)
		{
			throw new exceptions\runtime($this->locale->_('Origin directory must be defined', $this->originDirectory));
		}

		if ($this->destinationDirectory === null)
		{
			throw new exceptions\runtime($this->locale->_('Destination directory must be defined', $this->originDirectory));
		}

		if ($this->stubFile === null)
		{
			throw new exceptions\runtime($this->locale->_('Stub file must be defined', $this->originDirectory));
		}

		if ($this->adapter->is_readable($this->originDirectory) === false)
		{
			throw new exceptions\runtime($this->locale->_('Origin directory \'%s\' is not readable', $this->originDirectory));
		}

		if ($this->adapter->is_writable($this->destinationDirectory) === false)
		{
			throw new exceptions\runtime($this->locale->_('Destination directory \'%s\' is not writable', $this->destinationDirectory));
		}

		if ($this->adapter->is_readable($this->stubFile) === false)
		{
			throw new exceptions\runtime($this->locale->_('Stub file \'%s\' is not readable', $this->stubFile));
		}

		$pharFile = $this->destinationDirectory . DIRECTORY_SEPARATOR . self::phar;

		@$this->adapter->unlink($pharFile);

		$description = @$this->adapter->file_get_contents($this->originDirectory . DIRECTORY_SEPARATOR . 'ABOUT');

		if ($description === false)
		{
			throw new exceptions\runtime($this->locale->_('ABOUT file is missing in \'%s\'', $this->originDirectory));
		}

		$licence = @$this->adapter->file_get_contents($this->originDirectory . DIRECTORY_SEPARATOR . 'LICENSE');

		if ($licence === false)
		{
			throw new exceptions\runtime($this->locale->_('LICENSE file is missing in \'%s\'', $this->originDirectory));
		}

		$stub = @$this->adapter->file_get_contents($this->stubFile);

		if ($stub === false)
		{
			throw new exceptions\runtime($this->locale->_('Unable to read stub file \'%s\'', $this->stubFile));
		}

		$phar = call_user_func($this->pharFactory, $pharFile);

		$phar['versions'] = serialize(array('1' => atoum\version, 'current' => '1'));

		$phar->setStub($stub);
		$phar->setMetadata(
			array(
				'version' => atoum\version,
				'author' => atoum\author,
				'support' => atoum\mail,
				'repository' => atoum\repository,
				'description' => $description,
				'licence' => $licence
			)
		);

		$phar->buildFromIterator(new iterators\recursives\atoum\source($this->originDirectory, '1'));
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
		else if (DIRECTORY_SEPARATOR != '/' || $path != '/')
		{
			$path = rtrim($path, DIRECTORY_SEPARATOR);
		}

		return $path;
	}

	protected function setArgumentHandlers()
	{
		return $this
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
				'Display this help'
			)
			->addArgumentHandler(
				function($script, $argument, $values) {
					if (sizeof($values) !== 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setDestinationDirectory($values[0]);
				},
				array('-d', '--directory'),
				'<directory>',
				$this->locale->_('Destination directory <dir>')
			)
		;
	}
}
