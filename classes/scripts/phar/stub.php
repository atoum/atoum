<?php

namespace mageekguy\atoum\scripts\phar;

use
	\mageekguy\atoum,
	\mageekguy\atoum\scripts,
	\mageekguy\atoum\exceptions
;

class stub extends atoum\script
{
	const defaultScript = 'runner';
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

		return parent::run($arguments);
	}

	public function help(array $options = array())
	{
		$this
			->writeMessage(sprintf($this->locale->_('Usage: %s [options]'), $this->getName()) . PHP_EOL)
			->writeMessage($this->locale->_('Available options are:') . PHP_EOL)
		;

		$this->writeLabels(
			array_merge(
				array(
					'-i, --infos' => $this->locale->_('Display informations'),
					'-s, --signature' => $this->locale->_('Display phar signature'),
					'-e <dir>, --extract <dir>' => $this->locale->_('Extract all file from phar in <dir>'),
					'--testIt' => $this->locale->_('Execute all Atoum unit tests'),
					'-u <script> <args>, --use <script> <args>' => $this->locale->_('Run script <script> from PHAR with <args> as arguments (this argument must be the first)')
				)
				,
				$options
			)
		);

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
