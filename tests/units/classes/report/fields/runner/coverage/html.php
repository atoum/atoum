<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\coverage;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\mock,
	mageekguy\atoum\locale,
	mageekguy\atoum\template,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\runner\coverage
;

require_once(__DIR__ . '/../../../../../runner.php');

class html extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\report\fields\runner\coverage\cli')
		;
	}

	public function test__construct()
	{
		$field = new coverage\html($projectName = uniqid(), $destinationDirectory = uniqid());

		$this->assert
			->string($field->getProjectName())->isEqualTo($projectName)
			->string($field->getDestinationDirectory())->isEqualTo($destinationDirectory)
			->string($field->getTemplatesDirectory())->isEqualTo(atoum\directory . '/resources/templates/coverage')
			->object($field->getPrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getCoverageColorizer())->isEqualTo(new colorizer())
			->object($field->getUrlPrompt())->isEqualTo(new prompt())
			->object($field->getUrlColorizer())->isEqualTo(new colorizer())
			->object($field->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			->object($field->getLocale())->isEqualTo(new locale())
			->object($field->getTemplateParser())->isInstanceOf('mageekguy\atoum\template\parser')
			->variable($field->getCoverage())->isNull()
			->array($field->getSrcDirectories())->isEmpty()
		;

		$field = new coverage\html(
			$projectName = uniqid(),
			$destinationDirectory = uniqid(),
			$templatesDirectory = uniqid(),
			$prompt = new prompt(),
			$titleColorizer = new colorizer(),
			$coverageColorizer = new colorizer(),
			$urlPrompt = new prompt(),
			$urlColorizer = new colorizer(),
			$templateParser = new template\parser(),
			$adapter = new atoum\adapter(),
			$locale = new atoum\locale()
		);

		$this->assert
			->string($field->getProjectName())->isEqualTo($projectName)
			->string($field->getDestinationDirectory())->isEqualTo($destinationDirectory)
			->string($field->getTemplatesDirectory())->isEqualTo($templatesDirectory)
			->object($field->getPrompt())->isIdenticalTo($prompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getCoverageColorizer())->isIdenticalTo($coverageColorizer)
			->object($field->getUrlPrompt())->isIdenticalTo($urlPrompt)
			->object($field->getUrlColorizer())->isIdenticalTo($urlColorizer)
			->object($field->getAdapter())->isIdenticalTo($adapter)
			->object($field->getLocale())->isIdenticalTo($locale)
			->object($field->getTemplateParser())->isIdenticalTo($templateParser)
			->variable($field->getCoverage())->isNull()
			->array($field->getSrcDirectories())->isEmpty()
		;
	}

	public function testSetAdapter()
	{
		$field = new coverage\html($projectName = uniqid(), $templatesDirectory = uniqid(), $destinationDirectory = uniqid());

		$this->assert
			->object($field->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($field)
			->object($field->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetUrlPrompt()
	{
		$field = new coverage\html($projectName = uniqid(), $destinationDirectory = uniqid());

		$this->assert
			->object($field->setUrlPrompt($urlPrompt = new prompt()))->isIdenticalTo($field)
			->object($field->getUrlPrompt())->isIdenticalTo($urlPrompt)
		;
	}

	public function testSetUrlColorizer()
	{
		$field = new coverage\html($projectName = uniqid(), $destinationDirectory = uniqid());

		$this->assert
			->object($field->setUrlColorizer($urlColorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getUrlColorizer())->isIdenticalTo($urlColorizer)
		;
	}

	public function testSetTemplatesDirectory()
	{
		$field = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->object($field->setTemplatesDirectory($directory = uniqid()))->isIdenticalTo($field)
			->string($field->getTemplatesDirectory())->isEqualTo($directory)
			->object($field->setTemplatesDirectory($directory = rand(1, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getTemplatesDirectory())->isIdenticalTo((string) $directory)
		;
	}

	public function testSetDestinationDirectory()
	{
		$field = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->object($field->setDestinationDirectory($directory = uniqid()))->isIdenticalTo($field)
			->string($field->getDestinationDirectory())->isEqualTo($directory)
			->object($field->setDestinationDirectory($directory = rand(1, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getDestinationDirectory())->isIdenticalTo((string) $directory)
		;
	}

	public function testSetTemplateParser()
	{
		$field = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->object($field->setTemplateParser($templateParser = new template\parser()))->isIdenticalTo($field)
			->object($field->getTemplateParser())->isIdenticalTo($templateParser)
		;
	}

	public function testSetProjectName()
	{
		$field = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->object($field->setProjectName($projectName = uniqid()))->isIdenticalTo($field)
			->string($field->getProjectName())->isIdenticalTo($projectName)
			->object($field->setProjectName($projectName = rand(1, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getProjectName())->isIdenticalTo((string) $projectName)
		;
	}

	public function testGetDestinationDirectoryIterator()
	{
		$field = new coverage\html(uniqid(), __DIR__);

		$this->assert
			->object($recursiveIteratorIterator = $field->getDestinationDirectoryIterator())->isInstanceOf('recursiveIteratorIterator')
			->object($recursiveDirectoryIterator = $recursiveIteratorIterator->getInnerIterator())->isInstanceOf('recursiveDirectoryIterator')
			->string($recursiveDirectoryIterator->current()->getPathInfo()->getPathname())->isEqualTo(__DIR__)
		;
	}

	public function testGetSrcDirectoryIterators()
	{
		$field = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->array($field->getSrcDirectoryIterators())->isEmpty()
		;

		$field->addSrcDirectory($directory = __DIR__);

		$this->assert
			->array($iterators = $field->getSrcDirectoryIterators())->isEqualTo(array(new \recursiveIteratorIterator(new atoum\iterators\filters\recursives\closure(new \recursiveDirectoryIterator($directory)))))
			->array(current($iterators)->getClosures())->isEmpty()
		;

		$field->addSrcDirectory($directory, $closure = function() {});

		$this->assert
			->array($iterators = $field->getSrcDirectoryIterators())->isEqualTo(array(new \recursiveIteratorIterator(new atoum\iterators\filters\recursives\closure(new \recursiveDirectoryIterator($directory)))))
			->array(current($iterators)->getClosures())->isEqualTo(array($closure))
		;

		$field->addSrcDirectory($otherDirectory = __DIR__ . '/..', $otherClosure = function() {});

		$this->assert
			->array($iterators = $field->getSrcDirectoryIterators())->isEqualTo(array(
						new \recursiveIteratorIterator(new atoum\iterators\filters\recursives\closure(new \recursiveDirectoryIterator($directory))),
						new \recursiveIteratorIterator(new atoum\iterators\filters\recursives\closure(new \recursiveDirectoryIterator($otherDirectory)))
				)
			)
			->array(current($iterators)->getClosures())->isEqualTo(array($closure))
			->array(next($iterators)->getClosures())->isEqualTo(array($otherClosure))
		;

		$field->addSrcDirectory($otherDirectory, $anOtherClosure = function() {});

		$this->assert
			->array($iterators = $field->getSrcDirectoryIterators())->isEqualTo(array(
						new \recursiveIteratorIterator(new atoum\iterators\filters\recursives\closure(new \recursiveDirectoryIterator($directory))),
						new \recursiveIteratorIterator(new atoum\iterators\filters\recursives\closure(new \recursiveDirectoryIterator($otherDirectory)))
				)
			)
			->array(current($iterators)->getClosures())->isEqualTo(array($closure))
			->array(next($iterators)->getClosures())->isEqualTo(array($otherClosure, $anOtherClosure))
		;
	}

	public function testCleanDestinationDirectory()
	{
		$this->mockGenerator
			->generate('mageekguy\atoum\report\fields\runner\coverage\html')
			->generate('splFileInfo')
		;

		$field = new \mock\mageekguy\atoum\report\fields\runner\coverage\html(uniqid(), $destinationDirectoryPath = uniqid(), uniqid(), null, null, null, null, null, null, $adapter = new test\adapter());

		$adapter->rmdir = function() {};
		$adapter->unlink = function() {};

		$inode11Controller = new mock\controller();
		$inode11Controller->__construct = function() {};
		$inode11Controller->isDir = false;
		$inode11Controller->getPathname = $inode11Path = uniqid();

		$inode11 = new \mock\splFileInfo(uniqid(), $inode11Controller);

		$inode12Controller = new mock\controller();
		$inode12Controller->__construct = function() {};
		$inode12Controller->isDir = false;
		$inode12Controller->getPathname = $inode12Path = uniqid();

		$inode12 = new \mock\splFileInfo(uniqid(), $inode12Controller);

		$inode1Controller = new mock\controller();
		$inode1Controller->__construct = function() {};
		$inode1Controller->isDir = true;
		$inode1Controller->getPathname = $inode1Path = uniqid();

		$inode1 = new \mock\splFileInfo(uniqid(), $inode1Controller);

		$inode2Controller = new mock\controller();
		$inode2Controller->__construct = function() {};
		$inode2Controller->isDir = false;
		$inode2Controller->getPathname = $inode2Path = uniqid();

		$inode2 = new \mock\splFileInfo(uniqid(), $inode2Controller);

		$inode31Controller = new mock\controller();
		$inode31Controller->__construct = function() {};
		$inode31Controller->isDir = false;
		$inode31Controller->getPathname = $inode31Path = uniqid();

		$inode31 = new \mock\splFileInfo(uniqid(), $inode31Controller);

		$inode32Controller = new mock\controller();
		$inode32Controller->__construct = function() {};
		$inode32Controller->isDir = false;
		$inode32Controller->getPathname = $inode32Path = uniqid();

		$inode32 = new \mock\splFileInfo(uniqid(), $inode32Controller);

		$inode3Controller = new mock\controller();
		$inode3Controller->__construct = function() {};
		$inode3Controller->isDir = true;
		$inode3Controller->getPathname = $inode3Path = uniqid();

		$inode3 = new \mock\splFileInfo(uniqid(), $inode3Controller);

		$inodeController = new mock\controller();
		$inodeController->__construct = function() {};
		$inodeController->isDir = true;
		$inodeController->getPathname = $destinationDirectoryPath;

		$inode = new \mock\splFileInfo(uniqid(), $inodeController);

		$field->getMockController()->getDestinationDirectoryIterator = array(
			$inode11,
			$inode12,
			$inode1,
			$inode2,
			$inode31,
			$inode32,
			$inode3,
			$inode
		);

		$this->assert
			->object($field->cleanDestinationDirectory())->isIdenticalTo($field)
			->adapter($adapter)
				->call('unlink')->withArguments($inode11Path)->once()
				->call('unlink')->withArguments($inode12Path)->once()
				->call('rmdir')->withArguments($inode1Path)->once()
				->call('unlink')->withArguments($inode2Path)->once()
				->call('unlink')->withArguments($inode31Path)->once()
				->call('unlink')->withArguments($inode32Path)->once()
				->call('rmdir')->withArguments($inode3Path)->once()
				->call('rmdir')->withArguments($destinationDirectoryPath)->never()
		;

		$field->getMockController()->getDestinationDirectoryIterator->throw = new \exception();

		$this->assert
			->object($field->cleanDestinationDirectory())->isIdenticalTo($field)
		;
	}

	public function testAddSrcDirectory()
	{
		$field = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->object($field->addSrcDirectory($srcDirectory = uniqid()))->isIdenticalTo($field)
			->array($field->getSrcDirectories())->isEqualTo(array($srcDirectory => array()))
			->object($field->addSrcDirectory($srcDirectory))->isIdenticalTo($field)
			->array($field->getSrcDirectories())->isEqualTo(array($srcDirectory => array()))
			->object($field->addSrcDirectory($otherSrcDirectory = rand(1, PHP_INT_MAX)))->isIdenticalTo($field)
			->array($field->getSrcDirectories())->isIdenticalTo(array($srcDirectory => array(), (string) $otherSrcDirectory => array()))
			->object($field->addSrcDirectory($srcDirectory, $closure = function() {}))->isIdenticalTo($field)
			->array($field->getSrcDirectories())->isIdenticalTo(array($srcDirectory => array($closure), (string) $otherSrcDirectory => array()))
			->object($field->addSrcDirectory($srcDirectory, $otherClosure = function() {}))->isIdenticalTo($field)
			->array($field->getSrcDirectories())->isIdenticalTo(array($srcDirectory => array($closure, $otherClosure), (string) $otherSrcDirectory => array()))
		;
	}

	public function testSetRootUrl()
	{
		$field = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->object($field->setRootUrl($rootUrl = uniqid()))->isIdenticalTo($field)
			->string($field->getRootUrl())->isIdenticalTo($rootUrl . '/')
			->object($field->setRootUrl($rootUrl = rand(1, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getRootUrl())->isIdenticalTo((string) $rootUrl . '/')
			->object($field->setRootUrl(($rootUrl = uniqid()) . '/'))->isIdenticalTo($field)
			->string($field->getRootUrl())->isIdenticalTo($rootUrl . '/')
		;
	}

	public function testSetWithRunner()
	{
		$field = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->object($field->setWithRunner($runner = new atoum\runner()))->isIdenticalTo($field)
			->variable($field->getCoverage())->isNull()
		;

		$this->assert
			->object($field->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($field)
			->object($field->getCoverage())->isIdenticalTo($runner->getScore()->getCoverage())
		;
	}

	public function testSetReflectionClassInjector()
	{
		$field = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->mockGenerator
			->generate('reflectionClass')
		;

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};

		$reflectionClass = new \mock\reflectionClass(uniqid(), $reflectionClassController);

		$this->assert
			->object($field->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; }))->isIdenticalTo($field)
			->object($field->getReflectionClass(uniqid()))->isIdenticalTo($reflectionClass)
			->exception(function() use ($field) {
						$field->setReflectionClassInjector(function() {});
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Reflection class injector must take one argument')
		;
	}

	public function testGetReflectionClass()
	{
		$field = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->object($field->getReflectionClass(__CLASS__))->isInstanceOf('reflectionClass')
			->string($field->getReflectionClass(__CLASS__)->getName())->isEqualTo(__CLASS__)
		;

		$field->setReflectionClassInjector(function($class) {});

		$this->assert
			->exception(function() use ($field) {
						$field->getReflectionClass(uniqid());
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime\unexpectedValue')
				->hasMessage('Reflection class injector must return a \reflectionClass instance')
		;
	}

	public function test__toString()
	{
		$field = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->castToString($field)->isEqualTo('Code coverage: unknown.' . PHP_EOL)
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\score')
			->generate('mageekguy\atoum\score\coverage')
			->generate('mageekguy\atoum\runner')
			->generate('mageekguy\atoum\template')
			->generate('mageekguy\atoum\template\tag')
			->generate('mageekguy\atoum\template\parser')
			->generate('reflectionClass')
			->generate('reflectionMethod')
			->generate($this->getTestedClassName())
		;

		$coverage = new \mock\mageekguy\atoum\score\coverage();
		$coverageController = $coverage->getMockController();
		$coverageController->count = rand(1, PHP_INT_MAX);
		$coverageController->getClasses = array(
			$className = uniqid() => $classFile = uniqid()
		);
		$coverageController->getMethods = array(
			$className =>
				array(
					$method1Name = uniqid() =>
						array(
							5 => 1,
							6 => 1,
							7 => -1,
							8 => 1,
							9 => -2
						),
					$method3Name = uniqid() =>
						array(
							10 => -2
						),
					$method4Name = uniqid() =>
						array(
							11 => 1,
							12 => -2
						)
				)
		);
		$coverageController->getValue = $coverageValue = rand(1, 10) / 10;
		$coverageController->getValueForClass = $classCoverageValue = rand(1, 10) / 10;
		$coverageController->getValueForMethod = $methodCoverageValue = rand(1, 10) / 10;

		$score = new \mock\mageekguy\atoum\score();
		$score->getMockController()->getCoverage = $coverage;

		$runner = new \mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = $score;

		$classCoverageTemplate = new \mock\mageekguy\atoum\template\tag('classCoverage');
		$classCoverageTemplate
			->addChild($classCoverageAvailableTemplate = new \mock\mageekguy\atoum\template\tag('classCoverageAvailable'))
		;

		$indexTemplate = new \mock\mageekguy\atoum\template();
		$indexTemplate
			->addChild($coverageAvailableTemplate = new \mock\mageekguy\atoum\template\tag('coverageAvailable'))
			->addChild($classCoverageTemplate)
		;

		$indexTemplateController = $indexTemplate->getMockController();
		$indexTemplateController->__set = function() {};
		$indexTemplateController->build = $buildOfIndexTemplate = uniqid();

		$methodTemplate = new \mock\mageekguy\atoum\template();
		$methodTemplateController = $methodTemplate->getMockController();
		$methodTemplateController->__set = function() {};


		$lineTemplate = new \mock\mageekguy\atoum\template\tag('line');
		$lineTemplateController = $lineTemplate->getMockController();
		$lineTemplateController->__set = function() {};

		$coveredLineTemplate = new \mock\mageekguy\atoum\template\tag('coveredLine');
		$coveredLineTemplateController = $coveredLineTemplate->getMockController();
		$coveredLineTemplateController->__set = function() {};

		$notCoveredLineTemplate = new \mock\mageekguy\atoum\template\tag('notCoveredLine');
		$notCoveredLineTemplateController = $notCoveredLineTemplate->getMockController();
		$notCoveredLineTemplateController->__set = function() {};

		$sourceFileTemplate = new \mock\mageekguy\atoum\template\tag('sourceFile');
		$sourceFileTemplateController = $sourceFileTemplate->getMockController();
		$sourceFileTemplateController->__set = function() {};

		$sourceFileTemplate
			->addChild($lineTemplate)
			->addChild($coveredLineTemplate)
			->addChild($notCoveredLineTemplate)
		;

		$methodCoverageAvailableTemplate = new \mock\mageekguy\atoum\template\tag('methodCoverageAvailable');

		$methodTemplate = new \mock\mageekguy\atoum\template\tag('method');
		$methodTemplate->addChild($methodCoverageAvailableTemplate);

		$methodsTemplate = new \mock\mageekguy\atoum\template\tag('methods');
		$methodsTemplate->addChild($methodTemplate);

		$classTemplate = new \mock\mageekguy\atoum\template();
		$classTemplateController = $classTemplate->getMockController();
		$classTemplateController->__set = function() {};

		$classTemplate
			->addChild($methodsTemplate)
			->addChild($sourceFileTemplate)
		;

		$reflectedClassController = new mock\controller();
		$reflectedClassController->__construct = function() {};
		$reflectedClassController->getName = $className;

		$reflectedClass = new \mock\reflectionClass(uniqid(), $reflectedClassController);

		$otherReflectedClassController = new mock\controller();
		$otherReflectedClassController->__construct = function() {};
		$otherReflectedClassController->getName = uniqid();

		$otherReflectedClass = new \mock\reflectionClass(uniqid(), $otherReflectedClassController);

		$reflectedMethod1Controller = new mock\controller();
		$reflectedMethod1Controller->__construct = function() {};
		$reflectedMethod1Controller->getName = $method1Name;
		$reflectedMethod1Controller->isAbstract = false;
		$reflectedMethod1Controller->getDeclaringClass = $reflectedClass;
		$reflectedMethod1Controller->getStartLine = 5;

		$reflectedMethod1 = new \mock\reflectionMethod(uniqid(), uniqid(), $reflectedMethod1Controller);

		$reflectedMethod2Controller = new mock\controller();
		$reflectedMethod2Controller->__construct = function() {};
		$reflectedMethod2Controller->getName = $method2Name = uniqid();
		$reflectedMethod2Controller->isAbstract = false;
		$reflectedMethod2Controller->getDeclaringClass = $otherReflectedClass;
		$reflectedMethod2Controller->getStartLine = 5;

		$reflectedMethod2 = new \mock\reflectionMethod(uniqid(), uniqid(), $reflectedMethod2Controller);

		$reflectedMethod3Controller = new mock\controller();
		$reflectedMethod3Controller->__construct = function() {};
		$reflectedMethod3Controller->getName = $method3Name;
		$reflectedMethod3Controller->isAbstract = true;
		$reflectedMethod3Controller->getDeclaringClass = $reflectedClass;
		$reflectedMethod3Controller->getStartLine = 10;

		$reflectedMethod3 = new \mock\reflectionMethod(uniqid(), uniqid(), $reflectedMethod3Controller);

		$reflectedMethod4Controller = new mock\controller();
		$reflectedMethod4Controller->__construct = function() {};
		$reflectedMethod4Controller->getName = $method4Name;
		$reflectedMethod4Controller->isAbstract = false;
		$reflectedMethod4Controller->getDeclaringClass = $reflectedClass;
		$reflectedMethod4Controller->getStartLine = 11;

		$reflectedMethod4 = new \mock\reflectionMethod(uniqid(), uniqid(), $reflectedMethod4Controller);

		$reflectedClassController->getMethods = array($reflectedMethod1, $reflectedMethod2, $reflectedMethod3, $reflectedMethod4);

		$templateParser = new \mock\mageekguy\atoum\template\parser();

		$field = new \mock\mageekguy\atoum\report\fields\runner\coverage\html($projectName = uniqid(), $destinationDirectory = uniqid(), $templatesDirectory = uniqid());
		$field
			->setTemplateParser($templateParser)
			->setAdapter($adapter = new test\adapter())
		;

		$fieldController = $field->getMockController();
		$fieldController->cleanDestinationDirectory = function() {};
		$fieldController->getReflectionClass = $reflectedClass;

		$field
			->setWithRunner($runner, atoum\runner::runStop)
			->setRootUrl($rootUrl = uniqid())
		;

		$templateParserController = $templateParser->getMockController();
		$templateParserController->parseFile = function($path) use ($templatesDirectory, $indexTemplate, $classTemplate){
			switch ($path)
			{
				case $templatesDirectory . '/index.tpl':
					return $indexTemplate;

				case $templatesDirectory . '/class.tpl':
					return $classTemplate;
			}
		};

		$adapter->mkdir = function() {};
		$adapter->file_put_contents = function() {};
		$adapter->filemtime = $filemtime = time();
		$adapter->fopen = $classResource = uniqid();
		$adapter->fgets = false;
		$adapter->fgets[1] = $line1 = uniqid();
		$adapter->fgets[2] = $line2 = uniqid();
		$adapter->fgets[3] = $line3 = uniqid();
		$adapter->fgets[4] = $line4 = uniqid();
		$adapter->fgets[5] = $line5 = uniqid();
		$adapter->fgets[6] = $line6 = uniqid();
		$adapter->fgets[7] = $line7 = uniqid();
		$adapter->fgets[8] = $line8 = uniqid();
		$adapter->fgets[9] = $line9 = uniqid();
		$adapter->fgets[10] = $line10 = uniqid();
		$adapter->fgets[11] = $line11 = uniqid();
		$adapter->fgets[12] = $line12 = uniqid();
		$adapter->fgets[13] = $line13 = uniqid();
		$adapter->fgets[14] = $line14 = uniqid();
		$adapter->fgets[15] = $line15 = uniqid();
		$adapter->fclose = function() {};
		$adapter->copy = function() {};

		$this->assert
			->object($field->getCoverage())->isIdenticalTo($coverage)
			->castToString($field)->isIdenticalTo(sprintf($field->getLocale()->_('Code coverage: %3.2f%%.'),  round($coverageValue * 100, 2)) . PHP_EOL . 'Details of code coverage are available at ' . $rootUrl . '/.' . PHP_EOL)
			->mock($coverage)->call('count')->once()
			->mock($field)
				->call('cleanDestinationDirectory')
			->mock($coverage)
				->call('count')->once()
				->call('getClasses')->once()
				->call('getMethods')->once()
				->call('getValueForClass')->withArguments($className)->atLeastOnce()
				->call('getValueForMethod')->withArguments($className, $method1Name)->once()
				->call('getValueForMethod')->withArguments($className, $method2Name)->never()
				->call('getValueForMethod')->withArguments($className, $method3Name)->never()
				->call('getValueForMethod')->withArguments($className, $method4Name)->once()
			->mock($templateParser)
				->call('parseFile')->withArguments($templatesDirectory . '/index.tpl', null)->once()
				->call('parseFile')->withArguments($templatesDirectory . '/class.tpl', null)->once()
			->mock($indexTemplate)
				->call('__set')->withArguments('projectName', $projectName)->once()
				->call('__set')->withArguments('rootUrl', $rootUrl . '/')->once()
				->call('__get')->withArguments('coverageAvailable')->once()
				->call('__get')->withArguments('classCoverage')->once()
			->mock($coverageAvailableTemplate)
				->call('build')->withArguments(array('coverageValue' => round($coverageValue * 100, 2)))->once()
			->mock($classTemplate)
				->call('__set')->withArguments('rootUrl', $rootUrl . '/')->once()
				->call('__set')->withArguments('projectName' , $projectName)->once()
				->call('__set')->withArguments('className', $className)->once()
				->call('__get')->withArguments('methods')->once()
				->call('__get')->withArguments('sourceFile')->once()
				->call('build')->once()
			->mock($classCoverageTemplate)
				->call('__set')->withArguments('className', $className)->once()
				->call('__set')->withArguments('classUrl', str_replace('\\', '/', $className) . coverage\html::htmlExtensionFile)->once()
				->call('build')->once()
			->mock($classCoverageAvailableTemplate)
				->call('build')->withArguments(array('classCoverageValue' => round($classCoverageValue * 100, 2)))->once()
				->call('resetData')->atLeastOnce()
			->mock($methodsTemplate)
				->call('build')->once()
				->call('resetData')->atLeastOnce()
			->mock($methodTemplate)
				->call('build')->atLeastOnce()
				->call('resetData')->atLeastOnce()
			->mock($methodCoverageAvailableTemplate)
				->call('__set')->withArguments('methodName', $method1Name)->once()
				->call('__set')->withArguments('methodName', $method2Name)->never()
				->call('__set')->withArguments('methodName', $method3Name)->never()
				->call('__set')->withArguments('methodName', $method4Name)->once()
				->call('__set')->withArguments('methodCoverageValue', round($methodCoverageValue * 100, 2))->atLeastOnce()
				->call('build')->atLeastOnce()
				->call('resetData')->atLeastOnce()
			->mock($lineTemplate)
				->call('__set')->withArguments('code', $line1)->once()
				->call('__set')->withArguments('code', $line2)->once()
				->call('__set')->withArguments('code', $line3)->once()
				->call('__set')->withArguments('code', $line4)->once()
				->call('__set')->withArguments('code', $line9)->once()
				->call('__set')->withArguments('code', $line12)->once()
			->mock($coveredLineTemplate)
				->call('__set')->withArguments('code', $line5)->once()
				->call('__set')->withArguments('code', $line6)->once()
				->call('__set')->withArguments('code', $line8)->once()
				->call('__set')->withArguments('code', $line11)->once()
			->mock($notCoveredLineTemplate)
				->call('__set')->withArguments('code', $line7)->once()
			->adapter($adapter)
				->call('file_put_contents')->withArguments($destinationDirectory . '/index.html', $buildOfIndexTemplate)->once()
				->call('fopen')->withArguments($classFile, 'r')->once()
				->call('fgets')->withArguments($classResource)->atLeastOnce()
				->call('fclose')->withArguments($classResource)->once()
		;

		$indexTemplateController->build->throw = new \exception($errorMessage = uniqid());

		$this->assert
			->castToString($field)->isIdenticalTo(sprintf($field->getLocale()->_('Code coverage: %3.2f%%.'),  round($coverageValue * 100, 2)) . PHP_EOL . 'Unable to generate code coverage at ' . $rootUrl . '/: ' . $errorMessage . '.' . PHP_EOL)
		;
	}
}

?>
