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
	const githubUpdateUrl = 'https://api.github.com/repos/atoum/atoum/releases';

	protected $pharFactory = null;

	public function __construct($name, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $adapter);

		$this->setPharFactory();
	}

	public function setPharFactory(\closure $factory = null)
	{
		$this->pharFactory = $factory ?: function($path) { return new \phar($path); };

		return $this;
	}

	public function getPharFactory()
	{
		return $this->pharFactory;
	}

	public function listScripts()
	{
		$this->writeMessage($this->locale->_('Available scripts are:') . PHP_EOL);
		$this->writeMessage(self::padding . 'builder' . PHP_EOL);
		$this->writeMessage(self::padding . 'tagger' . PHP_EOL);
		$this->writeMessage(self::padding . 'treemap' . PHP_EOL);
		$this->writeMessage(self::padding . 'coverage' . PHP_EOL);

		return $this->stopRun();
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
		$phar = call_user_func($this->pharFactory, $this->getName());

		$this->writeLabels($phar->getMetadata());

		return $this->stopRun();
	}

	public function signature()
	{
		$phar = call_user_func($this->pharFactory, $this->getName());

		$signature = $phar->getSignature();

		$this->writeLabel($this->locale->_('Signature'), $signature['hash']);

		return $this->stopRun();
	}

	public function extractTo($directory)
	{
		if (($versions = $this->getVersions($phar = call_user_func($this->pharFactory, $this->getName()))) === null)
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

		return $this->stopRun();
	}

	public function extractResourcesTo($directory)
	{
		$resourcesDirectory = $this->getResourcesDirectory();
		$directory = rtrim($directory, DIRECTORY_SEPARATOR);

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

		return $this->stopRun();
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
			->writeInfo($this->locale->_('atoum version %s by %s (%s)', atoum\version, atoum\author, \phar::running()))
		;

		return $this->stopRun();
	}

	public function updateFromGithub()
	{
		if ($this->adapter->ini_get('phar.readonly') == true)
		{
			throw new exceptions\runtime('Unable to update the PHAR, phar.readonly is set, use \'-d phar.readonly=0\'');
		}

		if ($this->adapter->ini_get('allow_url_fopen') == false)
		{
			throw new exceptions\runtime('Unable to update the PHAR, allow_url_fopen is not set, use \'-d allow_url_fopen=1\'');
		}

		if (($versions = $this->getVersions($currentPhar = call_user_func($this->pharFactory, $this->getName()))) === null)
		{
			throw new exceptions\runtime('Unable to update the PHAR, the versions\'s file is invalid');
		}

		$this->writeMessage($this->locale->_('Checking if a new version is available on Github...'), false);

		$httpContext = stream_context_create(array(
			'http' => array(
					'method' => 'GET',
					'protocol_version' => '1.1',
					'header' => "Accept: */*\r\nUser-Agent:atoum\r\nCache-Control: no-cache"
			)
		));
		$data = json_decode($this->adapter->file_get_contents(self::githubUpdateUrl, false, $httpContext), true);

		$this
			->clearMessage()
			->writeMessage($this->locale->_('Checking if a new version is available Github... Done!' . PHP_EOL))
		;

		if (is_array($data) === false || sizeof($data) === 0)
		{
			$this->writeInfo($this->locale->_('There is no new version available!'));
		}
		else
		{
			$data = array_filter($data, function ($release) { return $release['draft'] === false; });

			$release = array_shift($data);

			if (is_array($release) === false || isset($release['name']) === false || version_compare($versions[$versions['current']], $release['name']) >= 0)
			{
				$this->writeInfo($this->locale->_('There is no new version available!'));
			}
			else
			{
				$assets = array_filter($release['assets'], function ($asset) { return $asset['name'] === 'atoum.phar'; });
				$asset = array_shift($assets);

				$assetData = json_decode($this->adapter->file_get_contents($asset['url'], false, $httpContext), true);

				if (is_array($assetData) === false || isset($assetData['browser_download_url']) === false)
				{
					$this->writeInfo($this->locale->_('There is no new version available!'));
				}
				else
				{
					$this->downloadPhar($release['name'], $currentPhar, $this->adapter->file_get_contents($assetData['browser_download_url'], false, $httpContext));
				}
			}
		}

		return $this->stopRun();
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

		if (($versions = $this->getVersions($currentPhar = call_user_func($this->pharFactory, $this->getName()))) === null)
		{
			throw new exceptions\runtime('Unable to update the PHAR, the versions\'s file is invalid');
		}

		unset($versions['current']);

		$this->writeMessage($this->locale->_('Checking if a new version is available...'), false);

		$data = json_decode($this->adapter->file_get_contents(sprintf(self::updateUrl, json_encode(array_values($versions)))), true);

		$this
			->clearMessage()
			->writeMessage($this->locale->_('Checking if a new version is available... Done!' . PHP_EOL))
		;

		if (is_array($data) === false || isset($data['version']) === false || isset($data['phar']) === false)
		{
			$this->writeInfo($this->locale->_('There is no new version available!'));
		}
		else
		{
			$this->downloadPhar($data['version'], $currentPhar, utf8_decode($data['phar']));
		}

		return $this->stopRun();
	}

	private function downloadPhar($newVersion, $currentPhar, $newPhar)
	{
		$tmpFile = $this->adapter->realpath($this->adapter->sys_get_temp_dir()) . '/' . md5($newVersion) . '.phar';

		if ($this->adapter->file_put_contents($tmpFile, $newPhar) === false)
		{
			throw new exceptions\runtime('Unable to create temporary file to update to version \'' . $newVersion);
		}

		$this->writeMessage($this->locale->_('Update to version \'%s\'...', $newVersion), false);

		$pharPathLength = strlen($pharPath = 'phar://' . $tmpFile . '/1/');

		$newCurrentDirectory = 1;

		while (isset($versions[$newCurrentDirectory]) === true)
		{
			$newCurrentDirectory++;
		}

		$newFiles = new \arrayIterator();

		foreach (new \recursiveIteratorIterator(new \recursiveDirectoryIterator($pharPath)) as $newFile)
		{
			$newFiles[$newCurrentDirectory . '/' . substr($newFile, $pharPathLength)] = ($newFile = (string) $newFile);
		}

		$currentPhar->buildFromIterator($newFiles);

		$this
			->clearMessage()
			->writeMessage($this->locale->_('Update to version \'%s\'... Done!' . PHP_EOL, $newVersion))
		;

		@$this->adapter->unlink($tmpFile);

		$this->writeMessage($this->locale->_('Enable version \'%s\'...', $newVersion), false);

		$versions[$newCurrentDirectory] = $newVersion;
		$versions['current'] = $newCurrentDirectory;

		$currentPhar['versions'] = serialize($versions);

		$this
			->clearMessage()
			->writeMessage($this->locale->_('Enable version \'%s\'... Done!' . PHP_EOL, $newVersion))
		;

		$this->writeInfo($this->locale->_('Atoum has been updated from version \'%s\' to \'%s\' successfully!', atoum\version, $newVersion));
	}

	public function listAvailableVersions()
	{
		$currentPhar = call_user_func($this->pharFactory, $this->getName());

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
			$this->writeMessage(($directory == $currentDirectory ? '*' : ' ') . ' ' . $version . PHP_EOL);
		}

		return $this->stopRun();
	}

	public function enableVersion($versionName, \phar $phar = null)
	{
		if ($this->adapter->ini_get('phar.readonly') == true)
		{
			throw new exceptions\runtime('Unable to update the PHAR, phar.readonly is set, use \'-d phar.readonly=0\'');
		}

		if ($phar === null)
		{
			$phar = call_user_func($this->pharFactory, $this->getName());
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

		return $this->stopRun();
	}

	public function deleteVersion($versionName, \phar $phar = null)
	{
		if ($this->adapter->ini_get('phar.readonly') == true)
		{
			throw new exceptions\runtime('Unable to update the PHAR, phar.readonly is set, use \'-d phar.readonly=0\'');
		}

		if ($phar === null)
		{
			$phar = call_user_func($this->pharFactory, $this->getName());
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

		$phar['versions'] = serialize($versions);

		return $this->stopRun();
	}

	public function getResourcesDirectory()
	{
		if (($versions = $this->getVersions($phar = call_user_func($this->pharFactory, $this->getName()))) === null)
		{
			throw new exceptions\runtime('Unable to define resources directory, verions\'s file is invalid');
		}

		if (isset($phar[$versions['current'] . '/resources']) === false)
		{
			throw new exceptions\logic('Resources directory does not exist in PHAR \'' . $this->getName() . '\'');
		}

		return 'phar://' . $this->getName() . '/' . $versions['current'] . '/resources';
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
				array('-e', '--extract-to'),
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
				array('-er', '--extract-resources-to'),
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

					$script->updateFromGithub();
				},
				array('--github-update'),
				null,
				$this->locale->_('Update atoum from github')
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

	protected static function getScriptFile($scriptName)
	{
		return atoum\directory . '/' . self::scriptsDirectory . '/' . $scriptName . self::scriptsExtension;
	}
}
