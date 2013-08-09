<?php

namespace mageekguy\atoum\test;

use
	mageekguy\atoum,
	mageekguy\atoum\fs\path,
	mageekguy\atoum\template,
	mageekguy\atoum\test\generator
;

class generator
{
	protected $templatesDirectory = '';
	protected $testedClassesDirectory = null;
	protected $testedClassNamespace = null;
	protected $testClassesDirectory = null;
	protected $testClassNamespace = null;
	protected $testedClassPathExtractor = null;
	protected $fullyQualifiedTestClassNameExtractor = null;
	protected $fullyQualifiedTestedClassNameExtractor = null;
	protected $runnerPath = null;
	protected $templateParser = null;
	protected $pathFactory = null;
	protected $adapter = null;

	public function __construct()
	{
		$this
			->setTemplatesDirectory()
			->setTemplateParser()
			->setPathFactory()
			->setAdapter()
			->setFullyQualifiedTestClassNameExtractor()
			->setFullyQualifiedTestedClassNameExtractor()
			->setTestedClassPathExtractor()
		;
	}

	public function setTemplatesDirectory($directory = null)
	{
		$this->templatesDirectory = $directory ?: atoum\directory . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR . 'generator';

		return $this;
	}

	public function getTemplatesDirectory()
	{
		return $this->templatesDirectory;
	}

	public function setTestedClassesDirectory($directory)
	{
		$this->testedClassesDirectory = self::cleanDirectory($directory);

		return $this;
	}

	public function getTestedClassesDirectory()
	{
		return $this->testedClassesDirectory;
	}

	public function setTestClassesDirectory($directory)
	{
		$this->testClassesDirectory = self::cleanDirectory($directory);

		return $this;
	}

	public function getTestClassesDirectory()
	{
		return $this->testClassesDirectory;
	}

	public function setRunnerPath($path)
	{
		$this->runnerPath = $path;

		return $this;
	}

	public function getRunnerPath()
	{
		return $this->runnerPath;
	}

	public function setTemplateParser(template\parser $parser = null)
	{
		$this->templateParser = $parser ?: new template\parser();

		return $this;
	}

	public function getTemplateParser()
	{
		return $this->templateParser;
	}

	public function setPathFactory(path\factory $factory = null)
	{
		$this->pathFactory = $factory ?: new path\factory();

		return $this;
	}

	public function getPathFactory()
	{
		return $this->pathFactory;
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

	public function setTestedClassNamespace($namespace)
	{
		$this->testedClassNamespace = self::cleanNamespace($namespace);

		return $this;
	}

	public function getTestedClassNamespace()
	{
		return $this->testedClassNamespace;
	}

	public function setTestClassNamespace($namespace)
	{
		$this->testClassNamespace = self::cleanNamespace($namespace);

		return $this;
	}

	public function getTestClassNamespace()
	{
		return $this->testClassNamespace;
	}

	public function setFullyQualifiedTestClassNameExtractor(\closure $extractor = null)
	{
		$this->fullyQualifiedTestClassNameExtractor = $extractor ?: function($generator, $relativeTestClassPath) {
			return $generator->getTestClassNamespace() . str_replace(DIRECTORY_SEPARATOR, '\\', substr($relativeTestClassPath, 0, -4));
		};

		return $this;
	}

	public function getFullyQualifiedTestClassNameExtractor()
	{
		return $this->fullyQualifiedTestClassNameExtractor;
	}

	public function setFullyQualifiedTestedClassNameExtractor(\closure $extractor = null)
	{
		$this->fullyQualifiedTestedClassNameExtractor = $extractor ?: function($generator, $fullyQualifiedTestClassName) {
			return $generator->getTestedClassNamespace() . substr($fullyQualifiedTestClassName, strlen($generator->getTestClassNamespace()));
		};

		return $this;
	}

	public function getFullyQualifiedTestedClassNameExtractor()
	{
		return $this->fullyQualifiedTestedClassNameExtractor;
	}

	public function setTestedClassPathExtractor(\closure $extractor = null)
	{
		$this->testedClassPathExtractor = $extractor ?: function($generator, $fullyQualifiedTestedClassName) {
			return $generator->getTestedClassesDirectory() . substr(str_replace('\\', DIRECTORY_SEPARATOR, $fullyQualifiedTestedClassName), strlen($generator->getTestedClassNamespace())) . '.php';
		};

		return $this;
	}

	public function getTestedClassPathExtractor()
	{
		return $this->testedClassPathExtractor;
	}

	public function generate($testClassPath)
	{
		if ($this->testedClassesDirectory === null)
		{
			throw new generator\exception('Tested classes directory is undefined');
		}

		if ($this->testClassesDirectory === null)
		{
			throw new generator\exception('Tests directory is undefined');
		}

		if ($this->testedClassNamespace === null)
		{
			throw new generator\exception('Tested class namespace is undefined');
		}

		if ($this->testClassNamespace === null)
		{
			throw new generator\exception('Test class namespace is undefined');
		}

		$testClassesDirectory = $this->pathFactory->build($this->testClassesDirectory);

		if ($testClassesDirectory->exists() === false)
		{
			throw new generator\exception('Test classes directory \'' . $testClassesDirectory . '\' does not exist');
		}

		$realTestClassesDirectory = $testClassesDirectory->getRealPath();
		$realTestClassPath = $this->pathFactory->build($testClassPath)->getRealPath();
		$realTestClassBaseDirectory = $realTestClassPath->getRealParentDirectoryPath();

		if ((string) $realTestClassesDirectory !== (string) $realTestClassBaseDirectory && $realTestClassBaseDirectory->isSubPathOf($realTestClassesDirectory) === false)
		{
			throw new generator\exception('Path \'' . $testClassPath . '\' is not in directory \'' . $this->testClassesDirectory . '\'');
		}

		$realTestClassRelativePath = substr($realTestClassPath->getRelativePathFrom($realTestClassesDirectory), 2);

		$fullyQualifiedTestClassName = call_user_func_array($this->fullyQualifiedTestClassNameExtractor, array($this, $realTestClassRelativePath));

		$testClassTemplate = $this->templateParser->parseFile($this->templatesDirectory . DIRECTORY_SEPARATOR . 'testClass.php');

		$testClassTemplate->fullyQualifiedTestClassName = $fullyQualifiedTestClassName;
		$testClassTemplate->testClassName = self::getShortClassName($fullyQualifiedTestClassName);
		$testClassTemplate->testClassNamespace = self::getClassNamespace($fullyQualifiedTestClassName);

		if ($this->runnerPath !== null)
		{
			$runnerPath = $this->pathFactory->build($this->runnerPath);
			$relativeRunnerPath = $runnerPath->relativizeFrom($realTestClassBaseDirectory);

			$testClassTemplate->requireRunner->relativeRunnerPath = $relativeRunnerPath;
			$testClassTemplate->requireRunner->build();
		}

		$fullyQualifiedTestedClassName = call_user_func_array($this->fullyQualifiedTestedClassNameExtractor, array($this, $fullyQualifiedTestClassName));

		if ($this->adapter->class_exists($fullyQualifiedTestedClassName) === false)
		{
			$testClassTemplate->testMethods->testMethod->methodName = '__construct';
			$testClassTemplate->testMethods->testMethod->methodName->build();
			$testClassTemplate->testMethods->testMethod->build();

			$testedClassPath = $this->pathFactory->build(call_user_func_array($this->testedClassPathExtractor, array($this, $fullyQualifiedTestedClassName)));

			$testedClassTemplate = $this->templateParser->parseFile($this->templatesDirectory . DIRECTORY_SEPARATOR . 'testedClass.php');

			$testedClassTemplate->testedClassName = self::getShortClassName($fullyQualifiedTestedClassName);
			$testedClassTemplate->testedClassNamespace = self::getClassNamespace($fullyQualifiedTestedClassName);

			$testedClassPath->putContents($testedClassTemplate->build());
		}
		else
		{
			$testedClass = new \reflectionClass($fullyQualifiedTestedClassName);

			foreach ($testedClass->getMethods(\reflectionMethod::IS_PUBLIC) as $publicMethod)
			{
				$testClassTemplate->testMethods->testMethod->methodName = $publicMethod->getName();
				$testClassTemplate->testMethods->testMethod->methodName->build();
				$testClassTemplate->testMethods->testMethod->build();
			}
		}

		$testClassTemplate->testMethods->build();

		$realTestClassPath->putContents($testClassTemplate->build());

		return $this;
	}

	protected function saveClassInFile($class, $file)
	{
		if (@$this->adapter->file_put_contents($file, $class) === false)
		{
			throw new generator\exception('Unable to write in file \'' . $file . '\'');
		}

		return $this;
	}

	protected static function cleanDirectory($path)
	{
		return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}

	protected static function cleanNamespace($namespace)
	{
		return trim($namespace, '\\') . '\\';
	}

	protected static function getShortClassName($fullyQualifiedClassName)
	{
		return basename(str_replace('\\', DIRECTORY_SEPARATOR, $fullyQualifiedClassName));
	}

	protected static function getClassNamespace($fullyQualifiedClassName)
	{
		return str_replace(DIRECTORY_SEPARATOR, '\\', dirname(str_replace('\\', DIRECTORY_SEPARATOR, $fullyQualifiedClassName)));
	}
}
