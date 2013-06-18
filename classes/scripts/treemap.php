<?php

namespace mageekguy\atoum\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\scripts\treemap\analyzers
;

class treemap extends atoum\script\configurable
{
	const defaultConfigFile = '.treemap.php';
	const dataFile = 'data.json';

	protected $projectName = null;
	protected $projectUrl = null;
	protected $codeUrl = null;
	protected $directories = array();
	protected $htmlDirectory = null;
	protected $outputDirectory = null;
	protected $onlyJsonFile = false;
	protected $analyzers = array();
	protected $categorizers = array();

	public function __construct($name, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $adapter);

		$this->setIncluder();
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

	public function setHtmlDirectory($path = null)
	{
		$this->htmlDirectory = $path;

		return $this;
	}

	public function getHtmlDirectory()
	{
		return $this->htmlDirectory;
	}

	public function setOutputDirectory($directory)
	{
		$this->outputDirectory = $directory;

		return $this;
	}

	public function getOutputDirectory()
	{
		return $this->outputDirectory;
	}

	public function getOnlyJsonFile($boolean = null)
	{
		if ($boolean !== null)
		{
			$this->onlyJsonFile = ($boolean == true);
		}

		return $this->onlyJsonFile;
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

	public function useConfigFile($path)
	{
		$script = $this;

		return $this->includeConfigFile($path, function($path) use ($script) { include_once($path); });
	}

	protected function setArgumentHandlers()
	{
		return parent::setArgumentHandlers()
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
				function($script, $argument, $outputDirectory) {
					if (sizeof($outputDirectory) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setOutputDirectory(current($outputDirectory));
				},
				array('-od', '--output-directory'),
				'<directory>',
				$this->locale->_('Generate treemap in directory <directory>')
			)
			->addArgumentHandler(
				function($script, $argument, $value) {
					if (sizeof($value) != 0)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->getOnlyJsonFile(true);
				},
				array('-ojf', '--only-json-file'),
				null,
				$this->locale->_('Generate only JSON file')
			)
			->addArgumentHandler(
				function($script, $argument, $value) {
					if (sizeof($value) != 0)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->addAnalyzer(new analyzers\sloc());
				},
				array('--sloc'),
				null,
				$this->locale->_('Count source line of code (SLOC)')
			)
			->addArgumentHandler(
				function($script, $argument, $value) {
					if (sizeof($value) != 0)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->addAnalyzer(new analyzers\token());
				},
				array('--php-token'),
				null,
				$this->locale->_('Count PHP tokens')
			)
			->addArgumentHandler(
				function($script, $argument, $value) {
					if (sizeof($value) != 0)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->addAnalyzer(new analyzers\size());
				},
				array('--file-size'),
				null,
				$this->locale->_('Get file size')
			)
			->addArgumentHandler(
				function($script, $argument, $htmlDirectory) {
					if (sizeof($htmlDirectory) != 1)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->setHtmlDirectory(current($htmlDirectory));
				},
				array('-hd', '--html-directory'),
				'<directory>',
				$this->locale->_('Use html files in <directory> to generate treemap')
			)
		;
	}

	protected function doRun()
	{
		if ($this->projectName === null)
		{
			throw new exceptions\runtime($this->locale->_('Project name is undefined'));
		}

		if (sizeof($this->directories) <= 0)
		{
			throw new exceptions\runtime($this->locale->_('Directories are undefined'));
		}

		if ($this->outputDirectory === null)
		{
			throw new exceptions\runtime($this->locale->_('Output directory is undefined'));
		}

		if ($this->htmlDirectory === null)
		{
			throw new exceptions\runtime($this->locale->_('Html directory is undefined'));
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
					'type' => ''
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

		if (@file_put_contents($this->outputDirectory . DIRECTORY_SEPARATOR . self::dataFile, json_encode($data)) === false)
		{
			throw new exceptions\runtime($this->locale->_('Unable to write in \'' . $this->outputDirectory . '\''));
		}

		if ($this->onlyJsonFile === false)
		{
			try
			{
				$htmlDirectoryIterator = new \recursiveIteratorIterator(new atoum\iterators\filters\recursives\dot($this->htmlDirectory));
			}
			catch (\exception $exception)
			{
				throw new exceptions\runtime($this->locale->_('Directory \'' . $this->htmlDirectory . '\' does not exist'));
			}

			foreach ($htmlDirectoryIterator as $file)
			{
				if (@copy($file, $this->outputDirectory . DIRECTORY_SEPARATOR . basename($file)) === false)
				{
					throw new exceptions\runtime($this->locale->_('Unable to write in \'' . $this->outputDirectory . '\''));
				}
			}
		}

		return $this;
	}
}
