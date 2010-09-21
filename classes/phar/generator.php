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

	public function setOriginDirectory($directory)
	{
		$originDirectory = $this->cleanPath($directory);

		if ($originDirectory == '')
		{
			throw new \logicException('Empty origin directory is invalid');
		}
		else if ($this->adapter->is_dir($originDirectory) === false)
		{
			throw new \logicException('Path \'' . $originDirectory . '\' of origin directory is invalid');
		}
		else if ($this->destinationDirectory !== null && $originDirectory === $this->destinationDirectory)
		{
			throw new \logicException('Origin directory must be different from destination directory');
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
			throw new \logicException('Empty destination directory is invalid');
		}
		else if ($this->adapter->is_dir($destinationDirectory) === false)
		{
			throw new \logicException('Path \'' . $destinationDirectory . '\' of destination directory is invalid');
		}
		else if ($this->originDirectory !== null && $destinationDirectory === $this->originDirectory)
		{
			throw new \logicException('Destination directory must be different from origin directory');
		}
		else if (strpos($destinationDirectory, $this->originDirectory) === 0)
		{
			throw new \logicException('Origin directory must not include destination directory');
		}

		$this->destinationDirectory = $destinationDirectory;

		return $this;
	}

	public function getDestinationDirectory()
	{
		return $this->destinationDirectory;
	}

	public function run()
	{
		parent::run();

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
		self::writeMessage(sprintf($this->locale->_('Usage: %s [options]'), $this->getName()));
		self::writeMessage(sprintf($this->locale->_('Phar generator of \mageekguy\atoum version %s'), self::version));
		self::writeMessage($this->locale->_('Available options are:'));

		$options = array(
			'-h, --help' => $this->locale->_('Display this help'),
			'-d <dir>, --directory <dir>' => $this->locale->_('Destination directory <dir>')
		);

		self::writeLabels($options);

		return $this;
	}

	protected function generate()
	{
		if ($this->originDirectory === null)
		{
			throw new \logicException(sprintf($this->locale->_('Origin directory must be defined'), $this->originDirectory));
		}
		else if ($this->destinationDirectory === null)
		{
			throw new \logicException(sprintf($this->locale->_('Destination directory must be defined'), $this->originDirectory));
		}
		else if ($this->adapter->is_readable($this->originDirectory) === false)
		{
			throw new \logicException(sprintf($this->locale->_('Origin directory \'%s\' is not readable'), $this->originDirectory));
		}
		else if ($this->adapter->is_writable($this->destinationDirectory) === false)
		{
			throw new \logicException(sprintf($this->locale->_('Destination directory \'%s\' is not writable'), $this->destinationDirectory));
		}

		try
		{
			$phar = new \phar($this->destinationDirectory . DIRECTORY_SEPARATOR . self::phar);

			$phar->setStub('<?php Phar::mapPhar(\'' . self::phar . '\'); require(\'phar://' . self::phar . '/classes/autoloader.php\'); if (PHP_SAPI === \'cli\') { $stub = new \mageekguy\atoum\phar\stub(__FILE__); $stub->run(); } __HALT_COMPILER(); ?>');
			$phar->setMetadata(array(
					'version' => atoum\test::getVersion(),
					'author' => atoum\test::author,
					'support' => self::mail,
					'repository' => self::repository,
					'description' => file_get_contents($this->originDirectory . DIRECTORY_SEPARATOR . 'ABOUT'),
					'licence' => file_get_contents($this->originDirectory . DIRECTORY_SEPARATOR . 'COPYING')
				)
			);

			$phar->buildFromIterator(new \recursiveIteratorIterator(new \recursiveDirectoryIterator($this->originDirectory)), $this->originDirectory);
			$phar->setSignatureAlgorithm(\Phar::SHA1);
		}
		catch (\exception $exception)
		{
			throw new \logicException(sprintf($this->locale->_('Unable to create phar \'%s\' in directory \'%s\''), $this->destinationDirectory . DIRECTORY_SEPARATOR . self::phar, $this->destinationDirectory));
		}

		return $phar;
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
