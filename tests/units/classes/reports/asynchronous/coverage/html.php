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
		$report = new coverage\html($templatesDirectory = uniqid(), $destinationDirectory = uniqid());

		$this->assert
			->string($report->getTemplatesDirectory())->isEqualTo($templatesDirectory)
			->string($report->getDestinationDirectory())->isEqualTo($destinationDirectory)
			->object($report->getTemplateParser())->isInstanceOf('\mageekguy\atoum\template\parser')
			->object($report->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->object($report->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
		;

		$report = new coverage\html($templatesDirectory = uniqid(), $destinationDirectory = uniqid(), $templateParser = new template\parser(), $locale = new atoum\locale(), $adapter = new atoum\adapter());

		$this->assert
			->string($report->getTemplatesDirectory())->isEqualTo($templatesDirectory)
			->string($report->getDestinationDirectory())->isEqualTo($destinationDirectory)
			->object($report->getTemplateParser())->isEqualTo($templateParser)
			->object($report->getLocale())->isIdenticalTo($locale)
			->object($report->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetTemplatesDirectory()
	{
		$report = new coverage\html(uniqid(), uniqid());

		$this->assert
			->object($report->setTemplatesDirectory($directory = uniqid()))->isIdenticalTo($report)
			->string($report->getTemplatesDirectory())->isEqualTo($directory)
			->object($report->setTemplatesDirectory($directory = rand(1, PHP_INT_MAX)))->isIdenticalTo($report)
			->string($report->getTemplatesDirectory())->isEqualTo($directory)
		;
	}

	public function testSetDestinationDirectory()
	{
		$report = new coverage\html(uniqid(), uniqid());

		$this->assert
			->object($report->setDestinationDirectory($directory = uniqid()))->isIdenticalTo($report)
			->string($report->getDestinationDirectory())->isEqualTo($directory)
			->object($report->setDestinationDirectory($directory = rand(1, PHP_INT_MAX)))->isIdenticalTo($report)
			->string($report->getDestinationDirectory())->isEqualTo($directory)
		;
	}

	public function testSetTemplateParser()
	{
		$report = new coverage\html(uniqid(), uniqid());

		$this->assert
			->object($report->setTemplateParser($templateParser = new template\parser()))->isIdenticalTo($report)
			->object($report->getTemplateParser())->isIdenticalTo($templateParser)
		;
	}

	public function testRunnerStop()
	{
		$this
			->mock('\mageekguy\atoum\score')
			->mock('\mageekguy\atoum\runner')
			->mock('\mageekguy\atoum\template\parser')
			->mock('\mageekguy\atoum\reports\asynchronous\coverage\html')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getCoverage = array();

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = $score;

		$templateParser = new mock\mageekguy\atoum\template\parser();

		$report = new coverage\html($templatesDirectory = uniqid(), $destinationDirectory = uniqid(), $templateParser, null, $adapter = new test\adapter());

		$this->assert
			->object($report->runnerStop($runner))->isIdenticalTo($report)
			->mock($runner)->call('getScore')
			->mock($score)->call('getCoverage')
		;

		$score->getMockController()->getCoverage = array();
	}
}

?>
