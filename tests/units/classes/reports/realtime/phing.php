<?php

namespace mageekguy\atoum\tests\units\reports\realtime;

use
	mageekguy\atoum,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields,
	mageekguy\atoum\reports\realtime\phing as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class phing extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\reports\realtime');
	}

	public function test__construct()
	{
		$this
			->define($phpPathField = new fields\runner\php\path\cli())
				->and($phpPathField
					->setPrompt(new prompt(PHP_EOL))
					->setTitleColorizer(new colorizer('1;36'))
				)
			->define($phpVersionField = new fields\runner\php\version\cli())
				->and($phpVersionField
					->setTitlePrompt(new prompt(PHP_EOL))
					->setTitleColorizer(new colorizer('1;36'))
					->setVersionPrompt(new prompt(' ', new colorizer('1;36')))
				)
			->define($runnerTestsDurationField = new fields\runner\duration\cli())
				->and($runnerTestsDurationField
					->setPrompt(new prompt(PHP_EOL))
					->setTitleColorizer(new colorizer('1;36'))
				)
			->define($runnerTestsMemoryField = new fields\runner\tests\memory\phing())
				->and($runnerTestsMemoryField
					->setPrompt(new prompt(PHP_EOL))
					->setTitleColorizer(new colorizer('1;36'))
				)
			->define($runnerTestsCoverageField = new fields\runner\tests\coverage\phing())
				->and($runnerTestsCoverageField
					->setTitlePrompt(new prompt(PHP_EOL))
					->setClassPrompt(new prompt(' ', new colorizer('1;36')))
					->setMethodPrompt(new prompt('  ', new colorizer('1;36')))
					->setTitleColorizer(new colorizer('1;36'))
				)
			->define($runnerResultField = new fields\runner\result\cli())
				->and($runnerResultField
					->setPrompt(new prompt(PHP_EOL))
					->setSuccessColorizer(new colorizer('0;37', '42'))
					->setFailureColorizer(new colorizer('0;37', '41'))
				)
			->define($runnerFailuresField = new fields\runner\failures\cli())
				->and($runnerFailuresField
					->setTitlePrompt(new prompt(PHP_EOL))
					->setTitleColorizer(new colorizer('0;31'))
					->setMethodPrompt(new prompt(' ', new colorizer('0;31')))
				)
			->define($runnerOutputsField = new fields\runner\outputs\cli())
				->and($runnerOutputsField
					->setTitlePrompt(new prompt(PHP_EOL))
					->setTitleColorizer(new colorizer('1;36'))
					->setMethodPrompt(new prompt(' ', new colorizer('1;36')))
				)
			->define($runnerErrorsField = new fields\runner\errors\cli())
				->and($runnerErrorsField
					->setTitlePrompt(new prompt(PHP_EOL))
					->setTitleColorizer(new colorizer('0;33'))
					->setMethodPrompt(new prompt(' ', new colorizer('0;33')))
				)
			->define($runnerExceptionsField = new fields\runner\exceptions\cli())
				->and($runnerExceptionsField
					->setTitlePrompt(new prompt(PHP_EOL))
					->setTitleColorizer(new colorizer('0;35'))
					->setMethodPrompt(new prompt(' ', new colorizer('0;35')))
				)
			->define($runnerUncompletedField = new fields\runner\tests\uncompleted\cli())
				->and($runnerUncompletedField
					->setTitlePrompt(new prompt(PHP_EOL))
					->setTitleColorizer(new colorizer('0;37'))
					->setMethodPrompt(new prompt(' ', new colorizer('0;37')))
					->setOutputPrompt(new prompt('  ', new colorizer('0;37')))
				)
			->define($runnerVoidField = new fields\runner\tests\void\cli())
				->and($runnerVoidField
					->setTitlePrompt(new prompt(PHP_EOL))
					->setTitleColorizer(new colorizer('0;34'))
					->setMethodPrompt(new prompt(' ', new colorizer('0;34')))
				)
			->define($runnerSkippedField = new fields\runner\tests\skipped\cli())
				->and($runnerSkippedField
					->setTitlePrompt(new prompt(PHP_EOL))
					->setTitleColorizer(new colorizer('0;90'))
					->setMethodPrompt(new prompt(' ', new colorizer('0;90')))
				)
			->define($testRunField = new fields\test\run\phing())
				->and($testRunField
					->setPrompt(new prompt(PHP_EOL))
					->setColorizer(new colorizer('1;36'))
				)
			->define($testDurationField = new fields\test\duration\phing())
				->and($testDurationField
					->setPrompt(new prompt(' ', new colorizer('1;36')))
				)
			->define($testMemoryField = new fields\test\memory\phing())
				->and($testMemoryField
					->setPrompt(new prompt(' ', new colorizer('1;36')))
				)
			->if($report = new testedClass())
			->then
				->boolean($report->progressIsShowed())->isTrue()
				->boolean($report->codeCoverageIsShowed())->isTrue()
				->boolean($report->missingCodeCoverageIsShowed())->isTrue()
				->boolean($report->durationIsShowed())->isTrue()
				->boolean($report->memoryIsShowed())->isTrue()
				->variable($report->getCodeCoverageReportPath())->isNull()
				->variable($report->getCodeCoverageReportUrl())->isNull()
				->array($report->getFields())->isEqualTo(array(
						$phpPathField,
						$phpVersionField,
						$runnerTestsDurationField,
						$runnerTestsMemoryField,
						$runnerTestsCoverageField,
						$runnerResultField,
						$runnerFailuresField,
						$runnerOutputsField,
						$runnerErrorsField,
						$runnerExceptionsField,
						$runnerUncompletedField,
						$runnerVoidField,
						$runnerSkippedField,
						$testRunField,
						new fields\test\event\phing(),
						$testDurationField,
						$testMemoryField,
					)
				)

		  ;
	}

	public function testShowProgress()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->showProgress())->isIdenticalTo($report)
				->boolean($report->progressIsShowed())->isTrue()
			->if($report->hideProgress())
			->then
				->object($report->showProgress())->isIdenticalTo($report)
				->boolean($report->progressIsShowed())->isTrue()
		;
	}

	public function testHideProgress()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->hideProgress())->isIdenticalTo($report)
				->boolean($report->progressIsShowed())->isFalse()
			->if($report->showProgress())
			->then
				->object($report->hideProgress())->isIdenticalTo($report)
				->boolean($report->progressIsShowed())->isFalse()
		;

	}

	public function testShowCodeCoverage()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->showCodeCoverage())->isIdenticalTo($report)
				->boolean($report->codeCoverageIsShowed())->isTrue()
			->if($report->hideCodeCoverage())
			->then
				->object($report->showCodeCoverage())->isIdenticalTo($report)
				->boolean($report->codeCoverageIsShowed())->isTrue()
		;
	}

	public function testHideCodeCoverage()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->hideCodeCoverage())->isIdenticalTo($report)
				->boolean($report->codeCoverageIsShowed())->isFalse()
			->if($report->showCodeCoverage())
			->then
				->object($report->hideCodeCoverage())->isIdenticalTo($report)
				->boolean($report->codeCoverageIsShowed())->isFalse()
		;
	}

	public function testShowMissingCodeCoverage()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->showMissingCodeCoverage())->isIdenticalTo($report)
				->boolean($report->missingCodeCoverageIsShowed())->isTrue()
			->if($report->hideMissingCodeCoverage())
			->then
				->object($report->showMissingCodeCoverage())->isIdenticalTo($report)
				->boolean($report->missingCodeCoverageIsShowed())->isTrue()
		;
	}

	public function testHideMissingCodeCoverage()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->hideMissingCodeCoverage())->isIdenticalTo($report)
				->boolean($report->missingCodeCoverageIsShowed())->isFalse()
			->if($report->showMissingCodeCoverage())
			->then
				->object($report->hideMissingCodeCoverage())->isIdenticalTo($report)
				->boolean($report->missingCodeCoverageIsShowed())->isFalse()
		;
	}

	public function testShowDuration()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->showDuration())->isIdenticalTo($report)
				->boolean($report->durationIsShowed())->isTrue()
			->if($report->hideDuration())
			->then
				->object($report->showDuration())->isIdenticalTo($report)
				->boolean($report->durationIsShowed())->isTrue()
		;
	}

	public function testHideDuration()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->hideDuration())->isIdenticalTo($report)
				->boolean($report->durationIsShowed())->isFalse()
			->if($report->showDuration())
			->then
				->object($report->hideDuration())->isIdenticalTo($report)
				->boolean($report->durationIsShowed())->isFalse()
		;
	}

	public function testShowMemory()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->showMemory())->isIdenticalTo($report)
				->boolean($report->memoryIsShowed())->isTrue()
			->if($report->hideMemory())
			->then
				->object($report->showMemory())->isIdenticalTo($report)
				->boolean($report->memoryIsShowed())->isTrue()
		;
	}

	public function testHideMemory()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->hideMemory())->isIdenticalTo($report)
				->boolean($report->memoryIsShowed())->isFalse()
			->if($report->showMemory())
			->then
				->object($report->hideMemory())->isIdenticalTo($report)
				->boolean($report->memoryIsShowed())->isFalse()
		;
	}

	public function testSetCodeCoverageReportPath()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->setCodeCoverageReportPath($path = uniqid()))->isIdenticalTo($report)
				->string($report->getCodeCoverageReportPath())->isEqualTo($path)
				->object($report->setCodeCoverageReportPath())->isIdenticalTo($report)
				->variable($report->getCodeCoverageReportPath())->isNull()
		;
	}

	public function testSetCodeCoverageReportUrl()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->setCodeCoverageReportUrl($url = uniqid()))->isIdenticalTo($report)
				->string($report->getCodeCoverageReportUrl())->isEqualTo($url)
				->object($report->setCodeCoverageReportUrl())->isIdenticalTo($report)
				->variable($report->getCodeCoverageReportUrl())->isNull()
		;
	}

	public function testSetCodeCoverageReportProjectName()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->setCodeCoverageReportProjectName($url = uniqid()))->isIdenticalTo($report)
				->string($report->getCodeCoverageReportProjectName())->isEqualTo($url)
				->object($report->setCodeCoverageReportProjectName())->isIdenticalTo($report)
				->variable($report->getCodeCoverageReportProjectName())->isNull()
		;
	}
}
