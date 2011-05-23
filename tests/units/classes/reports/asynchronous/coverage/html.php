<?php

namespace mageekguy\atoum\tests\units\reports\asynchronous\coverage;

use
	\mageekguy\atoum,
	\mageekguy\atoum\test,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\template,
	\mageekguy\atoum\reports\asynchronous\coverage
;

require_once(__DIR__ . '/../../../../runner.php');

class html extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('\mageekguy\atoum\reports\asynchronous')
		;
	}

	public function test__construct()
	{
		$report = new coverage\html($projectName = uniqid(), $templatesDirectory = uniqid(), $destinationDirectory = uniqid());

		$this->assert
			->string($report->getProjectName())->isEqualTo($projectName)
			->string($report->getTemplatesDirectory())->isEqualTo($templatesDirectory)
			->string($report->getDestinationDirectory())->isEqualTo($destinationDirectory)
			->object($report->getTemplateParser())->isInstanceOf('\mageekguy\atoum\template\parser')
			->object($report->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->object($report->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
		;

		$report = new coverage\html($projectName = uniqid(), $templatesDirectory = uniqid(), $destinationDirectory = uniqid(), $templateParser = new template\parser(), $locale = new atoum\locale(), $adapter = new atoum\adapter());

		$this->assert
			->string($report->getProjectName())->isEqualTo($projectName)
			->string($report->getTemplatesDirectory())->isEqualTo($templatesDirectory)
			->string($report->getDestinationDirectory())->isEqualTo($destinationDirectory)
			->object($report->getTemplateParser())->isEqualTo($templateParser)
			->object($report->getLocale())->isIdenticalTo($locale)
			->object($report->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetTemplatesDirectory()
	{
		$report = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->object($report->setTemplatesDirectory($directory = uniqid()))->isIdenticalTo($report)
			->string($report->getTemplatesDirectory())->isEqualTo($directory)
			->object($report->setTemplatesDirectory($directory = rand(1, PHP_INT_MAX)))->isIdenticalTo($report)
			->string($report->getTemplatesDirectory())->isIdenticalTo((string) $directory)
		;
	}

	public function testSetDestinationDirectory()
	{
		$report = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->object($report->setDestinationDirectory($directory = uniqid()))->isIdenticalTo($report)
			->string($report->getDestinationDirectory())->isEqualTo($directory)
			->object($report->setDestinationDirectory($directory = rand(1, PHP_INT_MAX)))->isIdenticalTo($report)
			->string($report->getDestinationDirectory())->isIdenticalTo((string) $directory)
		;
	}

	public function testSetTemplateParser()
	{
		$report = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->object($report->setTemplateParser($templateParser = new template\parser()))->isIdenticalTo($report)
			->object($report->getTemplateParser())->isIdenticalTo($templateParser)
		;
	}

	public function testSetProjectName()
	{
		$report = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->object($report->setProjectName($projectName = uniqid()))->isIdenticalTo($report)
			->string($report->getProjectName())->isIdenticalTo($projectName)
			->object($report->setProjectName($projectName = rand(1, PHP_INT_MAX)))->isIdenticalTo($report)
			->string($report->getProjectName())->isIdenticalTo((string) $projectName)
		;
	}

	public function testSetDirectoryIteratorInjector()
	{
		$report = new coverage\html(uniqid(), uniqid(), uniqid());

		$directoryIterator = new \directoryIterator(__DIR__);

		$this->assert
			->object($report->setDirectoryIteratorInjector($directoryIteratorInjector = function($directory) use ($directoryIterator) { return $directoryIterator; }))->isIdenticalTo($report)
			->object($report->getDirectoryIterator(uniqid()))->isIdenticalTo($directoryIterator)
		;
	}

	public function testGetDirectoryIterator()
	{
		$report = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->object($report->getDirectoryIterator(__DIR__))->isInstanceOf('\directoryIterator')
		;
	}

	public function testCleanDestinationDirectory()
	{
		$report = new coverage\html(uniqid(), uniqid(), $directoryPath = uniqid(), null, null, $adapter = new test\adapter());

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

		$report->setDirectoryIteratorInjector(function($path) use (
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
			->object($report->cleanDestinationDirectory())->isIdenticalTo($report)
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
		$report = new coverage\html(uniqid(), uniqid(), uniqid());

		$this->assert
			->object($report->setRootUrl($rootUrl = uniqid()))->isIdenticalTo($report)
			->string($report->getRootUrl())->isIdenticalTo($rootUrl)
			->object($report->setRootUrl($rootUrl = rand(1, PHP_INT_MAX)))->isIdenticalTo($report)
			->string($report->getRootUrl())->isIdenticalTo((string) $rootUrl)
		;

	}

	public function testRunnerStop()
	{
		$this
			->mock('\mageekguy\atoum\score')
			->mock('\mageekguy\atoum\score\coverage')
			->mock('\mageekguy\atoum\runner')
			->mock('\mageekguy\atoum\template')
			->mock('\mageekguy\atoum\template\tag')
			->mock('\mageekguy\atoum\template\parser')
			->mock('\mageekguy\atoum\reports\asynchronous\coverage\html')
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

		$report = new mock\mageekguy\atoum\reports\asynchronous\coverage\html($projectName = uniqid(), $templatesDirectory = uniqid(), $destinationDirectory = uniqid(), $templateParser, null, $adapter = new test\adapter());
		$reportController = $report->getMockController();
		$reportController->cleanDestinationDirectory = function() {};

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
			->object($report->runnerStop($runner))->isIdenticalTo($report)
			->mock($runner)->call('getScore')
			->mock($score)->call('getCoverage')
			->mock($coverage)->call('count')
		;

		$this->assert
			->object($report->runnerStop($runner))->isIdenticalTo($report)
			->mock($report)
				->call('cleanDestinationDirectory')
			->mock($runner)->call('getScore')
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
