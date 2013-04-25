<?php
namespace {
	class Task {}
	class FileSet {}
	class BuildException extends Exception {}
}

namespace tests\units {
	use
		atoum,
		AtoumTask as testedClass
	;

	require_once __DIR__ . '/../../runner.php';
	require_once __DIR__ . '/../../../../resources/phing/AtoumTask.php';

	class AtoumTask extends atoum
	{
		public function test__construct()
		{
			$this
				->if($task = new testedClass())
				->then
					->object($task->getRunner())->isInstanceOf('\\mageekguy\\atoum\\runner')
				->if($task = new testedClass($runner = new atoum\runner()))
				->then
					->object($task->getRunner())->isIdenticalTo($runner)
			;
		}

		public function testGetSetRunner()
		{
			$this
				->if($task = new testedClass())
				->then
					->object($task->getRunner())->isInstanceOf('\\mageekguy\\atoum\\runner')
					->object($task->setRunner())->isIdenticalTo($task)
					->object($task->getRunner())->isInstanceOf('\\mageekguy\\atoum\\runner')
					->object($task->setRunner($runner = new atoum\runner()))->isIdenticalTo($task)
					->object($task->getRunner())->isIdenticalTo($runner)
			;
		}

		public function testCodeCoverageEnabled()
		{
			$this
				->if($task = new testedClass())
				->then
					->boolean($task->codeCoverageEnabled())->isFalse()
				->if($task->setCodeCoverageReportPath(uniqid()))
				->then
					->boolean($task->codeCoverageEnabled())->isTrue()
				->if($task = new testedClass())
				->and($task->setCodeCoverage(true))
				->then
					->boolean($task->codeCoverageEnabled())->isTrue()
			;
		}

		public function testExecute()
		{
			$this
				->mockGenerator->shuntParentClassCalls()
				->if($runner = new \mock\mageekguy\atoum\runner())
				->and($this->calling($runner)->run = new atoum\score())
				->and($task = new testedClass($runner))
				->then
					->object($task->execute())->isIdenticalTo($task)
					->mock($runner)
						->call('addReport')->once()
						->call('disableCodeCoverage')->once()
						->call('run')->once()
				->if($runner->getMockController()->resetCalls())
				->and($task->setPhpPath($phpPath = uniqid()))
				->then
					->object($task->execute())->isIdenticalTo($task)
					->mock($runner)
						->call('setPhpPath')->withArguments($phpPath)->once()
				->if($runner->getMockController()->resetCalls())
				->and($task->setBootstrap($bootstrapFile = uniqid()))
				->then
					->object($task->execute())->isIdenticalTo($task)
					->mock($runner)
						->call('setBootstrapFile')->withArguments($bootstrapFile)->once()
				->if($runner->getMockController()->resetCalls())
				->and($task->setMaxChildren($maxChildren = rand(1, PHP_INT_MAX)))
				->then
					->object($task->execute())->isIdenticalTo($task)
					->mock($runner)
						->call('setMaxChildrenNumber')->withArguments($maxChildren)->once()
				->if($runner->getMockController()->resetCalls())
				->and($task->setCodeCoverage(true))
				->then
					->object($task->execute())->isIdenticalTo($task)
					->mock($runner)
						->call('enableCodeCoverage')->once()
				->if($runner->getMockController()->resetCalls())
				->and($task->setCodeCoverage(false))
				->and($task->setCodeCoverageReportPath(uniqid()))
				->then
					->object($task->execute())->isIdenticalTo($task)
					->mock($runner)
						->call('enableCodeCoverage')->once()
				->if($runner->getMockController()->resetCalls())
				->and($task->setCodeCoverageXunitPath(uniqid()))
				->then
					->object($task->execute())->isIdenticalTo($task)
					->mock($runner)
						->call('addReport')->twice()
				->if($score = new \mock\mageekguy\atoum\score())
				->and($this->calling($runner)->run = $score)
				->and($this->calling($score)->getUncompletedMethodNumber = rand(1, PHP_INT_MAX))
				->then
					->exception(function() use ($task) {
							$task->execute();
						}
					)
						->isInstanceOf('\\BuildException')
						->hasMessage('Tests did not pass')
				->if($this->calling($score)->getUncompletedMethodNumber = 0)
				->and($this->calling($score)->getFailNumber = rand(1, PHP_INT_MAX))
				->then
					->exception(function() use ($task) {
							$task->execute();
						}
					)
						->isInstanceOf('\\BuildException')
						->hasMessage('Tests did not pass')
				->if($this->calling($score)->getFailNumber = 0)
				->and($this->calling($score)->getErrorNumber = rand(1, PHP_INT_MAX))
				->then
					->exception(function() use ($task) {
							$task->execute();
						}
					)
						->isInstanceOf('\\BuildException')
						->hasMessage('Tests did not pass')
				->if($this->calling($score)->getErrorNumber = 0)
				->and($this->calling($score)->getExceptionNumber = rand(1, PHP_INT_MAX))
				->then
					->exception(function() use ($task) {
							$task->execute();
						}
					)
						->isInstanceOf('\\BuildException')
						->hasMessage('Tests did not pass')
				->if($this->calling($score)->getExceptionNumber = 0)
				->and($this->calling($score)->getRuntimeExceptionNumber = rand(1, PHP_INT_MAX))
				->then
					->exception(function() use ($task) {
							$task->execute();
						}
					)
						->isInstanceOf('\\BuildException')
						->hasMessage('Tests did not pass')
			;
		}

		public function testConfigureDefaultReport()
		{
			$this
				->if($task = new testedClass())
				->then
					->object($task->configureDefaultReport())->isInstanceOf('\\mageekguy\\atoum\\reports\\realtime\\phing')
				->if($report = new atoum\reports\realtime\phing())
				->then
					->object($task->configureDefaultReport($report))->isIdenticalTo($report)
				->if($report = new \mock\mageekguy\atoum\reports\realtime\phing())
				->then
					->object($task->configureDefaultReport($report))->isIdenticalTo($report)
					->mock($report)
						->call('addWriter')->once()
				->if($report->getMockController()->resetCalls())
				->and($task->setShowProgress(true))
				->then
					->object($task->configureDefaultReport($report))->isIdenticalTo($report)
					->mock($report)
						->call('showProgress')->once()
				->if($task->setShowProgress(false))
				->then
					->object($task->configureDefaultReport($report))->isIdenticalTo($report)
					->mock($report)
						->call('hideProgress')->once()
				->if($report->getMockController()->resetCalls())
				->and($task->setShowDuration(true))
				->then
					->object($task->configureDefaultReport($report))->isIdenticalTo($report)
					->mock($report)
						->call('showDuration')->once()
				->if($task->setShowDuration(false))
				->then
					->object($task->configureDefaultReport($report))->isIdenticalTo($report)
					->mock($report)
						->call('hideDuration')->once()
				->if($report->getMockController()->resetCalls())
				->and($task->setShowMemory(true))
				->then
					->object($task->configureDefaultReport($report))->isIdenticalTo($report)
					->mock($report)
						->call('showMemory')->once()
				->if($task->setShowMemory(false))
				->then
					->object($task->configureDefaultReport($report))->isIdenticalTo($report)
					->mock($report)
						->call('hideMemory')->once()
				->if($report->getMockController()->resetCalls())
				->and($task->setShowCodeCoverage(true))
				->then
					->object($task->configureDefaultReport($report))->isIdenticalTo($report)
					->mock($report)
						->call('showCodeCoverage')->once()
				->if($task->setShowCodeCoverage(false))
				->then
					->object($task->configureDefaultReport($report))->isIdenticalTo($report)
					->mock($report)
						->call('hideCodeCoverage')->once()
				->if($report->getMockController()->resetCalls())
				->and($task->setShowMissingCodeCoverage(true))
				->then
					->object($task->configureDefaultReport($report))->isIdenticalTo($report)
					->mock($report)
						->call('showMissingCodeCoverage')->once()
				->if($task->setShowMissingCodeCoverage(false))
				->then
					->object($task->configureDefaultReport($report))->isIdenticalTo($report)
					->mock($report)
						->call('hideMissingCodeCoverage')->once()
			;
		}

		public function testConfigureAsynchronousReport()
		{
			$this
				->if($task = new testedClass())
				->and($report = new \mock\mageekguy\atoum\reports\asynchronous())
				->then
					->object($task->configureAsynchronousReport($report, uniqid()))->isIdenticalTo($report)
					->mock($report)
						->call('addWriter')->once()
			;
		}

		public function testCreateFileSet()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->createFileSet())->isInstanceOf('\\FileSet')
			;
		}

		public function testSetBootstrap()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setBootstrap(uniqid()))->isIdenticalTo($task)
			;
		}

		public function testSetCodeCoverage()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setCodeCoverage((bool) rand(0, 1)))->isIdenticalTo($task)
			;
		}

		public function testSetAtoumPharPath()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setAtoumPharPath(uniqid()))->isIdenticalTo($task)
			;
		}

		public function testSetPhpPath()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setPhpPath(uniqid()))->isIdenticalTo($task)
			;
		}

		public function testSetShowCodeCoverage()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setShowCodeCoverage((bool) rand(0, 1)))->isIdenticalTo($task)
			;
		}

		public function testSetShowDuration()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setShowDuration((bool) rand(0, 1)))->isIdenticalTo($task)
			;
		}

		public function testSetShowMemory()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setShowMemory((bool) rand(0, 1)))->isIdenticalTo($task)
			;
		}

		public function testSetShowMissingCodeCoverage()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setShowMissingCodeCoverage((bool) rand(0, 1)))->isIdenticalTo($task)
			;
		}

		public function testSetShowProgress()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setShowProgress((bool) rand(0, 1)))->isIdenticalTo($task)
			;
		}

		public function testSetAtoumAutoloaderPath()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setAtoumAutoloaderPath(uniqid()))->isIdenticalTo($task)
			;
		}

		public function testSetCodeCoverageReportPath()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setCodeCoverageReportPath(uniqid()))->isIdenticalTo($task)
			;
		}

		public function testSetCodeCoverageReportUrl()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setCodeCoverageReportUrl(uniqid()))->isIdenticalTo($task)
			;
		}

		public function testSetMaxChildren()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setMaxChildren(rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
			;
		}

		public function testGetSetCodeCoverageXunitPath()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setCodeCoverageXunitPath($path = uniqid()))->isIdenticalTo($task)
					->string($task->getCodeCoverageXunitPath())->isEqualTo($path)
			;
		}

		public function testSetCodeCoverageCloverPath()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setCodeCoverageCloverPath($path = uniqid()))->isIdenticalTo($task)
			;
		}
	}
}
