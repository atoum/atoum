<?php

namespace mageekguy\atoum\scripts\phar;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts,
	mageekguy\atoum\exceptions
;

class stub extends scripts\runner
{
	const scriptsDirectory = 'scripts';
	const scriptsExtension = '.php';

	public function listScripts()
	{
		$this->writeMessage($this->locale->_('Available scripts are:') . PHP_EOL);
		$this->writeMessage(self::padding . 'builder');
		$this->writeMessage(self::padding . 'tagger');

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

		require_once $scriptFile;

		exit(0);
	}

	public function infos()
	{
		$phar = new \phar($this->getName());

		$this
			->writeMessage($this->locale->_('Informations:') . PHP_EOL)
			->writeLabels($phar->getMetadata())
		;

		$this->runTests = false;

		return $this;
	}

	public function signature()
	{
		$phar = new \phar($this->getName());

		$signature = $phar->getSignature();

		$this->writeLabel($this->locale->_('Signature'), $signature['hash']);

		$this->runTests = false;

		return $this;
	}

	public function extractTo($directory)
	{
		$phar = new \phar($this->getName());

		try
		{
			$phar->extractTo($directory);
		}
		catch (\exception $exception)
		{
			throw new exceptions\logic('Unable to extract in \'' . $directory . '\'');
		}

		$this->runTests = false;

		return $this;
	}

	public function extractResourcesTo($directory)
	{
		$phar = new \phar($this->getName());

		if (isset($phar['resources']) === false)
		{
			throw new exceptions\logic('Resources directory does not exist in PHAR \'' . $this->getName() . '\'');
		}

		try
		{
			foreach (new \recursiveIteratorIterator(new \recursiveDirectoryIterator($phar['resources'], \filesystemIterator::CURRENT_AS_SELF)) as $resources)
			{
				if ($resources->current()->isFile() === true)
				{
					$phar->extractTo($directory, 'resources/' . $resources->getSubpathname());
				}
			}
		}
		catch (\exception $exception)
		{
			throw new exceptions\logic('Unable to extract resources in \'' . $directory . '\'');
		}

		$this->runTests = false;

		return $this;
	}

	public function useDefaultConfigFile()
	{
		try
		{
			$this->useConfigFile(dirname($this->getName()) . '/' . self::defaultConfigFile);
		}
		catch (\exception $exception) {};

		return $this;
	}

	protected function setArgumentHandlers()
	{
		parent::setArgumentHandlers();

		$this->addArgumentHandler(
			function($script, $argument, $values) {
				if (sizeof($values) !== 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->infos();
			},
			array('-i', '--infos'),
			null,
			$this->locale->_('Display informations, do not run any script')
		);

		$this->addArgumentHandler(
			function($script, $argument, $values) {
				if (sizeof($values) !== 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->signature();
			},
			array('-s', '--signature'),
			null,
			$this->locale->_('Display phar signature, do not run any script')
		);

		$this->addArgumentHandler(
			function($script, $argument, $values) {
				if (sizeof($values) !== 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->extractTo($values[0]);
			},
			array('-e', '--extractTo'),
			'<directory>',
			$this->locale->_('Extract all file from phar to <directory>, do not run any script')
		);

		$this->addArgumentHandler(
			function($script, $argument, $values) {
				if (sizeof($values) !== 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->extractResourcesTo($values[0]);
			},
			array('-er', '--extractResourcesTo'),
			'<directory>',
			$this->locale->_('Extract resources from phar to <directory>, do not run any script')
		);

		$this->addArgumentHandler(
	      function($script, $argument, $values, $position) {
				if ($position !== 1 || sizeof($values) !== 1)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				unset($_SERVER['argv'][1]);
				unset($_SERVER['argv'][2]);

				$script->useScript($values[0]);

			},
			array('-u', '--use'),
			'<script> <args>',
			$this->locale->_('Run script <script> from PHAR with <args> as arguments (this argument must be the first)')
		);

		$this->addArgumentHandler(
	      function($script, $argument, $values) {
				if (sizeof($values) > 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->listScripts();
			},
			array('-ls', '--list-scripts'),
			null,
			$this->locale->_('List available scripts')
		);

		return $this;
	}

	protected static function getScriptFile($scriptName)
	{
		return \Phar::running() . '/' . self::scriptsDirectory . '/' . $scriptName . self::scriptsExtension;
	}
}

?>
