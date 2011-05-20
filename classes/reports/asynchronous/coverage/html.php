<?php

namespace mageekguy\atoum\reports\asynchronous\coverage;

use
	\mageekguy\atoum,
	\mageekguy\atoum\reports,
	\mageekguy\atoum\template,
	\mageekguy\atoum\exceptions
;

class html extends reports\asynchronous
{
	protected $rootUrl = '';
	protected $projectName = '';
	protected $templatesDirectory = null;
	protected $destinationDirectory = null;
	protected $templateParser = null;
	protected $directoryIteratorInjector = null;

	public function __construct($projectName, $templatesDirectory, $destinationDirectory, template\parser $parser = null, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($locale, $adapter);

		if ($parser === null)
		{
			$parser = new template\parser();
		}

		$this
			->setProjectName($projectName)
			->setTemplatesDirectory($templatesDirectory)
			->setDestinationDirectory($destinationDirectory)
			->setTemplateParser($parser)
			->setRootUrl('/')
		;
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

	public function runnerStop(atoum\runner $runner)
	{
		$coverage = $runner->getScore()->getCoverage();

		if (sizeof($coverage) > 0)
		{
			$this->cleanDestinationDirectory();

			$classes = $coverage->getClasses();
			$methods = $coverage->getMethods();

			$indexTemplate = $this->templateParser->parseFile($this->templatesDirectory . '/index.tpl');

			$coverageTemplate = $indexTemplate->getById('coverage');

			if (isset($indexTemplate->projectName) === true)
			{
				$indexTemplate->projectName = $this->projectName;
			}

			if (isset($indexTemplate->rootUrl) === true)
			{
				$indexTemplate->rootUrl = $this->rootUrl;
			}

			$classTemplate = $this->templateParser->parseFile($this->templatesDirectory . '/class.tpl');

			if (isset($classTemplate->rootUrl) === true)
			{
				$classTemplate->rootUrl = $this->rootUrl;
			}

			$methodTemplate = $classTemplate->getById('method');
			$sourceTemplate = $classTemplate->getById('source');
			$blankLineTemplate = $classTemplate->getById('blankLine');
			$coveredLineTemplate = $classTemplate->getById('coveredLine');
			$notCoveredLineTemplate = $classTemplate->getById('notCoveredLine');

			if (isset($classTemplate->projectName) === true)
			{
				$classTemplate->projectName = $this->projectName;
			}

			if ($coverageTemplate !== null)
			{
				ksort($classes);

				foreach ($classes as $className => $classFile)
				{
					if (isset($coverageTemplate->className) === true)
					{
						$coverageTemplate->className = $className;
					}

					if (isset($coverageTemplate->classDate) === true)
					{
						$coverageTemplate->classDate = date('Y-m-d H:i:s', $this->adapter->filemtime($classFile));
					}

					if (isset($coverageTemplate->classUrl) === true)
					{
						$coverageTemplate->classUrl = ltrim(str_replace('\\', DIRECTORY_SEPARATOR, $className), DIRECTORY_SEPARATOR);
					}

					if (isset($coverageTemplate->classCoverageValue) === true)
					{
						$coverageTemplate->classCoverageValue = round($coverage->getValueForClass($className) * 100, 2);
					}

					$coverageTemplate->build();

					if (isset($classTemplate->className) === true)
					{
						$classTemplate->className = $className;
					}

					if (isset($classTemplate->classCoverageValue) === true)
					{
						$classTemplate->classCoverageValue = round($coverage->getValueForClass($className) * 100, 2);
					}

					if ($methodTemplate !== null)
					{
						foreach ($methods[$className] as $methodName => $methodCoverage)
						{
							if (isset($methodTemplate->methodName) === true)
							{
								$methodTemplate->methodName = $methodName;
							}

							if (isset($methodTemplate->methodCoverageValue) === true)
							{
								$methodTemplate->methodCoverageValue = round($coverage->getValueForMethod($className, $methodName) * 100, 2);
							}

							$methodTemplate->build();
						}
					}

					if ($sourceTemplate !== null && $blankLineTemplate !== null && $coveredLineTemplate !== null && $notCoveredLineTemplate !== null)
					{
						$srcFile = $this->adapter->fopen($classFile, 'r');

						if ($srcFile !== false)
						{
							$reflection = new \reflectionClass($className);

							$methodLines = array();

							foreach ($reflection->getMethods(ReflectionMethod::IS_STATIC | \ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED | \ReflectionMethod::IS_PRIVATE | \ReflectionMethod::IS_FINAL) as $method)
							{
								if ($method->getDeclaringClass()->getName() === $className)
								{
									$methodLines[$method->getStartLine()] = $method->getName();
								}
							}

							$lineNumber = 1;
							$currentMethod = null;

							while (($code = $this->adapter->fgets($srcFile)) !== false)
							{
								if (isset($methodLines[$lineNumber]) === true)
								{
									$currentMethod = $methodLines[$lineNumber];
								}

								$code = htmlentities(rtrim($code), ENT_QUOTES, 'UTF-8');

								switch (true)
								{
									case isset($methods[$className][$currentMethod]) === false || isset($methods[$className][$currentMethod][$lineNumber]) === false:
										$blankLineTemplate->lineNumber = $lineNumber;
										$blankLineTemplate->code = $code;

										if (isset($methodLines[$lineNumber]) === true)
										{
											foreach ($blankLineTemplate->getByTag('anchor') as $anchorTemplate)
											{
												$anchorTemplate->resetData();
												$anchorTemplate->method = $currentMethod;
												$anchorTemplate->build();
											}
										}

										$blankLineTemplate
											->addToParent()
											->resetData()
										;
										break;

									case isset($methods[$className][$currentMethod]) === true && isset($methods[$className][$currentMethod][$lineNumber]) === true && $methods[$className][$currentMethod][$lineNumber] == -1:
										$notCoveredLineTemplate->lineNumber = $lineNumber;
										$notCoveredLineTemplate->code = $code;

										if (isset($methodLines[$lineNumber]) === true)
										{
											foreach ($notCoveredLineTemplate->getByTag('anchor') as $anchorTemplate)
											{
												$anchorTemplate->resetData();
												$anchorTemplate->method = $currentMethod;
												$anchorTemplate->build();
											}
										}

										$notCoveredLineTemplate
											->addToParent()
											->resetData()
										;
										break;

									default:
										$coveredLineTemplate->lineNumber = $lineNumber;
										$coveredLineTemplate->code = $code;

										if (isset($methodLines[$lineNumber]) === true)
										{
											foreach ($coveredLineTemplate->getByTag('anchor') as $anchorTemplate)
											{
												$anchorTemplate->resetData();
												$anchorTemplate->method = $currentMethod;
												$anchorTemplate->build();
											}
										}

										$coveredLineTemplate
											->addToParent()
											->resetData()
										;
								}

								$lineNumber++;
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
				}
			}

			$this->adapter->file_put_contents($this->destinationDirectory . '/index.html', (string) $indexTemplate->build());

			$this->adapter->copy($this->templatesDirectory . '/screen.css', $this->destinationDirectory . '/screen.css');
		}

		return $this;
	}

	public function setDirectoryIteratorInjector(\closure $directoryIteratorInjector)
	{
		$this->directoryIteratorInjector = $directoryIteratorInjector;

		return $this;
	}

	public function getDirectoryIterator($directory)
	{
		if ($this->directoryIteratorInjector === null)
		{
			$this->setDirectoryIteratorInjector(function($directory) { return new \directoryIterator($directory); });
		}

		return $this->directoryIteratorInjector->__invoke($directory);
	}

	public function cleanDestinationDirectory()
	{
		return $this->cleanDirectory($this->destinationDirectory);
	}

	protected function cleanDirectory($path)
	{
		foreach ($this->getDirectoryIterator($path) as $inode)
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
						->cleanDirectory($inodePath)
						->adapter->rmdir($inodePath)
					;
				}
			}
		}

		return $this;
	}
}

?>
