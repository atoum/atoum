<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\coverage;

use
	\mageekguy\atoum,
	\mageekguy\atoum\test,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\template,
	\mageekguy\atoum\report\fields\runner\coverage
;

require_once(__DIR__ . '/../../../../../runner.php');

class html extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('\mageekguy\atoum\report\fields\runner\coverage\string')
			->string(coverage\html::defaultPrompt)->isEqualTo('> ')
			->string(coverage\html::defaultAlternatePrompt)->isEqualTo('=> ')
		;
	}

	public function test__construct()
	{
		$field = new coverage\html($projectName = uniqid(), $templatesDirectory = uniqid(), $destinationDirectory = uniqid());

		$this->assert
			->string($field->getProjectName())->isEqualTo($projectName)
			->string($field->getTemplatesDirectory())->isEqualTo($templatesDirectory)
			->string($field->getDestinationDirectory())->isEqualTo($destinationDirectory)
			->object($field->getTemplateParser())->isInstanceOf('\mageekguy\atoum\template\parser')
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->object($field->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
			->string($field->getPrompt())->isEqualTo(coverage\html::defaultPrompt)
			->string($field->getAlternatePrompt())->isEqualTo(coverage\html::defaultAlternatePrompt)
			->variable($field->getCoverage())->isNull()
		;

		$field = new coverage\html($projectName = uniqid(), $templatesDirectory = uniqid(), $destinationDirectory = uniqid(), $templateParser = new template\parser(), $adapter = new atoum\adapter(), $locale = new atoum\locale(), $prompt = uniqid(), $alternatePrompt = uniqid());

		$this->assert
			->string($field->getProjectName())->isEqualTo($projectName)
			->string($field->getTemplatesDirectory())->isEqualTo($templatesDirectory)
			->string($field->getDestinationDirectory())->isEqualTo($destinationDirectory)
			->object($field->getTemplateParser())->isEqualTo($templateParser)
			->object($field->getAdapter())->isIdenticalTo($adapter)
			->object($field->getLocale())->isIdenticalTo($locale)
			->string($field->getPrompt())->isEqualTo($prompt)
			->string($field->getAlternatePrompt())->isEqualTo($alternatePrompt)
			->variable($field->getCoverage())->isNull()
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

	public function testSetAlternatePrompt()
	{
		$field = new coverage\html($projectName = uniqid(), $templatesDirectory = uniqid(), $destinationDirectory = uniqid());

		$this->assert
			->object($field->setAlternatePrompt($alternatePrompt = uniqid()))->isIdenticalTo($field)
			->string($field->getAlternatePrompt())->isIdenticalTo($alternatePrompt)
			->object($field->setAlternatePrompt($alternatePrompt = rand(1, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getAlternatePrompt())->isIdenticalTo((string) $alternatePrompt)
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

	public function testSetDirectoryIteratorInjector()
	{
		$field = new coverage\html(uniqid(), uniqid(), uniqid());

		$directoryIterator = new \directoryIterator(__DIR__);

		$this->assert
			->object($field->setDirectoryIteratorInjector($directoryIteratorInjector = function($directory) use ($directoryIterator) { return $directoryIterator; }))->isIdenticalTo($field)
			->object($field->getDirectoryIterator(uniqid()))->isIdenticalTo($directoryIterator)
		;
	}

	public function testGetDirectoryIterator()
	{
		$field = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->object($field->getDirectoryIterator(__DIR__))->isInstanceOf('\directoryIterator')
		;
	}

	public function testCleanDestinationDirectory()
	{
		$field = new coverage\html(uniqid(), uniqid(), $directoryPath = uniqid(), null, $adapter = new test\adapter());

		$adapter->rmdir = function() {};
		$adapter->unlink = function() {};

		$this
			->mock('\directoryIterator')
		;

		$directory11Controller = new mock\controller();
		$directory11Controller->__construct = function() {};
		$directory11Controller->isDir = false;
		$directory11Controller->isDot = false;
		$directory11Controller->getPathname = $directory11Path = uniqid();

		$directory11 = new mock\directoryIterator(uniqid(), $directory11Controller);

		$directory12Controller = new mock\controller();
		$directory12Controller->__construct = function() {};
		$directory12Controller->isDir = false;
		$directory12Controller->isDot = false;
		$directory12Controller->getPathname = $directory12Path = uniqid();

		$directory12 = new mock\directoryIterator(uniqid(), $directory12Controller);

		$directory1Controller = new mock\controller();
		$directory1Controller->__construct = function() {};
		$directory1Controller->isDir = true;
		$directory1Controller->isDot = false;
		$directory1Controller->getPathname = $directory1Path = uniqid();
		$directory1Controller->current == null;
		$directory1Controller->current[1] = $directory11;
		$directory1Controller->current[2] = $directory12;
		$directory1Controller->valid = false;
		$directory1Controller->valid[1] = true;
		$directory1Controller->valid[2] = true;
		$directory1Controller->next = function() {};
		$directory1Controller->rewind = function() {};

		$directory1 = new mock\directoryIterator(uniqid(), $directory1Controller);

		$directory2Controller = new mock\controller();
		$directory2Controller->__construct = function() {};
		$directory2Controller->isDir = false;
		$directory2Controller->isDot = false;
		$directory2Controller->getPathname = $directory2Path = uniqid();

		$directory2 = new mock\directoryIterator(uniqid(), $directory2Controller);

		$directory31Controller = new mock\controller();
		$directory31Controller->__construct = function() {};
		$directory31Controller->isDir = false;
		$directory31Controller->isDot = false;
		$directory31Controller->getPathname = $directory31Path = uniqid();

		$directory31 = new mock\directoryIterator(uniqid(), $directory31Controller);

		$directory32Controller = new mock\controller();
		$directory32Controller->__construct = function() {};
		$directory32Controller->isDir = false;
		$directory32Controller->isDot = false;
		$directory32Controller->getPathname = $directory32Path = uniqid();

		$directory32 = new mock\directoryIterator(uniqid(), $directory32Controller);

		$directory3Controller = new mock\controller();
		$directory3Controller->__construct = function() {};
		$directory3Controller->isDir = true;
		$directory3Controller->isDot = false;
		$directory3Controller->getPathname = $directory3Path = uniqid();
		$directory3Controller->current == null;
		$directory3Controller->current[1] = $directory31;
		$directory3Controller->current[2] = $directory32;
		$directory3Controller->valid = false;
		$directory3Controller->valid[1] = true;
		$directory3Controller->valid[2] = true;
		$directory3Controller->next = function() {};
		$directory3Controller->rewind = function() {};

		$directory3 = new mock\directoryIterator(uniqid(), $directory3Controller);

		$directoryController = new mock\controller();
		$directoryController->__construct = function() {};
		$directoryController->getPathname = $directoryPath;
		$directoryController->current == null;
		$directoryController->current[1] = $directory1;
		$directoryController->current[2] = $directory2;
		$directoryController->current[3] = $directory3;
		$directoryController->valid = false;
		$directoryController->valid[1] = true;
		$directoryController->valid[2] = true;
		$directoryController->valid[3] = true;
		$directoryController->next = function() {};
		$directoryController->rewind = function() {};

		$directory = new mock\directoryIterator(uniqid(), $directoryController);

		$field->setDirectoryIteratorInjector(function($path) use (
				$directory11Path,
				$directory11,
				$directory12Path,
				$directory12,
				$directory1Path,
				$directory1,
				$directory2Path,
				$directory2,
				$directory31Path,
				$directory31,
				$directory32Path,
				$directory32,
				$directory3Path,
				$directory3,
				$directoryPath,
				$directory
			)
			{
				switch ($path)
				{
					case $directory11Path:
						return $directory11;

					case $directory12Path:
						return $directory12;

					case $directory1Path:
						return $directory1;

					case $directory2Path:
						return $directory2;

					case $directory31Path:
						return $directory31;

					case $directory32Path:
						return $directory32;

					case $directory3Path:
						return $directory3;

					case $directoryPath:
						return $directory;
				}
			}
		);

		$this->assert
			->object($field->cleanDestinationDirectory())->isIdenticalTo($field)
			->adapter($adapter)
				->call('unlink', array($directory11Path))
				->call('unlink', array($directory12Path))
				->call('rmdir', array($directory1Path))
				->call('unlink', array($directory2Path))
				->call('unlink', array($directory31Path))
				->call('unlink', array($directory32Path))
				->call('rmdir', array($directory3Path))
				->notCall('rmdir', array($directoryPath))
		;
	}

	public function testSetRootUrl()
	{
		$field = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->object($field->setRootUrl($rootUrl = uniqid()))->isIdenticalTo($field)
			->string($field->getRootUrl())->isIdenticalTo($rootUrl)
			->object($field->setRootUrl($rootUrl = rand(1, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getRootUrl())->isIdenticalTo((string) $rootUrl)
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

	public function test__toString()
	{
		$field = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->castToString($field)->isEqualTo('> Code coverage: unknown.' . PHP_EOL)
		;

		$this
			->mock('\mageekguy\atoum\score')
			->mock('\mageekguy\atoum\score\coverage')
			->mock('\mageekguy\atoum\runner')
			->mock('\mageekguy\atoum\template')
			->mock('\mageekguy\atoum\template\tag')
			->mock('\mageekguy\atoum\template\parser')
			->mock($this->getTestedClassName())
		;

		$coverage = new mock\mageekguy\atoum\score\coverage();
		$coverageController = $coverage->getMockController();
		$coverageController->count = rand(1, PHP_INT_MAX);
		$coverageController->getClasses = array(
			$className = uniqid() => $classFile = uniqid()
		);
		$coverageController->getMethods = array(
			$className =>
				array(
					$methodName = uniqid() =>
						array(
							1 => 1,
							2 => 1,
							3 => 0,
							4 => 1,
							5 => 1
						)
				)
		);
		$coverageController->getValue = $coverageValue = rand(1, 10) / 10;
		$coverageController->getValueForClass = $classCoverageValue = rand(1, 10) / 10;
		$coverageController->getValueForMethod = $methodCoverageValue = rand(1, 10) / 10;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getCoverage = $coverage;

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = $score;

		$coverageTemplate = new mock\mageekguy\atoum\template\tag('class', 'codeCoverage');
		$coverageTemplateController = $coverageTemplate->getMockController();
		$coverageTemplateController->__set = function() {};
		$coverageTemplateController->__isset = true;

		$indexTemplate = new mock\mageekguy\atoum\template();
		$indexTemplateController = $indexTemplate->getMockController();
		$indexTemplateController->__set = function() {};
		$indexTemplateController->__isset = true;
		$indexTemplateController->getById = $coverageTemplate;
		$indexTemplateController->build = $buildOfIndexTemplate = uniqid();

		$methodTemplate = new mock\mageekguy\atoum\template();
		$methodTemplateController = $methodTemplate->getMockController();
		$methodTemplateController->__set = function() {};
		$methodTemplateController->__isset = true;

		$sourceTemplate = new mock\mageekguy\atoum\template();
		$sourceTemplateController = $sourceTemplate->getMockController();
		$sourceTemplateController->__set = function() {};
		$sourceTemplateController->__isset = true;

		$blankLineTemplate = new mock\mageekguy\atoum\template();
		$blankLineTemplateController = $blankLineTemplate->getMockController();
		$blankLineTemplateController->__set = function() {};
		$blankLineTemplateController->__isset = true;

		$coveredLineTemplate = new mock\mageekguy\atoum\template();
		$coveredLineTemplateController = $coveredLineTemplate->getMockController();
		$coveredLineTemplateController->__set = function() {};
		$coveredLineTemplateController->__isset = true;

		$notCoveredLineTemplate = new mock\mageekguy\atoum\template();
		$notCoveredLineTemplateController = $notCoveredLineTemplate->getMockController();
		$notCoveredLineTemplateController->__set = function() {};
		$notCoveredLineTemplateController->__isset = true;

		$classTemplate = new mock\mageekguy\atoum\template();
		$classTemplateController = $classTemplate->getMockController();
		$classTemplateController->__set = function() {};
		$classTemplateController->__isset = true;
		$classTemplateController->getById = function ($id) use ($methodTemplate, $sourceTemplate, $blankLineTemplate, $coveredLineTemplate, $notCoveredLineTemplate) {
			switch ($id)
			{
				case 'method':
					return $methodTemplate;

				case 'source':
					return $sourceTemplate;

				case 'blankLine':
					return $blankLineTemplate;

				case 'coveredLine':
					return $coveredLineTemplate;

				case 'notCoveredLine':
					return $notCoveredLineTemplate;
			}
		};

		$templateParser = new mock\mageekguy\atoum\template\parser();

		$field = new mock\mageekguy\atoum\report\fields\runner\coverage\html($projectName = uniqid(), $templatesDirectory = uniqid(), $destinationDirectory = uniqid(), $templateParser, $adapter = new test\adapter());
		$fieldController = $field->getMockController();
		$fieldController->cleanDestinationDirectory = function() {};

		$field->setWithRunner($runner, atoum\runner::runStop);

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
		$adapter->fopen = false;
		$adapter->copy = function() {};

		$this->assert
			->object($field->getCoverage())->isIdenticalTo($coverage)
			->castToString($field)->isIdenticalTo('> ' . sprintf($field->getLocale()->_('Code coverage: %3.2f%%.'),  round($coverageValue * 100, 2)) . PHP_EOL . '=> Details of code coverage are available at /.' . PHP_EOL)
			->mock($coverage)->call('count')
			->mock($field)
				->call('cleanDestinationDirectory')
			->mock($coverage)
				->call('count')
				->call('getClasses')
				->call('getMethods')
			->mock($templateParser)
				->call('parseFile', array($templatesDirectory . '/index.tpl', null))
				->call('parseFile', array($templatesDirectory . '/class.tpl', null))
			->mock($indexTemplate)
				->call('__set', array('projectName', $projectName))
				->call('getById', array('class', true))
				->call('build')
			->mock($coverageTemplate)
				->call('__set', array('className', $className))
				->call('__set', array('classUrl', ltrim(str_replace('\\', DIRECTORY_SEPARATOR, $className), DIRECTORY_SEPARATOR)))
				->call('build')
			->mock($coverage)
				->call('getValueForMethod', array($className, $methodName))
			->mock($classTemplate)
				->call('__set', array('className', $className))
				->call('__set', array('classCoverageValue', round($classCoverageValue * 100, 2)))
			->mock($methodTemplate)
				->call('__set', array('methodName', $methodName))
				->call('__set', array('methodCoverageValue', round($methodCoverageValue * 100, 2)))
			->adapter($adapter)
				->call('file_put_contents', array($destinationDirectory . '/index.html', $buildOfIndexTemplate))
		;
	}
}

?>
