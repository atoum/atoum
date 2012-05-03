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

require_once __DIR__ . '/../../../../../runner.php';

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
		$this->assert
			->if($field = new coverage\html($projectName = uniqid(), $destinationDirectory = uniqid()))
			->then
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
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
			->if($field = new coverage\html($projectName = uniqid(), $destinationDirectory = uniqid(), $templatesDirectory = uniqid(), $prompt = new prompt(), $titleColorizer = new colorizer(), $coverageColorizer = new colorizer(), $urlPrompt = new prompt(), $urlColorizer = new colorizer(), $templateParser = new template\parser(), $adapter = new atoum\adapter(), $locale = new atoum\locale()))
			->then
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
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
		;
	}

	public function testSetAdapter()
	{
		$this->assert
			->if($field = new coverage\html(uniqid(), uniqid()))
			->then
				->object($field->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($field)
				->object($field->getAdapter())->isIdenticalTo($adapter)
			->if($field = new coverage\html(uniqid(), uniqid(), null, null, null, null, null, null, null, new atoum\adapter()))
			->then
				->object($field->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($field)
				->object($field->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetUrlPrompt()
	{
		$this->assert
			->if($field = new coverage\html(uniqid(), uniqid()))
			->then
				->object($field->setUrlPrompt($urlPrompt = new prompt()))->isIdenticalTo($field)
				->object($field->getUrlPrompt())->isIdenticalTo($urlPrompt)
		;
	}

	public function testSetUrlColorizer()
	{
		$this->assert
			->if($field = new coverage\html(uniqid(), uniqid()))
			->then
				->object($field->setUrlColorizer($urlColorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getUrlColorizer())->isIdenticalTo($urlColorizer)
		;
	}

	public function testSetTemplatesDirectory()
	{

		$this->assert
			->if($field = new coverage\html(uniqid(), uniqid()))
			->then
				->object($field->setTemplatesDirectory($directory = uniqid()))->isIdenticalTo($field)
				->string($field->getTemplatesDirectory())->isEqualTo($directory)
				->object($field->setTemplatesDirectory($directory = rand(1, PHP_INT_MAX)))->isIdenticalTo($field)
				->string($field->getTemplatesDirectory())->isIdenticalTo((string) $directory)
		;
	}

	public function testSetDestinationDirectory()
	{
		$this->assert
			->if($field = new coverage\html(uniqid(), uniqid()))
			->then
				->object($field->setDestinationDirectory($directory = uniqid()))->isIdenticalTo($field)
				->string($field->getDestinationDirectory())->isEqualTo($directory)
				->object($field->setDestinationDirectory($directory = rand(1, PHP_INT_MAX)))->isIdenticalTo($field)
				->string($field->getDestinationDirectory())->isIdenticalTo((string) $directory)
		;
	}

	public function testSetTemplateParser()
	{
		$this->assert
			->if($field = new coverage\html(uniqid(), uniqid(), uniqid()))
			->then
				->object($field->setTemplateParser($templateParser = new template\parser()))->isIdenticalTo($field)
				->object($field->getTemplateParser())->isIdenticalTo($templateParser)
		;
	}

	public function testSetProjectName()
	{
		$this->assert
			->if($field = new coverage\html(uniqid(), uniqid()))
			->then
				->object($field->setProjectName($projectName = uniqid()))->isIdenticalTo($field)
				->string($field->getProjectName())->isIdenticalTo($projectName)
				->object($field->setProjectName($projectName = rand(1, PHP_INT_MAX)))->isIdenticalTo($field)
				->string($field->getProjectName())->isIdenticalTo((string) $projectName)
		;
	}

	public function testGetDestinationDirectoryIterator()
	{

		$this->assert
			->if($field = new coverage\html(uniqid(), __DIR__))
			->then
				->object($recursiveIteratorIterator = $field->getDestinationDirectoryIterator())->isInstanceOf('recursiveIteratorIterator')
				->object($recursiveDirectoryIterator = $recursiveIteratorIterator->getInnerIterator())->isInstanceOf('recursiveDirectoryIterator')
				->string($recursiveDirectoryIterator->current()->getPathInfo()->getPathname())->isEqualTo(__DIR__)
		;
	}

	public function testGetSrcDirectoryIterators()
	{
		$this->assert
			->if($field = new coverage\html(uniqid(), uniqid()))
			->then
				->array($field->getSrcDirectoryIterators())->isEmpty()
			->if($field->addSrcDirectory($directory = __DIR__))
			->then
				->array($iterators = $field->getSrcDirectoryIterators())->isEqualTo(array(new \recursiveIteratorIterator(new atoum\iterators\filters\recursives\closure(new \recursiveDirectoryIterator($directory)))))
				->array(current($iterators)->getClosures())->isEmpty()
			->if($field->addSrcDirectory($directory, $closure = function() {}))
			->then
				->array($iterators = $field->getSrcDirectoryIterators())->isEqualTo(array(new \recursiveIteratorIterator(new atoum\iterators\filters\recursives\closure(new \recursiveDirectoryIterator($directory)))))
				->array(current($iterators)->getClosures())->isEqualTo(array($closure))
			->if($field->addSrcDirectory($otherDirectory = __DIR__ . '/..', $otherClosure = function() {}))
			->then
				->array($iterators = $field->getSrcDirectoryIterators())->isEqualTo(array(
							new \recursiveIteratorIterator(new atoum\iterators\filters\recursives\closure(new \recursiveDirectoryIterator($directory))),
							new \recursiveIteratorIterator(new atoum\iterators\filters\recursives\closure(new \recursiveDirectoryIterator($otherDirectory)))
					)
				)
				->array(current($iterators)->getClosures())->isEqualTo(array($closure))
				->array(next($iterators)->getClosures())->isEqualTo(array($otherClosure))
			->if($field->addSrcDirectory($otherDirectory, $anOtherClosure = function() {}))
			->then
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
		$this
			->assert
				->if($firstFile = atoum\mock\stream::get('destinationDirectory/aDirectory/firstFile'))
				->and($firstFile->unlink = true)
				->and($secondFile = atoum\mock\stream::get('destinationDirectory/aDirectory/secondFile'))
				->and($secondFile->unlink = true)
				->and($aDirectory = atoum\mock\stream::get('destinationDirectory/aDirectory'))
				->and($aDirectory->opendir = true)
				->and($aDirectory->readdir[1] = 'firstFile')
				->and($aDirectory->readdir[2] = 'secondFile')
				->and($aDirectory->readdir[3] = false)
				->and($emptyDirectory = atoum\mock\stream::get('destinationDirectory/emptyDirectory'))
				->and($emptyDirectory->opendir = true)
				->and($emptyDirectory->readdir[1] = false)
				->and($anOtherFirstFile = atoum\mock\stream::get('destinationDirectory/anOtherDirectory/anOtherFirstFile'))
				->and($anOtherFirstFile->unlink = true)
				->and($anOtherSecondFile = atoum\mock\stream::get('destinationDirectory/anOtherDirectory/anOtherSecondFile'))
				->and($anOtherSecondFile->unlink = true)
				->and($anOtherDirectory = atoum\mock\stream::get('destinationDirectory/anOtherDirectory'))
				->and($anOtherDirectory->opendir = true)
				->and($anOtherDirectory->readdir[1] = 'anOtherFirstFile')
				->and($anOtherDirectory->readdir[2] = 'anOtherSecondFile')
				->and($anOtherDirectory->readdir[3] = false)
				->and($aFile = atoum\mock\stream::get('destinationDirectory/aFile'))
				->and($aFile->unlink = true)
				->and($destinationDirectory = atoum\mock\stream::get('destinationDirectory'))
				->and($destinationDirectory->opendir = true)
				->and($destinationDirectory->readdir[1] = 'aDirectory')
				->and($destinationDirectory->readdir[2] = 'emptyDirectory')
				->and($destinationDirectory->readdir[3] = 'anOtherDirectory')
				->and($destinationDirectory->readdir[4] = 'aFile')
				->and($destinationDirectory->readdir[5] = false)
				->and($field = new \mock\mageekguy\atoum\report\fields\runner\coverage\html(uniqid(), $destinationDirectoryPath = 'atoum://destinationDirectory', uniqid(), null, null, null, null, null, null, $adapter = new test\adapter()))
				->and($adapter->rmdir = function() {})
				->and($adapter->unlink = function() {})
				->then
					->object($field->cleanDestinationDirectory())->isIdenticalTo($field)
					->adapter($adapter)
						->call('unlink')->withArguments('atoum://destinationDirectory/aDirectory/firstFile')->once()
						->call('unlink')->withArguments('atoum://destinationDirectory/aDirectory/secondFile')->once()
						->call('rmdir')->withArguments('atoum://destinationDirectory/aDirectory')->once()
						->call('unlink')->withArguments('atoum://destinationDirectory/anOtherDirectory/anOtherFirstFile')->once()
						->call('unlink')->withArguments('atoum://destinationDirectory/anOtherDirectory/anOtherSecondFile')->once()
						->call('rmdir')->withArguments('atoum://destinationDirectory/anOtherDirectory')->once()
						->call('unlink')->withArguments('atoum://destinationDirectory/aFile')->once()
						->call('rmdir')->withArguments('atoum://destinationDirectory/emptyDirectory')->once()
						->call('rmdir')->withArguments($destinationDirectoryPath)->never()
				->if($field->getMockController()->getDestinationDirectoryIterator->throw = new \exception())
				->then
					->object($field->cleanDestinationDirectory())->isIdenticalTo($field)
		;
	}

	public function testAddSrcDirectory()
	{
		$this->assert
			->if($field = new coverage\html(uniqid(), uniqid()))
			->then
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
		$this->assert
			->if($field = new coverage\html(uniqid(), uniqid(), uniqid()))
			->then
				->object($field->setRootUrl($rootUrl = uniqid()))->isIdenticalTo($field)
				->string($field->getRootUrl())->isIdenticalTo($rootUrl . '/')
				->object($field->setRootUrl($rootUrl = rand(1, PHP_INT_MAX)))->isIdenticalTo($field)
				->string($field->getRootUrl())->isIdenticalTo((string) $rootUrl . '/')
				->object($field->setRootUrl(($rootUrl = uniqid()) . '/'))->isIdenticalTo($field)
				->string($field->getRootUrl())->isIdenticalTo($rootUrl . '/')
		;
	}

	public function testHandleEvent()
	{
		$this->assert
			->if($field = new coverage\html(uniqid(), uniqid()))
			->then
				->boolean($field->handleEvent(atoum\runner::runStart, new atoum\runner()))->isFalse()
				->variable($field->getCoverage())->isNull()
				->boolean($field->handleEvent(atoum\runner::runStop, $runner = new atoum\runner()))->isTrue()
				->object($field->getCoverage())->isIdenticalTo($runner->getScore()->getCoverage())
		;
	}

	public function testSetReflectionClassInjector()
	{
		$this
			->assert
				->if($field = new coverage\html(uniqid(), uniqid(), uniqid()))
				->and($reflectionClassController = new mock\controller())
				->and($reflectionClassController->__construct = function() {})
				->and($reflectionClass = new \mock\reflectionClass(uniqid(), $reflectionClassController))
				->then
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
		$this->assert
			->if($field = new coverage\html(uniqid(), uniqid(), uniqid()))
			->then
				->object($field->getReflectionClass(__CLASS__))->isInstanceOf('reflectionClass')
				->string($field->getReflectionClass(__CLASS__)->getName())->isEqualTo(__CLASS__)
			->if($field->setReflectionClassInjector(function($class) {}))
			->then
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
		$this
			->assert
				->if($field = new coverage\html(uniqid(), uniqid(), uniqid()))
				->then
					->castToString($field)->isEqualTo('Code coverage: unknown.' . PHP_EOL)
				->if($coverage = new \mock\mageekguy\atoum\score\coverage())
				->and($coverageController = $coverage->getMockController())
				->and($coverageController->count = rand(1, PHP_INT_MAX))
				->and($coverageController->getClasses = array(
							$className = uniqid() => $classFile = uniqid()
						)
					)
				->and($coverageController->getMethods = array(
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
						)
					)
				->and($coverageController->getValue = $coverageValue = rand(1, 10) / 10)
				->and($coverageController->getValueForClass = $classCoverageValue = rand(1, 10) / 10)
				->and($coverageController->getValueForMethod = $methodCoverageValue = rand(1, 10) / 10)
				->and($score = new \mock\mageekguy\atoum\score())
				->and($score->getMockController()->getCoverage = $coverage)
				->if($classCoverageTemplate = new \mock\mageekguy\atoum\template\tag('classCoverage'))
				->and($classCoverageTemplate->addChild($classCoverageAvailableTemplate = new \mock\mageekguy\atoum\template\tag('classCoverageAvailable')))
				->and($indexTemplate = new \mock\mageekguy\atoum\template())
				->and($indexTemplate
						->addChild($coverageAvailableTemplate = new \mock\mageekguy\atoum\template\tag('coverageAvailable'))
						->addChild($classCoverageTemplate)
					)
				->and($indexTemplateController = $indexTemplate->getMockController())
				->and($indexTemplateController->__set = function() {})
				->and($indexTemplateController->build = $buildOfIndexTemplate = uniqid())
				->and($methodTemplate = new \mock\mageekguy\atoum\template())
				->and($methodTemplateController = $methodTemplate->getMockController())
				->and($methodTemplateController->__set = function() {})
				->and($lineTemplate = new \mock\mageekguy\atoum\template\tag('line'))
				->and($lineTemplateController = $lineTemplate->getMockController())
				->and($lineTemplateController->__set = function() {})
				->and($coveredLineTemplate = new \mock\mageekguy\atoum\template\tag('coveredLine'))
				->and($coveredLineTemplateController = $coveredLineTemplate->getMockController())
				->and($coveredLineTemplateController->__set = function() {})
				->and($notCoveredLineTemplate = new \mock\mageekguy\atoum\template\tag('notCoveredLine'))
				->and($notCoveredLineTemplateController = $notCoveredLineTemplate->getMockController())
				->and($notCoveredLineTemplateController->__set = function() {})
				->and($sourceFileTemplate = new \mock\mageekguy\atoum\template\tag('sourceFile'))
				->and($sourceFileTemplateController = $sourceFileTemplate->getMockController())
				->and($sourceFileTemplateController->__set = function() {})
				->and($sourceFileTemplate
						->addChild($lineTemplate)
						->addChild($coveredLineTemplate)
						->addChild($notCoveredLineTemplate)
					)
				->and($methodCoverageAvailableTemplate = new \mock\mageekguy\atoum\template\tag('methodCoverageAvailable'))
				->and($methodTemplate = new \mock\mageekguy\atoum\template\tag('method'))
				->and($methodTemplate->addChild($methodCoverageAvailableTemplate))
				->and($methodsTemplate = new \mock\mageekguy\atoum\template\tag('methods'))
				->and($methodsTemplate->addChild($methodTemplate))
				->and($classTemplate = new \mock\mageekguy\atoum\template())
				->and($classTemplateController = $classTemplate->getMockController())
				->and($classTemplateController->__set = function() {})
				->and($classTemplate
						->addChild($methodsTemplate)
						->addChild($sourceFileTemplate)
					)
				->and($reflectedClassController = new mock\controller())
				->and($reflectedClassController->__construct = function() {})
				->and($reflectedClassController->getName = $className)
				->and($reflectedClass = new \mock\reflectionClass(uniqid(), $reflectedClassController))
				->and($otherReflectedClassController = new mock\controller())
				->and($otherReflectedClassController->__construct = function() {})
				->and($otherReflectedClassController->getName = uniqid())
				->and($otherReflectedClass = new \mock\reflectionClass(uniqid(), $otherReflectedClassController))
				->and($reflectedMethod1Controller = new mock\controller())
				->and($reflectedMethod1Controller->__construct = function() {})
				->and($reflectedMethod1Controller->getName = $method1Name)
				->and($reflectedMethod1Controller->isAbstract = false)
				->and($reflectedMethod1Controller->getDeclaringClass = $reflectedClass)
				->and($reflectedMethod1Controller->getStartLine = 5)
				->and($reflectedMethod1 = new \mock\reflectionMethod(uniqid(), uniqid(), $reflectedMethod1Controller))
				->and($reflectedMethod2Controller = new mock\controller())
				->and($reflectedMethod2Controller->__construct = function() {})
				->and($reflectedMethod2Controller->getName = $method2Name = uniqid())
				->and($reflectedMethod2Controller->isAbstract = false)
				->and($reflectedMethod2Controller->getDeclaringClass = $otherReflectedClass)
				->and($reflectedMethod2Controller->getStartLine = 5)
				->and($reflectedMethod2 = new \mock\reflectionMethod(uniqid(), uniqid(), $reflectedMethod2Controller))
				->and($reflectedMethod3Controller = new mock\controller())
				->and($reflectedMethod3Controller->__construct = function() {})
				->and($reflectedMethod3Controller->getName = $method3Name)
				->and($reflectedMethod3Controller->isAbstract = true)
				->and($reflectedMethod3Controller->getDeclaringClass = $reflectedClass)
				->and($reflectedMethod3Controller->getStartLine = 10)
				->and($reflectedMethod3 = new \mock\reflectionMethod(uniqid(), uniqid(), $reflectedMethod3Controller))
				->and($reflectedMethod4Controller = new mock\controller())
				->and($reflectedMethod4Controller->__construct = function() {})
				->and($reflectedMethod4Controller->getName = $method4Name)
				->and($reflectedMethod4Controller->isAbstract = false)
				->and($reflectedMethod4Controller->getDeclaringClass = $reflectedClass)
				->and($reflectedMethod4Controller->getStartLine = 11)
				->and($reflectedMethod4 = new \mock\reflectionMethod(uniqid(), uniqid(), $reflectedMethod4Controller))
				->and($reflectedClassController->getMethods = array($reflectedMethod1, $reflectedMethod2, $reflectedMethod3, $reflectedMethod4))
				->and($templateParser = new \mock\mageekguy\atoum\template\parser())
				->and($field = new \mock\mageekguy\atoum\report\fields\runner\coverage\html($projectName = uniqid(), $destinationDirectory = uniqid(), $templatesDirectory = uniqid()))
				->and($field
						->setTemplateParser($templateParser)
						->setAdapter($adapter = new test\adapter())
					)
				->and($fieldController = $field->getMockController())
				->and($fieldController->cleanDestinationDirectory = function() {})
				->and($fieldController->getReflectionClass = $reflectedClass)
				->and($runner = new \mock\mageekguy\atoum\runner())
				->and($runner->getMockController()->getScore = $score)
				->and($field->setRootUrl($rootUrl = uniqid()))
				->and($templateParserController = $templateParser->getMockController())
				->and($templateParserController->parseFile = function($path) use ($templatesDirectory, $indexTemplate, $classTemplate) {
							switch ($path)
							{
								case $templatesDirectory . '/index.tpl':
									return $indexTemplate;

								case $templatesDirectory . '/class.tpl':
									return $classTemplate;
							}
						}
					)
				->and($adapter->mkdir = function() {})
				->and($adapter->file_put_contents = function() {})
				->and($adapter->filemtime = $filemtime = time())
				->and($adapter->fopen = $classResource = uniqid())
				->and($adapter->fgets = false)
				->and($adapter->fgets[1] = $line1 = uniqid())
				->and($adapter->fgets[2] = $line2 = uniqid())
				->and($adapter->fgets[3] = $line3 = uniqid())
				->and($adapter->fgets[4] = $line4 = uniqid())
				->and($adapter->fgets[5] = $line5 = uniqid())
				->and($adapter->fgets[6] = $line6 = uniqid())
				->and($adapter->fgets[7] = $line7 = uniqid())
				->and($adapter->fgets[8] = $line8 = uniqid())
				->and($adapter->fgets[9] = $line9 = uniqid())
				->and($adapter->fgets[10] = $line10 = uniqid())
				->and($adapter->fgets[11] = $line11 = uniqid())
				->and($adapter->fgets[12] = $line12 = uniqid())
				->and($adapter->fgets[13] = $line13 = uniqid())
				->and($adapter->fgets[14] = $line14 = uniqid())
				->and($adapter->fgets[15] = $line15 = uniqid())
				->and($adapter->fclose = function() {})
				->and($adapter->copy = function() {})
				->and($field->handleEvent(atoum\runner::runStop, $runner))
				->then
					->object($field->getCoverage())->isIdenticalTo($coverage)
					->castToString($field)->isIdenticalTo(sprintf($field->getLocale()->_('Code coverage: %3.2f%%.'),  round($coverageValue * 100, 2)) . PHP_EOL . 'Details of code coverage are available at ' . $rootUrl . '/.' . PHP_EOL)
					->mock($coverage)->call('count')->once()
					->mock($field)
						->call('cleanDestinationDirectory')->once()
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
			->if($indexTemplateController->build->throw = new \exception($errorMessage = uniqid()))
			->then
				->castToString($field)->isIdenticalTo(sprintf($field->getLocale()->_('Code coverage: %3.2f%%.'),  round($coverageValue * 100, 2)) . PHP_EOL . 'Unable to generate code coverage at ' . $rootUrl . '/: ' . $errorMessage . '.' . PHP_EOL)
		;
	}
}

?>
