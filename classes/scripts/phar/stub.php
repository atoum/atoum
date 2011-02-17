<?php

namespace mageekguy\atoum\scripts\phar;

use \mageekguy\atoum;
use \mageekguy\atoum\exceptions;

class stub extends atoum\script
{
	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $locale, $adapter);
	}

	public function run(array $arguments = array())
	{
		if (realpath($_SERVER['argv'][0]) !== $this->getName())
		{
			require_once(\phar::running() . '/scripts/runner.php');
		}
		else
		{
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

					$script->infos();
				},
				array('-i', '--infos')
			);

			$this->argumentsParser->addHandler(
				function($script, $argument, $values) {
					if (sizeof($values) !== 0)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->signature();
				},
				array('-s', '--signature')
			);

			$this->argumentsParser->addHandler(
				function($script, $argument, $values) {
					if (sizeof($values) !== 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->extractTo($values[0]);
				},
				array('-e', '--extractTo')
			);

			$this->argumentsParser->addHandler(
				function($script, $argument, $values) {
					if (sizeof($values) !== 0)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->testIt();
				},
				array('--testIt')
			);

			$this->argumentsParser->addHandler(
				function($script, $argument, $files) {
					if (sizeof($files) <= 0)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->executeTestFiles($files);
				},
				array('-t', '--test-files')
			);

			$this->argumentsParser->addHandler(
				function($script, $argument, $directories) {
					if (sizeof($directories) <= 0)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->executeDirectories($directories);
				},
				array('-d', '--directories')
			);

			parent::run($arguments);
		}

		return $this;
	}

	public function help()
	{
		$this->writeMessage(sprintf($this->locale->_('Usage: %s [options]') . PHP_EOL, $this->getName()));
		$this->writeMessage(sprintf($this->locale->_('Atoum version %s by %s.'), atoum\test::getVersion(), atoum\test::author) . PHP_EOL);
		$this->writeMessage($this->locale->_('Available options are:') . PHP_EOL);

		$options = array(
			'-h, --help' => $this->locale->_('Display this help'),
			'-v, --version' => $this->locale->_('Display version'),
			'-i, --infos' => $this->locale->_('Display informations'),
			'-s, --signature' => $this->locale->_('Display phar signature'),
			'-e <dir>, --extract <dir>' => $this->locale->_('Extract all file from phar in <dir>'),
			'-t <files>, --test-files <files>' => $this->locale->_('Use test files'),
			'-d <directories>, --directories <directories>' => $this->locale->_('Use test files in directories'),
			'--testIt' => $this->locale->_('Execute all Atoum unit tests')
		);

		$this->writeLabels($options);

		return $this;
	}

	public function version()
	{
		$this->writeMessage(sprintf($this->locale->_('Atoum version %s by %s.'), atoum\test::getVersion(), atoum\test::author) . PHP_EOL);

		return $this;
	}

	public function infos()
	{
		$phar = new \phar(\phar::running());

		$this->writeMessage($this->locale->_('Informations:') . PHP_EOL);
		$this->writeLabels($phar->getMetadata());

		return $this;
	}

	public function signature()
	{
		$phar = new \phar(\phar::running());

		$signature = $phar->getSignature();

		$this->writeLabel($this->locale->_('Signature'), $signature['hash']);

		return $this;
	}

	public function extractTo($directory)
	{
		if (is_dir($directory) === false)
		{
			throw new exceptions\logic('Path \'' . $directory . '\' is not a directory');
		}

		if (is_writable($directory) === false)
		{
			throw new exceptions\logic('Directory \'' . $directory . '\' is not writable');
		}

		$phar = new \phar($this->getName());

		$phar->extractTo($directory);

		return $this;
	}

	public function testIt()
	{
		foreach (new \recursiveIteratorIterator(new atoum\runner\directory\filter(new \recursiveDirectoryIterator(\phar::running() . '/tests/units/classes'))) as $file)
		{
			require_once($file->getPathname());
		}

		return $this;
	}

	public function executeTestFiles(array $files)
	{
		require_once(\phar::running() . '/../../scripts/runner.php');

		foreach ($files as $file)
		{
			$file = realpath($file);

			if ($file === false)
			{
				throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Path \'%s\' is invalid'), $file));
			}

			if (is_file($file) === false)
			{
				throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Path \'%s\' is not a file'), $file));
			}

			if (is_readable($file) === false)
			{
				throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Unable to read test file \'%s\''), $file));
			}

			require_once($file);
		}

		return $this;
	}

	public function executeDirectories(array $directories)
	{
		require_once(\phar::running() . '/../../scripts/runner.php');

		foreach ($directories as $directory)
		{
			$directory = realpath($directory);

			if ($directory === false)
			{
				throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Path \'%s\' is invalid'), $directory));
			}

			if (is_dir($directory) === false)
			{
				throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Path \'%s\' is not a directory'), $directory));
			}

			if (is_readable($directory) === false)
			{
				throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Unable to read directory \'%s\''), $directory));
			}

			foreach (new \recursiveIteratorIterator(new \mageekguy\atoum\runner\directory\filter(new \recursiveDirectoryIterator($directory))) as $file)
			{
				require_once($file->getPathname());
			}
		}

		return $this;
	}
}

?>
