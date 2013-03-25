<?php

namespace mageekguy\atoum\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class treemap extends atoum\script
{
	protected $projectName = null;
	protected $projectUrl = null;
	protected $codeUrl = null;
	protected $directories = array();
	protected $outputFile = null;
	protected $analyzers = array();
	protected $categorizers = array();
	protected $includer = null;
	protected $run = true;

	public function __construct($name, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $adapter);

		$this->setIncluder();
	}

	public function help()
	{
		$this->run = false;

		return parent::help();
	}

	public function getProjectName()
	{
		return $this->projectName;
	}

	public function setProjectName($projectName)
	{
		$this->projectName = $projectName;

		return $this;
	}

	public function getProjectUrl()
	{
		return $this->projectUrl;
	}

	public function setProjectUrl($projectUrl)
	{
		$this->projectUrl = $projectUrl;

		return $this;
	}

	public function getCodeUrl()
	{
		return $this->codeUrl;
	}

	public function setCodeUrl($codeUrl)
	{
		$this->codeUrl = $codeUrl;

		return $this;
	}

	public function addDirectory($directory)
	{
		if (in_array($directory, $this->directories) === false)
		{
			$this->directories[] = $directory;
		}

		return $this;
	}

	public function getDirectories()
	{
		return $this->directories;
	}

	public function setOutputFile($file)
	{
		$this->outputFile = $file;

		return $this;
	}

	public function getOutputFile()
	{
		return $this->outputFile;
	}

	public function getAnalyzers()
	{
		return $this->analyzers;
	}

	public function addAnalyzer(treemap\analyzer $analyzer)
	{
		$this->analyzers[] = $analyzer;

		return $this;
	}

	public function getCategorizers()
	{
		return $this->categorizers;
	}

	public function addCategorizer(treemap\categorizer $categorizer)
	{
		$this->categorizers[] = $categorizer;

		return $this;
	}

	public function setIncluder(atoum\includer $includer = null)
	{
		$this->includer = $includer ?: new atoum\includer();

		return $this;
	}

	public function getIncluder()
	{
		return $this->includer;
	}

	public function useConfigFile($path)
	{
		$script = $this;

		try
		{
			$this->includer->includePath($path, function($path) use ($script) { include_once($path); });
		}
		catch (atoum\includer\exception $exception)
		{
			throw new atoum\includer\exception(sprintf($this->getLocale()->_('Unable to find configuration file \'%s\''), $path));
		}

		return $this;
	}

	public function run(array $arguments = array())
	{
		parent::run($arguments);

		if ($this->run === true)
		{
			if ($this->projectName === null)
			{
				throw new exceptions\runtime($this->locale->_('Project name is undefined'));
			}

			if (sizeof($this->directories) <= 0)
			{
				throw new exceptions\runtime($this->locale->_('Directories are undefined'));
			}

			if ($this->outputFile === null)
			{
				throw new exceptions\runtime($this->locale->_('Output file is undefined'));
			}

			$maxDepth = 1;

			$nodes = array(
				'name' => $this->projectName,
				'url' => $this->projectUrl,
				'path' => '',
				'children' => array()
			);

			foreach ($this->directories as $rootDirectory)
			{
				try
				{
					$directoryIterator = new \recursiveIteratorIterator(new atoum\iterators\filters\recursives\dot($rootDirectory));
				}
				catch (\exception $exception)
				{
					throw new exceptions\runtime($this->locale->_('Directory \'' . $rootDirectory . '\' does not exist'));
				}

				foreach ($directoryIterator as $file)
				{
					$node = & $nodes;

					$directories = ltrim(substr(dirname($file->getPathname()), strlen($rootDirectory)), DIRECTORY_SEPARATOR);

					if ($directories !== '')
					{
						$directories = explode(DIRECTORY_SEPARATOR, $directories);

						$depth = sizeof($directories);

						if ($depth > $maxDepth)
						{
							$maxDepth = $depth;
						}

						foreach ($directories as $directory)
						{
							$childFound = false;

							foreach ($node['children'] as $key => $child)
							{
								$childFound = ($child['name'] === $directory);

								if ($childFound === true)
								{
									break;
								}
							}

							if ($childFound === false)
							{
								$key = sizeof($node['children']);
								$node['children'][] = array(
									'name' => $directory,
									'path' => $node['path'] . DIRECTORY_SEPARATOR . $directory,
									'children' => array()
								);
							}

							$node = & $node['children'][$key];
						}
					}

					$child = array(
						'name' => $file->getFilename(),
						'path' => $node['path'] . DIRECTORY_SEPARATOR . $file->getFilename(),
						'metrics' => array(),
						'type' => null
					);

					foreach ($this->analyzers as $analyzer)
					{
						$child['metrics'][$analyzer->getMetricName()] = $analyzer->getMetricFromFile($file);
					}

					foreach ($this->categorizers as $categorizer)
					{
						if ($categorizer->categorize($file) === true)
						{
							$child['type'] = $categorizer->getName();

							break;
						}
					}

					$node['children'][] = $child;
				}
			}

			$data = array(
				'codeUrl' => $this->codeUrl,
				'metrics' => array(),
				'categorizers' => array(),
				'maxDepth' => $maxDepth,
				'nodes' => $nodes
			);

			foreach ($this->analyzers as $analyzer)
			{
				$data['metrics'][] = array(
					'name' => $analyzer->getMetricName(),
					'label' => $analyzer->getMetricLabel()
				);
			}

			foreach ($this->categorizers as $categorizer)
			{
				$data['categorizers'][] = array(
					'name' => $categorizer->getName(),
					'minDepthColor' => $categorizer->getMinDepthColor(),
					'maxDepthColor' => $categorizer->getMaxDepthColor()
				);
			}

			if (@file_put_contents($this->outputFile, json_encode($data)) === false)
			{
				throw new exceptions\runtime($this->locale->_('Unable to write in \'' . $this->outputFile . '\''));
			}
		}

		return $this;
	}

	protected function setArgumentHandlers()
	{
		return $this
			->addArgumentHandler(
				function($script, $argument, $values) {
					if (sizeof($values) != 0)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->help();
				},
				array('-h', '--help'),
				null,
				$this->locale->_('Display this help')
			)
			->addArgumentHandler(
				function($script, $argument, $outputFile) {
					if (sizeof($outputFile) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setOutputFile(current($outputFile));
				},
				array('-of', '--output-file'),
				'<file>',
				$this->locale->_('Save data in file <file>')
			)
			->addArgumentHandler(
				function($script, $argument, $directories) {
					if (sizeof($directories) <= 0)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					foreach ($directories as $directory)
					{
						$script->addDirectory($directory);
					}
				},
				array('-d', '--directories'),
				'<directory>...',
				$this->locale->_('Scan all directories <directory>')
			)
			->addArgumentHandler(
				function($script, $argument, $projectName) {
					if (sizeof($projectName) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setProjectName(current($projectName));
				},
				array('-pn', '--project-name'),
				'<string>',
				$this->locale->_('Set project name <string>')
			)
			->addArgumentHandler(
				function($script, $argument, $projectUrl) {
					if (sizeof($projectUrl) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getUrl()));
					}

					$script->setProjectUrl(current($projectUrl));
				},
				array('-pu', '--project-url'),
				'<string>',
				$this->locale->_('Set project url <string>')
			)
			->addArgumentHandler(
				function($script, $argument, $codeUrl) {
					if (sizeof($codeUrl) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getUrl()));
					}

					$script->setCodeUrl(current($codeUrl));
				},
				array('-cu', '--code-url'),
				'<string>',
				$this->locale->_('Set code url <string>')
			)
			->addArgumentHandler(
					function($script, $argument, $files) {
						if (sizeof($files) <= 0)
						{
							throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
						}

						foreach ($files as $path)
						{
							try
							{
								$script->useConfigFile($path);
							}
							catch (includer\exception $exception)
							{
								throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Configuration file \'%s\' does not exist'), $path));
							}
						}
					},
					array('-c', '--configurations'),
					'<file>...',
					$this->locale->_('Use all configuration files <file>'),
					1
				)
		;
	}
}
