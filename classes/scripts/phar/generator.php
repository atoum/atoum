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
	const phar = 'mageekguy.atoum.phar';

	protected $generate = true;
	protected $originDirectory = null;
	protected $destinationDirectory = null;
	protected $stubFile = null;

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

	public function run(array $arguments = array())
	{
		$this->generate = true;

		parent::run($arguments);

		if ($this->generate === true)
		{
			$this->generate();
		}

		return $this;
	}

	public function help()
	{
		$this->generate = false;

		return parent::help();
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

		$phar = $this->factory->build('phar', array($pharFile));

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
		else if (DIRECTORY_SEPARATOR == '/' && $path != '/')
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

?>
