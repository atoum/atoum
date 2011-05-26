<?php

namespace mageekguy\atoum\report\fields\runner\coverage;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report,
	\mageekguy\atoum\template,
	\mageekguy\atoum\exceptions
;

class html extends report\fields\runner\coverage\string
{
	const defaultAlternatePrompt = '=> ';

	protected $adapter = null;
	protected $rootUrl = '';
	protected $projectName = '';
	protected $alternatePrompt = '';
	protected $srcDirectories = array();
	protected $templatesDirectory = null;
	protected $destinationDirectory = null;
	protected $templateParser = null;
	protected $reflectionClassInjector = null;
	protected $srcDirectoryIteratorInjector = null;
	protected $destinationDirectoryIteratorInjector = null;

	public function __construct($projectName, $templatesDirectory, $destinationDirectory, template\parser $parser = null, atoum\adapter $adapter = null, atoum\locale $locale = null, $prompt = null, $alternatePrompt = null)
	{
		parent::__construct($locale, $prompt);

		$this
			->setProjectName($projectName)
			->setTemplatesDirectory($templatesDirectory)
			->setDestinationDirectory($destinationDirectory)
			->setTemplateParser($parser ?: new template\parser())
			->setAdapter($adapter ?: new atoum\adapter())
			->setAlternatePrompt($alternatePrompt ?: self::defaultAlternatePrompt)
			->setRootUrl('/')
		;
	}

	public function setReflectionClassInjector(\closure $reflectionClassInjector)
	{
		$closure = new \reflectionMethod($reflectionClassInjector, '__invoke');

		if ($closure->getNumberOfParameters() != 1)
		{
			throw new exceptions\logic\invalidArgument('Reflection class injector must take one argument');
		}

		$this->reflectionClassInjector = $reflectionClassInjector;

		return $this;
	}

	public function getReflectionClass($class)
	{
		$reflectionClass = null;

		if ($this->reflectionClassInjector === null)
		{
			$reflectionClass = new \reflectionClass($class);
		}
		else
		{
			$reflectionClass = $this->reflectionClassInjector->__invoke($class);

			if ($reflectionClass instanceof \reflectionClass === false)
			{
				throw new exceptions\runtime\unexpectedValue('Reflection class injector must return a \reflectionClass instance');
			}
		}

		return $reflectionClass;
	}

	public function __toString()
	{
		$string = parent::__toString();

		if ($this->adapter->extension_loaded('xdebug') === true)
		{
			foreach ($this->srcDirectories as $srcDirectory)
			{
				foreach ($this->getSrcDirectoryIterator($srcDirectory) as $file)
				{
					$this->adapter->xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);

					$declaredClasses = $this->adapter->get_declared_classes();

					require_once($file->getPathname());

					$xDebugData = $this->adapter->xdebug_get_code_coverage();

					$this->adapter->xdebug_stop_code_coverage();

					foreach (array_diff($this->adapter->get_declared_classes(), $declaredClasses) as $class)
					{
						$this->coverage->addXdebugDataForClass($class, $xDebugData);
					}
				}
			}
		}

		if (sizeof($this->coverage) > 0)
		{
			$this->cleanDestinationDirectory();

			$this->adapter->copy($this->templatesDirectory . '/screen.css', $this->destinationDirectory . '/screen.css');

			$classes = $this->coverage->getClasses();

			$indexTemplate = $this->templateParser->parseFile($this->templatesDirectory . '/index.tpl');

			if (isset($indexTemplate->projectName) === true)
			{
				$indexTemplate->projectName = $this->projectName;
			}

			if (isset($indexTemplate->rootUrl) === true)
			{
				$indexTemplate->rootUrl = $this->rootUrl;
			}

			if (isset($indexTemplate->coverageValue) === true)
			{
				$indexTemplate->coverageValue = round($this->coverage->getValue() * 100, 2);
			}

			$classTemplate = $indexTemplate->getById('class');

			if ($classTemplate !== null)
			{
				ksort($classes);

				foreach ($classes as $className => $classFile)
				{
					if (isset($classTemplate->className) === true)
					{
						$classTemplate->className = $className;
					}

					if (isset($classTemplate->classUrl) === true)
					{
						$classTemplate->classUrl = ltrim(str_replace('\\', DIRECTORY_SEPARATOR, $className), DIRECTORY_SEPARATOR);
					}

					if (isset($classTemplate->classCoverageValue) === true)
					{
						$classTemplate->classCoverageValue = round($this->coverage->getValueForClass($className) * 100, 2);
					}

					$classTemplate->build();
				}
			}

			$this->adapter->file_put_contents($this->destinationDirectory . '/index.html', (string) $indexTemplate->build());

			$classTemplate = $this->templateParser->parseFile($this->templatesDirectory . '/class.tpl');

			if (isset($classTemplate->rootUrl) === true)
			{
				$classTemplate->rootUrl = $this->rootUrl;
			}

			if (isset($classTemplate->projectName) === true)
			{
				$classTemplate->projectName = $this->projectName;
			}

			$methodTemplate = $classTemplate->getById('method');

			$sourceFileTemplate = $classTemplate->getById('sourceFile');

			if ($sourceFileTemplate === null)
			{
				$lineTemplate = null;
				$coveredLineTemplate = null;
				$notCoveredLineTemplate = null;
			}
			else
			{
				$lineTemplate = $sourceFileTemplate->getById('line');
				$coveredLineTemplate = $sourceFileTemplate->getById('coveredLine');
				$notCoveredLineTemplate = $sourceFileTemplate->getById('notCoveredLine');
			}

			foreach ($this->coverage->getMethods() as $className => $methods)
			{
				if (isset($classTemplate->className) === true)
				{
					$classTemplate->className = $className;
				}

				if (isset($classTemplate->classCoverageValue) === true)
				{
					$classTemplate->classCoverageValue = round($this->coverage->getValueForClass($className) * 100, 2);
				}

				if ($methodTemplate !== null)
				{
					$reflectedMethods = array();

					foreach (array_filter($this->getReflectionClass($className)->getMethods(), function($reflectedMethod) use ($className) { return $reflectedMethod->isAbstract() === false && $reflectedMethod->getDeclaringClass()->getName() === $className; }) as $reflectedMethod)
					{
						$reflectedMethods[$reflectedMethod->getName()] = $reflectedMethod;
					}

					foreach (array_intersect(array_keys($reflectedMethods), array_keys($methods)) as $methodName)
					{
						if (isset($methodTemplate->methodName) === true)
						{
							$methodTemplate->methodName = $methodName;
						}

						if (isset($methodTemplate->methodCoverageValue) === true)
						{
							$methodTemplate->methodCoverageValue = round($this->coverage->getValueForMethod($className, $methodName) * 100, 2);
						}

						$methodTemplate->build();
					}

					if ($lineTemplate !== null && $coveredLineTemplate !== null && $notCoveredLineTemplate !== null)
					{
						$srcFile = $this->adapter->fopen($classes[$className], 'r');

						if ($srcFile !== false)
						{
							$methodLines = array();

							foreach ($reflectedMethods as $reflectedMethodName => $reflectedMethod)
							{
								$methodLines[$reflectedMethod->getStartLine()] = $reflectedMethodName;
							}

							$currentMethod = null;

							for ($currentMethod = null, $lineNumber = 1, $line = $this->adapter->fgets($srcFile); $line !== false; $lineNumber++, $line = $this->adapter->fgets($srcFile))
							{
								if (isset($methodLines[$lineNumber]) === true)
								{
									$currentMethod = $methodLines[$lineNumber];
								}

								switch (true)
								{
									case isset($methods[$currentMethod]) === false || (isset($methods[$currentMethod][$lineNumber]) === false || $methods[$currentMethod][$lineNumber] == -2):
										$lineTemplateName = 'lineTemplate';
										break;

									case isset($methods[$currentMethod]) === true && isset($methods[$currentMethod][$lineNumber]) === true && $methods[$currentMethod][$lineNumber] == -1:
										$lineTemplateName = 'notCoveredLineTemplate';
										break;

									default:
										$lineTemplateName = 'coveredLineTemplate';
								}

								${$lineTemplateName}->lineNumber = $lineNumber;
								${$lineTemplateName}->code = htmlentities($line, ENT_QUOTES, 'UTF-8');

								if (isset($methodLines[$lineNumber]) === true)
								{
									foreach (${$lineTemplateName}->getByTag('anchor') as $anchorTemplate)
									{
										$anchorTemplate->resetData();
										$anchorTemplate->method = $currentMethod;
										$anchorTemplate->build();
									}
								}

								${$lineTemplateName}
									->addToParent()
									->resetData()
								;
							}

							$this->adapter->fclose($srcFile);

							$sourceFileTemplate->build();
						}

					}
				}

				$file = $this->destinationDirectory . '/' . ltrim(str_replace('\\', DIRECTORY_SEPARATOR, $className), DIRECTORY_SEPARATOR) . '.html';

				$directory = $this->adapter->dirname($file);

				if ($this->adapter->is_dir($directory) === false)
				{
					$this->adapter->mkdir($directory, 0777, true);
				}

				$this->adapter->file_put_contents($file, (string) $classTemplate->build());

				$classTemplate->resetData();

				if ($methodTemplate !== null)
				{
					$methodTemplate->resetData();
				}

				if ($sourceFileTemplate !== null)
				{
					$sourceFileTemplate->resetData();
				}
			}

			$string .= $this->alternatePrompt . sprintf($this->locale->_('Details of code coverage are available at %s.'), $this->rootUrl) . PHP_EOL;
		}

		return $string;
	}

	public function setAdapter(atoum\adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setAlternatePrompt($prompt)
	{
		$this->alternatePrompt = (string) $prompt;

		return $this;
	}

	public function getAlternatePrompt()
	{
		return $this->alternatePrompt;
	}

	public function addSrcDirectory($srcDirectory)
	{
		$srcDirectory = (string) $srcDirectory;

		if (in_array($srcDirectory, $this->srcDirectories) === false)
		{
			$this->srcDirectories[] = $srcDirectory;
		}

		return $this;
	}

	public function getSrcDirectories()
	{
		return $this->srcDirectories;
	}

	public function setTemplatesDirectory($path)
	{
		$this->templatesDirectory = (string) $path;

		return $this;
	}

	public function getTemplatesDirectory()
	{
		return $this->templatesDirectory;
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

	public function setTemplateParser(template\parser $parser)
	{
		$this->templateParser = $parser;

		return $this;
	}

	public function getTemplateParser()
	{
		return $this->templateParser;
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

	public function setRootUrl($rootUrl)
	{
		$this->rootUrl = (string) $rootUrl;

		return $this;
	}

	public function getRootUrl()
	{
		return $this->rootUrl;
	}

	public function setDestinationDirectoryIteratorInjector(\closure $destinationDirectoryIteratorInjector)
	{
		$this->destinationDirectoryIteratorInjector = $destinationDirectoryIteratorInjector;

		return $this;
	}

	public function getDestinationDirectoryIterator($directory)
	{
		if ($this->destinationDirectoryIteratorInjector === null)
		{
			$this->setDestinationDirectoryIteratorInjector(function($directory) { return new \directoryIterator($directory); });
		}

		return $this->destinationDirectoryIteratorInjector->__invoke($directory);
	}

	public function setSrcDirectoryIteratorInjector(\closure $srcDirectoryIteratorInjector)
	{
		$this->srcDirectoryIteratorInjector = $srcDirectoryIteratorInjector;

		return $this;
	}

	public function getSrcDirectoryIterator($directory)
	{
		if ($this->srcDirectoryIteratorInjector === null)
		{
			$this->setSrcDirectoryIteratorInjector(function($directory) { return new \recursiveIteratorIterator(new atoum\src\iterator\filter(new \recursiveDirectoryIterator($directory))); });
		}

		return $this->srcDirectoryIteratorInjector->__invoke($directory);
	}

	public function cleanDestinationDirectory()
	{
		return $this->doCleanDestinationDirectory($this->destinationDirectory);
	}

	protected function doCleanDestinationDirectory($path)
	{
		foreach ($this->getDestinationDirectoryIterator($path) as $inode)
		{
			if ($inode->isDot() === false)
			{
				$inodePath = $inode->getPathname();

				if ($inode->isDir() === false)
				{
					$this->adapter->unlink($inodePath);
				}
				else
				{
					$this
						->doCleanDestinationDirectory($inodePath)
						->adapter->rmdir($inodePath)
					;
				}
			}
		}

		return $this;
	}
}

?>
