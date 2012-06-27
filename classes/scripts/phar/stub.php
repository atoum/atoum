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
		if (($versions = $this->getVersions($phar = $this->factory->build('phar', array($this->getName())))) === null)
		{
			throw new exceptions\runtime('Unable to extract the PHAR to \'' . $directory . '\', the versions\'s file is invalid');
		}

		$directory = rtrim($directory, DIRECTORY_SEPARATOR);
		$pharName = \phar::running();

		foreach (new \recursiveIteratorIterator($phar) as $pharFile)
		{
			$pharFilePath = ltrim(str_replace($pharName, '', $pharFile), DIRECTORY_SEPARATOR);

			if (strpos($pharFilePath, $versions['current']) === 0)
			{
				$path = $directory . '/' . ltrim(substr($pharFilePath, strlen($versions['current'])), DIRECTORY_SEPARATOR);

				$pathDirectory = dirname($path);

				@mkdir($pathDirectory, 0777, true);

				if (is_dir($pathDirectory) === false)
				{
					throw new exceptions\runtime('Unable to create directory \'' . $pathDirectory . '\'');
				}

				$data = file_get_contents($pharFile);

				if (file_put_contents($path, $data) != strlen($data))
				{
					throw new exceptions\runtime('Unable to extract file \'' . $pharFilePath . '\' in directory \'' . $pathDirectory . '\'');
				}
			}
		}

		$this->runTests = false;

		return $this;
	}

	public function extractResourcesTo($directory)
	{
		if (($versions = $this->getVersions($phar = $this->factory->build('phar', array($this->getName())))) === null)
		{
			throw new exceptions\runtime('Unable to extract resources from PHAR in \'' . $directory . '\', the versions\'s file is invalid');
		}

		if (isset($phar[$versions['current'] . '/resources']) === false)
		{
			throw new exceptions\logic('Resources directory does not exist in PHAR \'' . $this->getName() . '\'');
		}

		$directory = rtrim($directory, DIRECTORY_SEPARATOR);
		$resourcesDirectory = 'phar://' . $this->getName() . '/' . $versions['current'] . '/resources';

		foreach (new \recursiveIteratorIterator(new \recursiveDirectoryIterator($resourcesDirectory)) as $resourceFile)
		{
			$resourceFilePath = ltrim(str_replace($resourcesDirectory, '', $resourceFile), DIRECTORY_SEPARATOR);

			$resourceFileDirectory = $directory . '/' . dirname($resourceFilePath);

			@mkdir($resourceFileDirectory, 0777, true);

			if (is_dir($resourceFileDirectory) === false)
			{
				throw new exceptions\runtime('Unable to create directory \'' . $resourceFileDirectory . '\'');
			}

			$data = file_get_contents($resourceFile);

			if (file_put_contents($directory . '/' . $resourceFilePath, $data) != strlen($data))
			{
				throw new exceptions\runtime('Unable to extract resource file \'' . $resourceFilePath . '\' in directory \'' . $directory . '\'');
			}
		}

		$this->runTests = false;

		return $this;
	}

	public function useDefaultConfigFiles($startDirectory = null)
	{
		if ($startDirectory === null)
		{
			$startDirectory = dirname($this->getName());
		}

		return parent::useDefaultConfigFiles($startDirectory);
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
			throw new exceptions\runtime('Unable to update the PHAR, phar.readonly is set, use \'-d phar.readonly=0\'');
		}

		if ($this->adapter->ini_get('allow_url_fopen') == false)
		{
			throw new exceptions\runtime('Unable to update the PHAR, allow_url_fopen is not set, use \'-d allow_url_fopen=1\'');
		}

		if (($versions = $this->getVersions($currentPhar = $this->factory->build('phar', array($this->getName())))) === null)
		{
			throw new exceptions\runtime('Unable to update the PHAR, the versions\'s file is invalid');
		}

		unset($versions['current']);

		$this->writeMessage($this->locale->_('Checking if a new version is available...'), false);

		$data = json_decode($this->adapter->file_get_contents(sprintf(self::updateUrl, json_encode(array_values($versions)))), true);

		$this
			->clearMessage()
			->writeMessage($this->locale->_('Checking if a new version is available... Done !'))
		;

		if (is_array($data) === false || isset($data['version']) === false || isset($data['phar']) === false)
		{
			$this->writeMessage($this->locale->_('There is no new version available !'));
		}
		else
		{
			$tmpFile = $this->adapter->realpath($this->adapter->sys_get_temp_dir()) . '/' . md5($data['version']) . '.phar';

			if ($this->adapter->file_put_contents($tmpFile, utf8_decode($data['phar'])) === false)
			{
				throw new exceptions\runtime('Unable to create temporary file to update to version \'' . $data['version']);
			}

			$this->writeMessage(sprintf($this->locale->_('Update to version \'%s\'...'), $data['version']), false);

			$pharPathLength = strlen($pharPath = 'phar://' . $tmpFile . '/1/');

			$newCurrentDirectory = sizeof($versions) + 1;

			$newFiles = new \arrayIterator();

			foreach (new \recursiveIteratorIterator(new \recursiveDirectoryIterator($pharPath)) as $newFile)
			{
				$newFiles[$newCurrentDirectory . '/' . substr($newFile, $pharPathLength)] = ($newFile = (string) $newFile);
			}

			$currentPhar->buildFromIterator($newFiles);

			$this
				->clearMessage()
				->writeMessage(sprintf($this->locale->_('Update to version \'%s\'... Done !'), $data['version']))
			;

			@$this->adapter->unlink($tmpFile);

			$this->writeMessage(sprintf($this->locale->_('Enable version \'%s\'...'), $data['version']), false);

			$versions[$newCurrentDirectory] = $data['version'];
			$versions['current'] = $newCurrentDirectory;

			$currentPhar['versions'] = serialize($versions);

			$this
				->clearMessage()
				->writeMessage(sprintf($this->locale->_('Enable version \'%s\'... Done !'), $data['version']))
			;

			$this->writeMessage(sprintf($this->locale->_('Atoum has been updated from version \'%s\' to \'%s\' successfully !'), atoum\version, $data['version']));
		}

		$this->runTests = false;

		return $this;
	}

	public function listAvailableVersions()
	{
		$currentPhar = $this->factory->build('phar', array($this->getName()));

		if (isset($currentPhar['versions']) === false)
		{
			throw new exceptions\runtime('Unable to list available versions in PHAR, the versions\'s file does not exist');
		}

		$versions = unserialize(file_get_contents($currentPhar['versions']));

		if (is_array($versions) === false || sizeof($versions) <= 0 || isset($versions['current']) === false)
		{
			throw new exceptions\runtime('Unable to list available versions in PHAR, the versions\'s file is invalid');
		}

		$currentDirectory = $versions['current'];

		unset($versions['current']);

		asort($versions);

		foreach ($versions as $directory => $version)
		{
			$this->writeMessage(($directory == $currentDirectory ? '*' : ' ') . ' ' . $version);
		}

		$this->runTests = false;

		return $this;
	}

	public function enableVersion($versionName, \phar $phar = null)
	{
		if ($this->adapter->ini_get('phar.readonly') == true)
		{
			throw new exceptions\runtime('Unable to update the PHAR, phar.readonly is set, use \'-d phar.readonly=0\'');
		}

		if ($phar === null)
		{
			$phar = $this->factory->build('phar', array($this->getName()));
		}

		if (($versions = $this->getVersions($phar)) === null)
		{
			throw new exceptions\runtime('Unable to enable version \'' . $versionName . '\', the versions\'s file is invalid');
		}

		$versionDirectory = array_search($versionName, $versions);

		if ($versionDirectory === false)
		{
			throw new exceptions\runtime('Unable to enable version \'' . $versionName . '\' because it does not exist');
		}

		$versions['current'] = $versionDirectory;

		$phar['versions'] = serialize($versions);

		$this->runTests = false;

		return $this;
	}

	public function deleteVersion($versionName, \phar $phar = null)
	{
		if ($this->adapter->ini_get('phar.readonly') == true)
		{
			throw new exceptions\runtime('Unable to update the PHAR, phar.readonly is set, use \'-d phar.readonly=0\'');
		}

		if ($phar === null)
		{
			$phar = $this->factory->build('phar', array($this->getName()));
		}

		if (($versions = $this->getVersions($phar)) === null)
		{
			throw new exceptions\runtime('Unable to delete version \'' . $versionName . '\', the versions\'s file is invalid');
		}

		$versionDirectory = array_search($versionName, $versions);

		if ($versionDirectory === false)
		{
			throw new exceptions\runtime('Unable to delete version \'' . $versionName . '\' because it does not exist');
		}

		if ($versionDirectory == $versions['current'])
		{
			throw new exceptions\runtime('Unable to delete version \'' . $versionName . '\' because it is the current version');
		}

		unset($versions[$versionDirectory]);
		unset($phar[$versionDirectory]);

		$currentVersion = $versions['current'];
		unset($versions['current']);

		$versions = array_values($versions);
		$versions['current'] = array_search($currentVersion, $versions);

		$phar['versions'] = serialize($versions);

		$this->runTests = false;

		return $this;
	}

	protected function setArgumentHandlers()
	{
		return
			parent::setArgumentHandlers()
			->addArgumentHandler(
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
			)
			->addArgumentHandler(
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
			)
			->addArgumentHandler(
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
			)
			->addArgumentHandler(
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
			)
			->addArgumentHandler(
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
				$this->locale->_('Run script <script> from PHAR with <args> as arguments (this argument must be the first)'),
				4
			)
			->addArgumentHandler(
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
			)
			->addArgumentHandler(
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
			)
			->addArgumentHandler(
				function($script, $argument, $values) {
					if (sizeof($values) > 0)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->listAvailableVersions();
				},
				array('-lav', '--list-available-versions'),
				null,
				$this->locale->_('List available versions in the PHAR')
			)
			->addArgumentHandler(
				function($script, $argument, $values) {
					if (sizeof($values) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->enableVersion($values[0]);
				},
				array('-ev', '--enable-version'),
				'<version>',
				$this->locale->_('Enable version <version>')
			)
			->addArgumentHandler(
				function($script, $argument, $values) {
					if (sizeof($values) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->deleteVersion($values[0]);
				},
				array('-dv', '--delete-version'),
				'<version>',
				$this->locale->_('Delete version <version>')
			)
		;

		return $this;
	}

	protected function getVersions(\phar $phar)
	{
		if (isset($phar['versions']) === false)
		{
			throw new exceptions\runtime('The versions\'s file does not exist');
		}

		$versions = unserialize($this->adapter->file_get_contents($phar['versions']));

		return ((is_array($versions) === false || isset($versions['current']) === false || isset($versions[$versions['current']]) === false) ? null : $versions);
	}

	protected static function extractFilesTo(\recursiveDirectoryIterator $fromPharDirectory, $toDirectory)
	{
		$directory = rtrim($directory, DIRECTORY_SEPARATOR);
		$pharName = \phar::running();

		foreach (new \recursiveIteratorIterator($fromPharDirectory) as $pharFile)
		{
			$pharFilePath = ltrim(str_replace($pharName, '', $pharFile), DIRECTORY_SEPARATOR);

			if (strpos($pharFilePath, $versions['current']) === 0)
			{
				$path = $directory . '/' . ltrim(substr($pharFilePath, strlen($versions['current'])), DIRECTORY_SEPARATOR);

				$pathDirectory = dirname($path);

				@mkdir($pathDirectory, 0777, true);

				if (is_dir($pathDirectory) === false)
				{
					throw new exceptions\runtime('Unable to create directory \'' . $pathDirectory . '\'');
				}

				$data = file_get_contents($pharFile);

				if (file_put_contents($path, $data) != strlen($data))
				{
					throw new exceptions\runtime('Unable to extract file \'' . $pharFilePath . '\' in directory \'' . $pathDirectory . '\'');
				}
			}
		}

		return $this;
	}

	protected static function getScriptFile($scriptName)
	{
		return atoum\directory . '/' . self::scriptsDirectory . '/' . $scriptName . self::scriptsExtension;
	}
}
