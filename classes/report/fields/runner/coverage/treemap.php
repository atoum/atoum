<?php

namespace mageekguy\atoum\report\fields\runner\coverage;

require_once __DIR__ . '/../../../../../constants.php';

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\report,
	mageekguy\atoum\template,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer
;

class treemap extends report\fields\runner\coverage\cli
{
	const dataFile = 'data.json';

	protected $urlPrompt = null;
	protected $urlColorizer = null;
	protected $treemapUrl = '';
	protected $projectName = '';
	protected $htmlReportBaseUrl = null;
	protected $resourcesDirectory = array();
	protected $destinationDirectory = null;
	protected $reflectionClassFactory = null;

	public function __construct($projectName, $destinationDirectory)
	{
		parent::__construct();

		$this
			->setProjectName($projectName)
			->setDestinationDirectory($destinationDirectory)
			->setAdapter()
			->setUrlPrompt()
			->setUrlColorizer()
			->setTreemapUrl('/')
			->setResourcesDirectory()
		;
	}

	public function __toString()
	{
		$string = '';

		if (sizeof($this->coverage) > 0)
		{
			try
			{
				$nodes = array(
					'coverage' => round($this->coverage->getValue() * 100, 2),
					'project' => $this->projectName,
					'name' => '',
					'fullname' => '',
					'htmlReportBaseUrl' => $this->htmlReportBaseUrl,
					'date' => time(),
					'children' => array()
				);

				foreach ($this->coverage->getClasses() as $className => $classPath)
				{
					$node = & $nodes;

					$class = new \reflectionClass($className);

					$namespaces = explode('\\', $class->getNamespaceName());

					foreach ($namespaces as $namespace)
					{
						$childFound = false;

						foreach ($node['children'] as $key => $child)
						{
							$childFound = ($child['name'] === $namespace);

							if ($childFound === true)
							{
								break;
							}
						}

						if ($childFound === false)
						{
							$key = sizeof($node['children']);
							$node['children'][] = array(
								'name' => $namespace,
								'fullname' => $node['fullname'] . ($node['fullname'] == '' ? '' : '\\') . $namespace,
								'children' => array()
							);
						}

						$node = & $node['children'][$key];
					}

					$child = array(
						'name' => $class->getShortName(),
						'fullname' => $node['fullname'] . '\\' . $class->getShortName(),
						'covered' => $this->coverage->getNumberOfCoveredLinesInClass($className),
						'coverable' => $this->coverage->getNumberOfCoverableLinesInClass($className),
						'pourcent' => round($this->coverage->getValueForClass($className) * 100, 2),
						'children' => array()
					);

					$node['children'][] = $child;
				}

				if (@file_put_contents($this->destinationDirectory . DIRECTORY_SEPARATOR . self::dataFile, json_encode($nodes)) === false)
				{
					throw new exceptions\runtime($this->locale->_('Unable to write in \'' . $this->destinationDirectory . '\''));
				}

				try
				{
					$resourcesDirectoryIterator = new \recursiveIteratorIterator(new atoum\iterators\filters\recursives\dot($this->resourcesDirectory));
				}
				catch (\exception $exception)
				{
					throw new exceptions\runtime($this->locale->_('Directory \'' . $this->resourcesDirectory . '\' does not exist'));
				}

				foreach ($resourcesDirectoryIterator as $file)
				{
					if (@copy($file, $this->destinationDirectory . DIRECTORY_SEPARATOR . $resourcesDirectoryIterator->getSubPathname()) === false)
					{
						throw new exceptions\runtime($this->locale->_('Unable to write in \'' . $this->destinationDirectory . '\''));
					}
				}

				$string .= $this->urlPrompt . $this->urlColorizer->colorize($this->locale->_('Treemap of code coverage are available at %s.', $this->treemapUrl)) . PHP_EOL;
			}
			catch (\exception $exception)
			{
				$string .= $this->urlPrompt . $this->urlColorizer->colorize($this->locale->_('Unable to generate code coverage at %s: %s.', $this->treemapUrl, $exception->getMessage())) . PHP_EOL;
			}
		}

		return $string;
	}

	public function getHtmlReportBaseUrl()
	{
		return $this->htmlReportBaseUrl;
	}

	public function setHtmlReportBaseUrl($url)
	{
		$this->htmlReportBaseUrl = (string) $url;

		return $this;
	}

	public function setReflectionClassFactory(\closure $factory)
	{
		$closure = new \reflectionMethod($factory, '__invoke');

		if ($closure->getNumberOfParameters() != 1)
		{
			throw new exceptions\logic\invalidArgument('Reflection class factory must take one argument');
		}

		$this->reflectionClassFactory = $factory;

		return $this;
	}

	public function getReflectionClass($class)
	{
		if ($this->reflectionClassFactory === null)
		{
			$reflectionClass = new \reflectionClass($class);
		}
		else
		{
			$reflectionClass = $this->reflectionClassFactory->__invoke($class);

			if ($reflectionClass instanceof \reflectionClass === false)
			{
				throw new exceptions\runtime\unexpectedValue('Reflection class injector must return a \reflectionClass instance');
			}
		}

		return $reflectionClass;
	}

	public function setProjectName($projectName)
	{
		$this->projectName = (string) $projectName;

		return $this;
	}

	public function getProjectName()
	{
		return $this->projectName;
	}

	public function setDestinationDirectory($path)
	{
		$this->destinationDirectory = (string) $path;

		return $this;
	}

	public function getDestinationDirectory()
	{
		return $this->destinationDirectory;
	}

	public function setAdapter(atoum\adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new atoum\adapter();

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setUrlPrompt(prompt $prompt = null)
	{
		$this->urlPrompt = $prompt ?: new prompt();

		return $this;
	}

	public function getUrlPrompt()
	{
		return $this->urlPrompt;
	}

	public function setUrlColorizer(colorizer $colorizer = null)
	{
		$this->urlColorizer = $colorizer ?: new colorizer();

		return $this;
	}

	public function getUrlColorizer()
	{
		return $this->urlColorizer;
	}

	public function setTreemapUrl($treemapUrl)
	{
		$this->treemapUrl = (string) $treemapUrl;

		return $this;
	}

	public function getTreemapUrl()
	{
		return $this->treemapUrl;
	}

	public function setResourcesDirectory($directory = null)
	{
		$this->resourcesDirectory = $directory ?: atoum\directory . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'coverage' . DIRECTORY_SEPARATOR . 'treemap';

		return $this;
	}

	public function getResourcesDirectory()
	{
		return $this->resourcesDirectory;
	}
}
