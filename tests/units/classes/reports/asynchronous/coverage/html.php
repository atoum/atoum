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

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getCoverage = $coverage;

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = $score;

		$codeCoverageTemplate = new mock\mageekguy\atoum\template\tag('class', 'codeCoverage');
		$codeCoverageTemplate
			->addChild(new template\tag('classUrl'))
			->addChild(new template\tag('classFile'))
			->addChild(new template\tag('classDate'))
		;

		$indexTemplate = new mock\mageekguy\atoum\template();
		$indexTemplate->addChild(new template\tag('projectName'));
		$indexTemplateController = $indexTemplate->getMockController();
		$indexTemplateController->getById = $codeCoverageTemplate;
		$indexTemplateController->build = $buildOfIndexTemplate = uniqid();

		$templateParser = new mock\mageekguy\atoum\template\parser();
		$templateParserController = $templateParser->getMockController();
		$templateParserController->parseFile = $indexTemplate;

		$report = new coverage\html($projectName = uniqid(), $templatesDirectory = uniqid(), $destinationDirectory = uniqid(), $templateParser, null, $adapter = new test\adapter());

		$adapter->file_put_contents = function() {};
		$adapter->filemtime = $filemtime = rand(1, PHP_INT_MAX);

		$this->assert
			->object($report->runnerStop($runner))->isIdenticalTo($report)
			->mock($runner)->call('getScore')
			->mock($score)->call('getCoverage')
			->mock($coverage)->call('count')
		;

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

		$this->assert
			->object($report->runnerStop($runner))->isIdenticalTo($report)
			->mock($runner)->call('getScore')
			->mock($coverage)
				->call('count')
				->call('getClasses')
				->call('getMethods')
			->mock($templateParser)->call('parseFile', array($templatesDirectory . '/index.tpl', null))
			->mock($indexTemplate)
				->call('__set', array('projectName', $projectName))
				->call('getById', array('codeCoverage', true))
				->call('build')
			->mock($codeCoverageTemplate)
				->call('__set', array('classDate', $filemtime))
				->call('__set', array('classFile', $classFile))
				->call('build')
			->adapter($adapter)
				->call('file_put_contents', array($destinationDirectory . '/index.html', $buildOfIndexTemplate))
				->call('filemtime', array($classFile))
		;
	}
}

?>
