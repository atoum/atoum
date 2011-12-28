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
	const updateUrl = 'http://downloads.atoum.org/update.php?version=%s';

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

	public function version()
	{
		$this
			->writeMessage(sprintf($this->locale->_('atoum version %s by %s (%s)'), atoum\version, atoum\author, \phar::running()) . PHP_EOL)
		;

		$this->runTests = false;

		return $this;
	}

	public function update()
	{
		if ($this->adapter->ini_get('phar.readonly') == true)
		{
			throw new exceptions\runtime('Unable to update the PHAR Archive, phar.readonly is set, use \'-d phar.readonly=0\'');
		}

		if ($this->adapter->ini_get('allow_url_fopen') == false)
		{
			throw new exceptions\runtime('Unable to update the PHAR Archive, allow_url_fopen is not set, use \'-d allow_url_fopen=1\'');
		}

		$this->writeMessage($this->locale->_('Checking if a new version is available...'));

		$data = json_decode($this->adapter->file_get_contents(sprintf(self::updateUrl, atoum\version)), true);

		if (is_array($data) === false || isset($data['version']) === false || isset($data['phar']) === false)
		{
			$this->writeMessage($this->locale->_('There is no new version available'));
		}
		else
		{
			$tmpFile = $this->adapter->realpath($this->adapter->sys_get_temp_dir()) . '/' . md5($data['version']) . '.phar';

			if ($this->adapter->file_put_contents($tmpFile, utf8_decode($data['phar'])) === false)
			{
				throw new exceptions\runtime('Unable to create temporary file to update to version \'' . $data['version']);
			}

			$currentPhar = $this->factory->build('phar', array($this->getName()));

			$newPharIterator = new \recursiveIteratorIterator(new \recursiveDirectoryIterator('phar://' . $tmpFile . '/1'));

			$size = 0;

			foreach ($newPharIterator as $file)
			{
				$size++;
			}

			$progressBar = $this->factory->build('atoum\cli\progressBar', array($size));

			$this->writeMessage(sprintf($this->locale->_('Update files to version \'%s\'...'), $data['version']));

			$this->outputWriter->write($progressBar);

			$newCurrentDirectory = atoum\phar\currentDirectory + 1;

			foreach ($newPharIterator as $newFile)
			{
				$currentPhar[$newCurrentDirectory . '/' . preg_replace('#^phar://' . preg_quote($tmpFile) . '/#', '', (string) $newFile)] = $this->adapter->file_get_contents($newFile);

				$this->outputWriter->write($progressBar->refresh('='));
			}

			$this->outputWriter->write(PHP_EOL);

			$this->writeMessage(sprintf($this->locale->_('Atoum was updated to version \'%s\' successfully'), $data['version']));

			@$this->adapter->unlink($tmpFile);

			$currentPhar->setStub(preg_replace("#('\\\phar\\\currentDirectory',\s+')[^']+(')#", ('${1}' . $newCurrentDirectory . '${2}'), $currentPhar->getStub()));
		}

		$this->runTests = false;

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

		$this->addArgumentHandler(
	      function($script, $argument, $values) {
				if (sizeof($values) > 0)
				{
					throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
				}

				$script->update();
			},
			array('--update'),
			null,
			$this->locale->_('Update atoum')
		);

		return $this;
	}

	protected static function getScriptFile($scriptName)
	{
		return atoum\directory . '/' . self::scriptsDirectory . '/' . $scriptName . self::scriptsExtension;
	}
}

?>
