<?php

namespace
{
	class Task {}
	class FileSet {}
	class BuildException extends Exception {}
}

namespace tests\units
{
	use
		atoum,
		AtoumTask as testedClass
	;

	require_once __DIR__ . '/../../runner.php';

	define('mageekguy\atoum\phing\task\path', atoum\mock\streams\fs\file::get());

	require_once __DIR__ . '/../../../../resources/phing/AtoumTask.php';

	class AtoumTask extends atoum
	{
		public function test__construct()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->getRunner())->isInstanceOf('mageekguy\atoum\runner')
					->variable($task->getBootstrap())->isNull()
					->boolean($task->getCodeCoverage())->isFalse()
					->variable($task->getAtoumPharPath())->isNull()
					->variable($task->getPhpPath())->isNull()
					->boolean($task->getShowCodeCoverage())->isTrue()
					->boolean($task->getShowDuration())->isTrue()
					->boolean($task->getShowMemory())->isTrue()
					->boolean($task->getShowMissingCodeCoverage())->isTrue()
					->boolean($task->getShowProgress())->isTrue()
					->variable($task->getAtoumAutoloaderPath())->isNull()
					->variable($task->getCodeCoverageReportPath())->isNull()
					->variable($task->getCodeCoverageReportUrl())->isNull()
					->variable($task->getCodeCoverageXunitPath())->isNull()
					->variable($task->getCodeCoverageCloverPath())->isNull()
					->variable($task->getCodeCoverageReportExtensionPath())->isNull()
					->variable($task->getCodeCoverageReportExtensionUrl())->isNull()
					->variable($task->getTelemetry())->isNotTrue()
					->variable($task->getTelemetryProjectName())->isNull()
					->integer($task->getMaxChildren())->isZero()
				->if($task = new testedClass($runner = new atoum\runner()))
				->then
					->object($task->getRunner())->isIdenticalTo($runner)
					->variable($task->getBootstrap())->isNull()
					->boolean($task->getCodeCoverage())->isFalse()
					->variable($task->getAtoumPharPath())->isNull()
					->variable($task->getPhpPath())->isNull()
					->boolean($task->getShowCodeCoverage())->isTrue()
					->boolean($task->getShowDuration())->isTrue()
					->boolean($task->getShowMemory())->isTrue()
					->boolean($task->getShowMissingCodeCoverage())->isTrue()
					->boolean($task->getShowProgress())->isTrue()
					->variable($task->getAtoumAutoloaderPath())->isNull()
					->variable($task->getCodeCoverageReportPath())->isNull()
					->variable($task->getCodeCoverageReportUrl())->isNull()
					->variable($task->getCodeCoverageXunitPath())->isNull()
					->variable($task->getCodeCoverageCloverPath())->isNull()
					->variable($task->getCodeCoverageReportExtensionPath())->isNull()
					->variable($task->getCodeCoverageReportExtensionUrl())->isNull()
					->variable($task->getTelemetry())->isNotTrue()
					->variable($task->getTelemetryProjectName())->isNull()
					->integer($task->getMaxChildren())->isZero()
			;
		}

		public function testGetSetRunner()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setRunner($runner = new atoum\runner()))->isIdenticalTo($task)
					->object($task->getRunner())->isIdenticalTo($runner)
					->object($task->setRunner())->isIdenticalTo($task)
					->object($task->getRunner())
						->isInstanceOf('mageekguy\atoum\runner')
						->isNotIdenticalTo($runner)
			;
		}

		public function testCodeCoverageEnabled()
		{
			$this
				->given($task = new testedClass())
				->then
					->boolean($task->codeCoverageEnabled())->isFalse()
				->if($task->setCodeCoverageReportPath(uniqid()))
				->then
					->boolean($task->codeCoverageEnabled())->isTrue()
				->if($task = new testedClass())
				->and($task->setCodeCoverageTreemapPath(uniqid()))
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
						->isInstanceOf('buildException')
						->hasMessage('Tests did not pass')
				->if($this->calling($score)->getUncompletedMethodNumber = 0)
				->and($this->calling($score)->getFailNumber = rand(1, PHP_INT_MAX))
				->then
					->exception(function() use ($task) {
							$task->execute();
						}
					)
						->isInstanceOf('buildException')
						->hasMessage('Tests did not pass')
				->if($this->calling($score)->getFailNumber = 0)
				->and($this->calling($score)->getErrorNumber = rand(1, PHP_INT_MAX))
				->then
					->exception(function() use ($task) {
							$task->execute();
						}
					)
						->isInstanceOf('buildException')
						->hasMessage('Tests did not pass')
				->if($this->calling($score)->getErrorNumber = 0)
				->and($this->calling($score)->getExceptionNumber = rand(1, PHP_INT_MAX))
				->then
					->exception(function() use ($task) {
							$task->execute();
						}
					)
						->isInstanceOf('buildException')
						->hasMessage('Tests did not pass')
				->if($this->calling($score)->getExceptionNumber = 0)
				->and($this->calling($score)->getRuntimeExceptionNumber = rand(1, PHP_INT_MAX))
				->then
					->exception(function() use ($task) {
							$task->execute();
						}
					)
						->isInstanceOf('buildException')
						->hasMessage('Tests did not pass')
			;
		}

		public function testExecuteWithCodeCoverageTreemap()
		{
			$this
				->mockGenerator->shuntParentClassCalls()
				->if($runner = new \mock\mageekguy\atoum\runner())
				->and($this->calling($runner)->run = new atoum\score())
				->and($task = new \mock\AtoumTask($runner))
				->and($this->calling($task)->configureDefaultReport = $report = new \mock\mageekguy\atoum\reports\realtime\phing())
				->and($this->calling($task)->configureCoverageTreemapField = $field = new atoum\report\fields\runner\coverage\treemap(uniqid(), uniqid()))
				->and($task->setCodeCoverageTreemapPath(uniqid()))
				->then
					->object($task->execute())->isIdenticalTo($task)
					->mock($report)
						->call('addField')->withArguments($field)->once()
			;
		}

		public function testConfigureDefaultReport()
		{
			$this
				->if($task = new testedClass())
				->then
					->object($task->configureDefaultReport())->isInstanceOf('mageekguy\atoum\reports\realtime\phing')
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

		public function testConfigureCoverageTreemapField()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($field = $task->configureCoverageTreemapField($path = uniqid()))->isInstanceOf('\\mageekguy\\atoum\\report\\fields\\runner\\coverage\\treemap')
					->string($field->getDestinationDirectory())->isEqualTo($path)
					->string($field->getTreemapUrl())->isEqualTo('file://' . $path . '/index.html')
				->if($field = $task->configureCoverageTreemapField($path = uniqid(), $url = uniqid()))
				->then
					->string($field->getHtmlReportBaseUrl())->isEqualTo($url)
			;
		}

		public function testCreateFileSet()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->createFileSet())->isInstanceOf('fileSet')
			;
		}

		public function testSetBootstrap()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setBootstrap($bootstrap = uniqid()))->isIdenticalTo($task)
					->string($task->getBootstrap())->isEqualTo($bootstrap)
					->object($task->setBootstrap($bootstrap = rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
					->string($task->getBootstrap())->isEqualTo((string) $bootstrap)
			;
		}

		public function testSetCodeCoverage()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setCodeCoverage(true))->isIdenticalTo($task)
					->boolean($task->getCodeCoverage())->isTrue()
					->object($task->setCodeCoverage(false))->isIdenticalTo($task)
					->boolean($task->getCodeCoverage())->isFalse()
					->object($task->setCodeCoverage(rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
					->boolean($task->getCodeCoverage())->isTrue()
					->object($task->setCodeCoverage(0))->isIdenticalTo($task)
					->boolean($task->getCodeCoverage())->isFalse()
			;
		}

		public function testSetAtoumPharPath()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setAtoumPharPath($path = uniqid()))->isIdenticalTo($task)
					->string($task->getAtoumPharPath())->isEqualTo($path)
					->object($task->setAtoumPharPath($path = rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
					->string($task->getAtoumPharPath())->isEqualTo((string) $path)

			;
		}

		public function testSetPhpPath()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setPhpPath($path = uniqid()))->isIdenticalTo($task)
					->string($task->getPhpPath())->isEqualTo($path)
					->object($task->setPhpPath($path = rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
					->string($task->getPhpPath())->isEqualTo((string) $path)
			;
		}

		public function testSetShowCodeCoverage()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setShowCodeCoverage(true))->isIdenticalTo($task)
					->boolean($task->getShowCodeCoverage())->isTrue()
					->object($task->setShowCodeCoverage(false))->isIdenticalTo($task)
					->boolean($task->getShowCodeCoverage())->isFalse()
					->object($task->setShowCodeCoverage(rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
					->boolean($task->getShowCodeCoverage())->isTrue()
					->object($task->setShowCodeCoverage(0))->isIdenticalTo($task)
					->boolean($task->getShowCodeCoverage())->isFalse()
			;
		}

		public function testSetShowDuration()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setShowDuration(true))->isIdenticalTo($task)
					->boolean($task->getShowDuration())->isTrue()
					->object($task->setShowDuration(false))->isIdenticalTo($task)
					->boolean($task->getShowDuration())->isFalse()
					->object($task->setShowDuration(rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
					->boolean($task->getShowDuration())->isTrue()
					->object($task->setShowDuration(0))->isIdenticalTo($task)
					->boolean($task->getShowDuration())->isFalse()
			;
		}

		public function testSetShowMemory()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setShowMemory(true))->isIdenticalTo($task)
					->boolean($task->getShowMemory())->isTrue()
					->object($task->setShowMemory(false))->isIdenticalTo($task)
					->boolean($task->getShowMemory())->isFalse()
					->object($task->setShowMemory(rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
					->boolean($task->getShowMemory())->isTrue()
					->object($task->setShowMemory(0))->isIdenticalTo($task)
					->boolean($task->getShowMemory())->isFalse()
			;
		}

		public function testSetShowMissingCodeCoverage()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setShowMissingCodeCoverage(true))->isIdenticalTo($task)
					->boolean($task->getShowMissingCodeCoverage())->isTrue()
					->object($task->setShowMissingCodeCoverage(false))->isIdenticalTo($task)
					->boolean($task->getShowMissingCodeCoverage())->isFalse()
					->object($task->setShowMissingCodeCoverage(rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
					->boolean($task->getShowMissingCodeCoverage())->isTrue()
					->object($task->setShowMissingCodeCoverage(0))->isIdenticalTo($task)
					->boolean($task->getShowMissingCodeCoverage())->isFalse()
			;
		}

		public function testSetShowProgress()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setShowProgress(true))->isIdenticalTo($task)
					->boolean($task->getShowProgress())->isTrue()
					->object($task->setShowProgress(false))->isIdenticalTo($task)
					->boolean($task->getShowProgress())->isFalse()
					->object($task->setShowProgress(rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
					->boolean($task->getShowProgress())->isTrue()
					->object($task->setShowProgress(0))->isIdenticalTo($task)
					->boolean($task->getShowProgress())->isFalse()
			;
		}

		public function testSetAtoumAutoloaderPath()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setAtoumAutoloaderPath($path = uniqid()))->isIdenticalTo($task)
					->string($task->getAtoumAutoloaderPath())->isEqualTo($path)
					->object($task->setAtoumAutoloaderPath($path = rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
					->string($task->getAtoumAutoloaderPath())->isEqualTo((string) $path)
			;
		}

		public function testSetCodeCoverageReportPath()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setCodeCoverageReportPath($path = uniqid()))->isIdenticalTo($task)
					->string($task->getCodeCoverageReportPath())->isEqualTo($path)
					->object($task->setCodeCoverageReportPath($path = rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
					->string($task->getCodeCoverageReportPath())->isEqualTo((string) $path)
			;
		}

		public function testSetCodeCoverageReportUrl()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setCodeCoverageReportUrl($path = uniqid()))->isIdenticalTo($task)
					->string($task->getCodeCoverageReportUrl())->isEqualTo($path)
					->object($task->setCodeCoverageReportUrl($path = rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
					->string($task->getCodeCoverageReportUrl())->isEqualTo((string) $path)
			;
		}

		public function testSetCodeCoverageTreemapPath()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setCodeCoverageTreemapPath(uniqid()))->isIdenticalTo($task)
			;
		}

		public function testSetCodeCoverageTreemapUrl()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setCodeCoverageTreemapUrl(uniqid()))->isIdenticalTo($task)
			;
		}

		public function testSetMaxChildren()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setMaxChildren($maxChildren = rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
					->integer($task->getMaxChildren())->isEqualTo($maxChildren)
					->object($task->setMaxChildren((string) ($maxChildren = rand(1, PHP_INT_MAX))))->isIdenticalTo($task)
					->integer($task->getMaxChildren())->isEqualTo($maxChildren)
			;
		}

		public function testGetSetCodeCoverageXunitPath()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setCodeCoverageXunitPath($path = uniqid()))->isIdenticalTo($task)
					->string($task->getCodeCoverageXunitPath())->isEqualTo($path)
					->object($task->setCodeCoverageXunitPath($path = rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
					->string($task->getCodeCoverageXunitPath())->isEqualTo((string) $path)
			;
		}

		public function testSetCodeCoverageCloverPath()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setCodeCoverageCloverPath($path = uniqid()))->isIdenticalTo($task)
					->string($task->getCodeCoverageCloverPath())->isEqualTo($path)
					->object($task->setCodeCoverageCloverPath($path = rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
					->string($task->getCodeCoverageCloverPath())->isEqualTo((string) $path)
			;
		}

		public function testGetSetBranchAndPathCoverage()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setBranchAndPathCoverage(true))->isIdenticalTo($task)
					->boolean($task->getBranchAndPathCoverage())->isTrue()
			;
		}

		public function testBranchAndPathCoverageEnabled()
		{
			$this
				->given($task = new testedClass())
				->then
					->boolean($task->branchAndPathCoverageEnabled())->isFalse()
				->if($task->setBranchAndPathCoverage(true))
				->then
					->boolean($task->branchAndPathCoverageEnabled())->isTrue()
			;
		}

		public function testGetSetCodeCoverageReportExtensionPath()
		{
			$this
				->given($task = new testedClass())
				->then
				    ->object($task->setCodeCoverageReportExtensionPath($path = uniqid()))->isIdenticalTo($task)
				    ->string($task->getCodeCoverageReportExtensionPath())->isEqualTo($path)
				    ->object($task->setCodeCoverageReportExtensionPath($path = rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
				    ->string($task->getCodeCoverageReportExtensionPath())->isEqualTo((string) $path)
			;
		}

		public function testGetSetCodeCoverageReportExtensionUrl()
		{
			$this
				->given($task = new testedClass())
				->then
				    ->object($task->setCodeCoverageReportExtensionUrl($path = uniqid()))->isIdenticalTo($task)
				    ->string($task->getCodeCoverageReportExtensionUrl())->isEqualTo($path)
				    ->object($task->setCodeCoverageReportExtensionUrl($path = rand(1, PHP_INT_MAX)))->isIdenticalTo($task)
				    ->string($task->getCodeCoverageReportExtensionUrl())->isEqualTo((string) $path)
			;
		}

		public function testGetSetTelemetry()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setTelemetry(true))->isIdenticalTo($task)
					->boolean($task->getTelemetry())->isTrue()
			;
		}

		public function testGetSetTelemetryProjectName()
		{
			$this
				->given($task = new testedClass())
				->then
					->object($task->setTelemetryProjectName($projectName = uniqid() . '/' . uniqid()))->isIdenticalTo($task)
					->string($task->getTelemetryProjectName())->isEqualTo($projectName)
			;
		}

		public function testTelemetryEnabled()
		{
			$this
				->given($task = new testedClass())
				->then
					->boolean($task->telemetryEnabled())->isFalse()
				->if($task->setTelemetry(true))
				->then
					->boolean($task->telemetryEnabled())->isTrue()
			;
		}
	}
}
