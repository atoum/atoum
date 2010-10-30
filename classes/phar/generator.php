<?php

namespace mageekguy\atoum\phar;

use \mageekguy\atoum;

class generator extends atoum\script
{
	const version = '0.0.1';
	const author = 'Frédéric Hardy';
	const mail = 'support@atoum.org';
	const repository = 'https://svn.mageekbox.net/repositories/unit/trunk';
	const phar = 'mageekguy.atoum.phar';

	protected $help = false;
	protected $originDirectory = null;
	protected $destinationDirectory = null;
	protected $stubFile = null;

	private $pharInjecter = null;
	private $fileIteratorInjecter = null;

	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $locale, $adapter);

		$this->pharInjecter = function ($name) { return new \phar($name); };
		$this->fileIteratorInjecter = function ($directory) { return new \recursiveIteratorIterator(new iterator(new \recursiveDirectoryIterator($directory))); };
	}

	public function setOriginDirectory($directory)
	{
		$originDirectory = $this->cleanPath($directory);

		if ($originDirectory == '')
		{
			throw new \runtimeException('Empty origin directory is invalid');
		}
		else if ($this->adapter->is_dir($originDirectory) === false)
		{
			throw new \runtimeException('Path \'' . $originDirectory . '\' of origin directory is invalid');
		}
		else if ($this->destinationDirectory !== null && $originDirectory === $this->destinationDirectory)
		{
			throw new \runtimeException('Origin directory must be different from destination directory');
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
			throw new \runtimeException('Empty destination directory is invalid');
		}
		else if ($this->adapter->is_dir($destinationDirectory) === false)
		{
			throw new \runtimeException('Path \'' . $destinationDirectory . '\' of destination directory is invalid');
		}
		else if ($this->originDirectory !== null && $destinationDirectory === $this->originDirectory)
		{
			throw new \runtimeException('Destination directory must be different from origin directory');
		}
		else if (strpos($destinationDirectory, $this->originDirectory) === 0)
		{
			throw new \runtimeException('Origin directory must not include destination directory');
		}

		$this->destinationDirectory = $destinationDirectory;

		return $this;
	}

	public function setStubFile($stubFile)
	{
		$stubFile = $this->cleanPath($stubFile);

		if ($stubFile == '')
		{
			throw new \runtimeException('Stub file is invalid');
		}

		if ($this->adapter->is_file($stubFile) === false)
		{
			throw new \runtimeException('Stub file is not a valid file');
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
		return $this->pharInjecter->__invoke($name);
	}

	public function setPharInjecter(\closure $pharInjecter)
	{
		$closure = new \reflectionMethod($pharInjecter, '__invoke');

		if ($closure->getNumberOfParameters() != 1)
		{
			throw new \runtimeException('Phar injecter must take one argument');
		}

		$this->pharInjecter = $pharInjecter;

		return $this;
	}

	public function getFileIterator($directory)
	{
		return $this->fileIteratorInjecter->__invoke($directory);
	}

	public function setFileIteratorInjecter(\closure $fileIteratorInjecter)
	{
		$closure = new \reflectionMethod($fileIteratorInjecter, '__invoke');

		if ($closure->getNumberOfParameters() != 1)
		{
			throw new \runtimeException('File iterator injecter must take one argument');
		}

		$this->fileIteratorInjecter = $fileIteratorInjecter;

		return $this;
	}

	public function run(atoum\superglobal $superglobal = null)
	{
		$this->help = false;

		parent::run($superglobal);

		return ($this->help === true ?  $this->help() : $this->generate());
	}

	protected function handleArgument($argument)
	{
		switch ($argument)
		{
			case '-h':
			case '--help':
				$this->help = true;
				break;

			case '-d':
			case '--directory':
				$this->arguments->next();

				$directory = $this->arguments->current();

				if ($this->arguments->valid() === false || self::isArgument($directory) === true)
				{
					throw new \logicException(sprintf($this->locale->_('Bad usage of %s, do php %s --help for more informations'), $argument, $this->getName()));
				}

				$this->setDestinationDirectory($directory);
				break;

			default:
				throw new \logicException(sprintf($this->locale->_('Argument \'%s\' is unknown'), $argument));
		}
	}

	protected function help()
	{
		$this
			->writeMessage(sprintf($this->locale->_('Usage: %s [options]'), $this->getName()))
			->writeMessage(sprintf($this->locale->_('Phar generator of \mageekguy\atoum version %s'), self::version))
			->writeMessage($this->locale->_('Available options are:'))
		;

		$options = array(
			'-h, --help' => $this->locale->_('Display this help'),
			'-d <dir>, --directory <dir>' => $this->locale->_('Destination directory <dir>')
		);

		$this->writeLabels($options);

		return $this;
	}

	protected function generate()
	{
		if ($this->originDirectory === null)
		{
			throw new \runtimeException(sprintf($this->locale->_('Origin directory must be defined'), $this->originDirectory));
		}

		if ($this->destinationDirectory === null)
		{
			throw new \runtimeException(sprintf($this->locale->_('Destination directory must be defined'), $this->originDirectory));
		}

		if ($this->stubFile === null)
		{
			throw new \runtimeException(sprintf($this->locale->_('Stub file must be defined'), $this->originDirectory));
		}

		if ($this->adapter->is_readable($this->originDirectory) === false)
		{
			throw new \runtimeException(sprintf($this->locale->_('Origin directory \'%s\' is not readable'), $this->originDirectory));
		}

		if ($this->adapter->is_writable($this->destinationDirectory) === false)
		{
			throw new \runtimeException(sprintf($this->locale->_('Destination directory \'%s\' is not writable'), $this->destinationDirectory));
		}

		if ($this->adapter->is_readable($this->stubFile) === false)
		{
			throw new \runtimeException(sprintf($this->locale->_('Stub file \'%s\' is not readable'), $this->stubFile));
		}

		$phar = $this->getPhar($this->destinationDirectory . DIRECTORY_SEPARATOR . self::phar);

		if ($phar instanceof \phar === false)
		{
			throw new \logicException('Phar injecter must return a \phar instance');
		}

		$fileIterator = $this->getFileIterator($this->originDirectory);

		if ($fileIterator instanceof \iterator === false)
		{
			throw new \logicException('File iterator injecter must return a \iterator instance');
		}

		$description = $this->adapter->file_get_contents($this->originDirectory . DIRECTORY_SEPARATOR . 'ABOUT');

		if ($description === false)
		{
			throw new \runtimeException(sprintf($this->locale->_('ABOUT file is missing in \'%s\''), $this->originDirectory));
		}

		$licence = $this->adapter->file_get_contents($this->originDirectory . DIRECTORY_SEPARATOR . 'COPYING');

		if ($licence === false)
		{
			throw new \runtimeException(sprintf($this->locale->_('COPYING file is missing in \'%s\''), $this->originDirectory));
		}

		$phar->setStub($this->adapter->file_get_contents($this->stubFile));

		$phar->setMetadata(array(
				'version' => atoum\test::getVersion(),
				'author' => atoum\test::author,
				'support' => self::mail,
				'repository' => self::repository,
				'description' => $description,
				'licence' => $licence
			)
		);

		$phar->buildFromIterator($fileIterator, $this->originDirectory);

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
