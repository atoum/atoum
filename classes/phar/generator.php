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
	protected $fromDirectory = null;
	protected $destinationDirectory = null;

	public function setFromDirectory($directory)
	{
		$this->fromDirectory = self::cleanPath($directory);

		if ($this->fromDirectory == '')
		{
			throw new \logicException('Empty path is invalid');
		}

		return $this;
	}

	public function getFromDirectory()
	{
		return $this->fromDirectory;
	}

	public function setDestinationDirectory($directory)
	{
		$this->destinationDirectory = self::cleanPath($directory);

		return $this;
	}

	public function getDestinationDirectory()
	{
		return $this->destinationDirectory;
	}

	public function run()
	{
		parent::run();

		if ($this->help === true)
		{
			$this->help();
		}

		if ($this->destinationDirectory !== false)
		{
			$this->generate();
		}

		return $this;
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
		$this->fromDirectory = self::cleanPath($this->fromDirectory);

		self::checkDirectory($this->fromDirectory);
		self::checkDirectory($this->destinationDirectory);

		if (is_readable($this->fromDirectory) === false)
		{
			throw new \logicException(sprintf($this->locale->_('Directory \'%s\' is not readable'), $this->fromDirectory));
		}

		if (is_writable($this->destinationDirectory) === false)
		{
			throw new \logicException(sprintf($this->locale->_('Directory \'%s\' is not writable'), $this->destinationDirectory));
		}

		$phar = new \Phar($this->destinationDirectory . DIRECTORY_SEPARATOR . self::phar);

		$phar->setStub('<?php Phar::mapPhar(\'' . self::phar . '\'); require(\'phar://' . self::phar . '/classes/autoloader.php\'); if (PHP_SAPI === \'cli\') { $stub = new \mageekguy\atoum\phar\stub(__FILE__); $stub->run(); } __HALT_COMPILER(); ?>');
		$phar->setMetadata(array(
				'version' => atoum\test::getVersion(),
				'author' => atoum\test::author,
				'support' => self::mail,
				'repository' => self::repository,
				'description' => file_get_contents($this->fromDirectory . DIRECTORY_SEPARATOR . 'ABOUT'),
				'licence' => file_get_contents($this->fromDirectory . DIRECTORY_SEPARATOR . 'COPYING')
			)
		);

		$phar->buildFromDirectory($this->fromDirectory, '/\.php$/');
		$phar->setSignatureAlgorithm(\Phar::SHA1);
		$phar->compressFiles(\Phar::GZ);
	}

	protected static function cleanPath($path)
	{
		return $path == '/' ? $path : rtrim($path, DIRECTORY_SEPARATOR);
	}

	protected static function checkDirectory($directory)
	{
		if (is_dir($directory) === false)
		{
			throw new \logicException('Path \'' . $directory . '\' is not a directory');
		}
	}
}

?>
