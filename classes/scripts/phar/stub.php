<?php

namespace mageekguy\atoum\scripts\phar;

use
	\mageekguy\atoum,
	\mageekguy\atoum\scripts,
	\mageekguy\atoum\exceptions
;

class stub extends scripts\runner
{
	const scriptsDirectory = 'scripts';
	const scriptsExtension = '.php';

	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $locale, $adapter);
	}

	public function run(array $arguments = array())
	{
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
	      function($script, $argument, $values, $position) {
				if ($position !== 1 || sizeof($values) !== 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->useScript($values[0]);
			},
			array('-u', '--use')
		);

		$this->argumentsParser->addHandler(
	      function($script, $argument, $values) {
				if (sizeof($values) > 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->listScripts();
			},
			array('-ls', '--list-scripts')
		);

		parent::run($arguments);

		return $this;
	}

	public function help(array $options = array())
	{
		return parent::help(array(
				'-i, --infos' => $this->locale->_('Display informations, do not run any script'),
				'-s, --signature' => $this->locale->_('Display phar signature, do not run any script'),
				'-e <dir>, --extract <dir>' => $this->locale->_('Extract all file from phar in <dir>, do not run any script'),
				'--testIt' => $this->locale->_('Execute all Atoum unit tests, do not run default script'),
				'-u <script> <args>, --use <script> <args>' => $this->locale->_('Run script <script> from PHAR with <args> as arguments (this argument must be the first)'),
				'-ls, --list-scripts' => $this->locale->_('List available scripts')
			)
		);
	}

	public function listScripts()
	{
		$this->writeMessage($this->locale->_('Available scripts are:') . PHP_EOL);

		foreach (new \directoryIterator(\phar::running() . '/' . self::scriptsDirectory) as $inode)
		{
			if ($inode->isFile() === true)
			{
				$this->writeMessage(self::padding . $inode->getBasename(self::scriptsExtension));
			}
		}

		$this->runTests = false;

		return $this;
	}

	public function useScript($script)
	{
		$scriptFile = self::getScriptFile($script);

		if (file_exists($scriptFile) === false)
		{
			throw new exceptions\logic\invalidArgument(sprintf($this->getLocale()->_('Script %s does not exist'), $script));
		}

		require_once($scriptFile);

		exit(0);
	}

	public function infos()
	{
		$phar = new \phar(\phar::running());

		$this
			->writeMessage($this->locale->_('Informations:') . PHP_EOL)
			->writeLabels($phar->getMetadata())
		;

		$this->runTests = false;

		return $this;
	}

	public function signature()
	{
		$phar = new \phar(\phar::running());

		$signature = $phar->getSignature();

		$this->writeLabel($this->locale->_('Signature'), $signature['hash']);

		$this->runTests = false;

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

		$this->runTests = false;

		return $this;
	}

	public function testIt()
	{
		foreach (new \recursiveIteratorIterator(new atoum\src\iterator\filter(new \recursiveDirectoryIterator(\phar::running() . '/tests/units/classes'))) as $file)
		{
			require_once($file->getPathname());
		}

		return $this;
	}

	protected static function getScriptFile($scriptName)
	{
		return \Phar::running() . '/' . self::scriptsDirectory . '/' . $scriptName . self::scriptsExtension;
	}
}

?>
