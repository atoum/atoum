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
			->if($report = new testedClass())
			->then
				->boolean($report->progressIsShowed())->isTrue()
				->boolean($report->codeCoverageIsShowed())->isTrue()
				->boolean($report->missingCodeCoverageIsShowed())->isTrue()
				->boolean($report->durationIsShowed())->isTrue()
				->boolean($report->memoryIsShowed())->isTrue()
				->variable($report->getCodeCoverageReportPath())->isNull()
				->variable($report->getCodeCoverageReportUrl())->isNull()
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
