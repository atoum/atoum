<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\mock,
	mageekguy\atoum\runner as testedClass
;

require_once __DIR__ . '/../runner.php';

class runner extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->hasInterface('mageekguy\atoum\observable')
			->string(atoum\runner::atoumVersionConstant)->isEqualTo('mageekguy\atoum\version')
			->string(atoum\runner::atoumDirectoryConstant)->isEqualTo('mageekguy\atoum\directory')
			->string(atoum\runner::runStart)->isEqualTo('runnerStart')
			->string(atoum\runner::runStop)->isEqualTo('runnerStop')
		;
	}

	public function test__construct()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->getScore())->isInstanceOf('mageekguy\atoum\score')
				->object($runner->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($runner->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($runner->getIncluder())->isInstanceOf('mageekguy\atoum\includer')
				->variable($runner->getTestGenerator())->isNull()
				->object($runner->getTestDirectoryIterator())->isInstanceOf('mageekguy\atoum\iterators\recursives\directory\factory')
				->object($defaultGlobIteratorFactory = $runner->getGlobIteratorFactory())->isInstanceOf('closure')
				->object($defaultGlobIteratorFactory($pattern = uniqid()))->isEqualTo(new \globIterator($pattern))
				->object($defaultReflectionClassFactory = $runner->getReflectionClassFactory())->isInstanceOf('closure')
				->object($defaultReflectionClassFactory($this))->isEqualTo(new \reflectionClass($this))
				->object($defaultTestFactory = $runner->getTestFactory())->isInstanceOf('closure')
				->object($defaultTestFactory(__CLASS__))->isInstanceOf($this)
				->variable($runner->getRunningDuration())->isNull()
				->boolean($runner->codeCoverageIsEnabled())->isTrue()
				->variable($runner->getDefaultReportTitle())->isNull()
				->array($runner->getObservers())->isEmpty()
				->array($runner->getTestPaths())->isEmpty()
				->variable($runner->getXdebugConfig())->isNull()
		;
	}

	public function testSetTestPaths()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->setTestPaths($paths = array(uniqid(), uniqid(), uniqid())))->isIdenticalTo($runner)
				->array($runner->getTestPaths())->isEqualTo($paths)
		;
	}

	public function testResetTestPaths()
	{
		$this
			->if($runner = new testedClass())
			->and($runner->setTestPaths(array(uniqid(), uniqid(), uniqid())))
			->then
				->object($runner->resetTestPaths())->isIdenticalTo($runner)
				->array($runner->getTestPaths())->isEmpty()
		;
	}

	public function testSetPhp()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->setPhp($php = new atoum\php()))->isIdenticalTo($runner)
				->object($runner->getPhp())->isIdenticalTo($php)
				->object($runner->setPhp())->isIdenticalTo($runner)
				->object($runner->getPhp())
					->isEqualTo(new atoum\php())
					->isNotIdenticalTo($php)
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->setAdapter($adapter = new atoum\test\adapter()))->isIdenticalTo($runner)
				->object($runner->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetScore()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->setScore($score = new atoum\runner\score()))->isIdenticalTo($runner)
				->object($runner->getScore())->isIdenticalTo($score);
		;
	}

	public function testSetDefaultReportTtitle()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->setDefaultReportTitle($title = uniqid()))->isIdenticalTo($runner)
				->string($runner->getDefaultReportTitle())->isEqualTo($title)
		;
	}

	public function testGetPhpPath()
	{
		$this
			->if($runner = new testedClass())
			->then
				->string($runner->getPhpPath())->isEqualTo($runner->getPhp()->getBinaryPath())
		;
	}

	public function testSetPhpPath()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->setPhpPath($phpPath = uniqid()))->isIdenticalTo($runner)
				->string($runner->getPhpPath())->isIdenticalTo($phpPath)
		;
	}

	public function testSetTestGenerator()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->setTestGenerator($generator = new test\generator()))->isIdenticalTo($runner)
				->object($runner->getTestGenerator())->isIdenticalTo($generator)
				->object($runner->setTestGenerator())->isIdenticalTo($runner)
				->object($runner->getTestGenerator())
					->isInstanceOf('mageekguy\atoum\test\generator')
					->isNotIdenticalTo($generator)
		;
	}

	public function testEnableDebugMode()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->enableDebugMode())->isIdenticalTo($runner)
				->boolean($runner->debugModeIsEnabled())->isTrue()
				->object($runner->enableDebugMode())->isIdenticalTo($runner)
				->boolean($runner->debugModeIsEnabled())->isTrue()
		;
	}

	public function testDisableDebugMode()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->disableDebugMode())->isIdenticalTo($runner)
				->boolean($runner->debugModeIsEnabled())->isFalse()
				->object($runner->disableDebugMode())->isIdenticalTo($runner)
				->boolean($runner->debugModeIsEnabled())->isFalse()
			->if($runner->enableDebugMode())
			->then
				->object($runner->disableDebugMode())->isIdenticalTo($runner)
				->boolean($runner->debugModeIsEnabled())->isFalse()
		;
	}

	public function testDisallowUsageOfUndefinedMethodInMock()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->disallowUsageOfUndefinedMethodInMock())->isIdenticalTo($runner)
				->boolean($runner->usageOfUndefinedMethodInMockAreAllowed())->isFalse()
				->object($runner->disallowUsageOfUndefinedMethodInMock())->isIdenticalTo($runner)
				->boolean($runner->debugModeIsEnabled())->isFalse()
		;
	}

	public function testAllowUsageOfUndefinedMethodInMock()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->allowUsageOfUndefinedMethodInMock())->isIdenticalTo($runner)
				->boolean($runner->usageOfUndefinedMethodInMockAreAllowed())->isTrue()
				->object($runner->allowUsageOfUndefinedMethodInMock())->isIdenticalTo($runner)
				->boolean($runner->usageOfUndefinedMethodInMockAreAllowed())->isTrue()
			->if($runner->disallowUsageOfUndefinedMethodInMock())
			->then
				->object($runner->allowUsageOfUndefinedMethodInMock())->isIdenticalTo($runner)
				->boolean($runner->usageOfUndefinedMethodInMockAreAllowed())->isTrue()
		;
	}

	public function testSetXdebugConfig()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->setXdebugConfig($value = uniqid()))->isIdenticalTo($runner)
				->string($runner->getXdebugConfig())->isEqualTo($value)
		;
	}

	public function testAddObserver()
	{
		$this
			->if($runner = new testedClass())
			->then
				->array($runner->getObservers())->isEmpty()
				->object($runner->addObserver($observer = new \mock\mageekguy\atoum\observers\runner()))->isIdenticalTo($runner)
				->array($runner->getObservers())->isEqualTo(array($observer))
		;
	}

	public function testRemoveObserver()
	{
		$this
			->if($runner = new testedClass())
			->then
				->array($runner->getObservers())->isEmpty()
				->object($runner->removeObserver(new \mock\mageekguy\atoum\observers\runner()))->isIdenticalTo($runner)
				->array($runner->getObservers())->isEmpty()
			->if($runner->addObserver($observer1 = new \mock\mageekguy\atoum\observers\runner()))
			->and($runner->addObserver($observer2 = new \mock\mageekguy\atoum\observers\runner()))
			->then
				->array($runner->getObservers())->isEqualTo(array($observer1, $observer2))
				->object($runner->removeObserver(new \mock\mageekguy\atoum\observers\runner()))->isIdenticalTo($runner)
				->array($runner->getObservers())->isEqualTo(array($observer1, $observer2))
				->object($runner->removeObserver($observer1))->isIdenticalTo($runner)
				->array($runner->getObservers())->isEqualTo(array($observer2))
				->object($runner->removeObserver($observer2))->isIdenticalTo($runner)
				->array($runner->getObservers())->isEmpty()
		;
	}

	public function testCallObservers()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->callObservers(atoum\runner::runStart))->isIdenticalTo($runner)
			->if($runner->addObserver($observer = new \mock\mageekguy\atoum\observers\runner()))
			->then
				->object($runner->callObservers(atoum\runner::runStart))->isIdenticalTo($runner)
				->mock($observer)->call('handleEvent')->withArguments(atoum\runner::runStart, $runner)->once()
		;
	}

	public function testGetRunningDuration()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->microtime = function() { static $call = 0; return (++$call * 100); })
			->and($adapter->get_declared_classes = array())
			->and($runner = new testedClass())
			->and($runner->setAdapter($adapter))
			->then
				->variable($runner->getRunningDuration())->isNull()
			->if($runner->run())
			->then
				->integer($runner->getRunningDuration())->isEqualTo(100)
			->if(eval('namespace ' . __NAMESPACE__ . ' { class forTestGetRunningDuration extends \mageekguy\atoum\test { public function testSomething() {} public function run(array $runTestMethods = array(), array $tags = array()) { return $this; } } }'))
			->and($adapter->get_declared_classes = array(__NAMESPACE__ . '\forTestGetRunningDuration'))
			->and($runner->run())
			->then
				->integer($runner->getRunningDuration())->isEqualTo(100)
		;
	}

	public function testGetTestNumber()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->get_declared_classes = array())
			->and($runner = new testedClass())
			->and($runner->setAdapter($adapter))
			->then
				->integer($runner->getTestNumber())->isZero()
			->if($runner->run())
			->then
				->integer($runner->getTestNumber())->isZero();
		;
	}

	public function testGetTestMethodNumber()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->get_declared_classes = array())
			->and($runner = new testedClass())
			->and($runner->setAdapter($adapter))
			->then
				->integer($runner->getTestMethodNumber())->isZero()
			->if($runner->run())
			->then
				->integer($runner->getTestMethodNumber())->isZero()
		;
	}

	public function testGetBootstrapFile()
	{
		$this
			->if($runner = new testedClass())
			->and($includer = new \mock\mageekguy\atoum\includer())
			->and($includer->getMockController()->includePath = function() {})
			->and($runner->setIncluder($includer))
			->then
				->object($runner->setBootstrapFile($path = uniqid()))->isIdenticalTo($runner)
				->string($runner->getBootstrapFile())->isEqualTo($path)
				->mock($includer)->call('includePath')->withArguments($path)->once()
		;
	}

	public function testGetAutoloaderFile()
	{
		$this
			->if($runner = new testedClass())
			->and($includer = new \mock\mageekguy\atoum\includer())
			->and($includer->getMockController()->includePath = function() {})
			->and($runner->setIncluder($includer))
			->then
				->object($runner->setAutoloaderFile($path = uniqid()))->isIdenticalTo($runner)
				->string($runner->getAutoloaderFile())->isEqualTo($path)
				->mock($includer)->call('includePath')->withArguments($path)->once()
		;
	}

	public function testHasReports()
	{
		$this
			->if($runner = new testedClass())
			->then
				->boolean($runner->hasReports())->isFalse()
			->if($runner->addReport(new atoum\reports\realtime\cli()))
			->then
				->boolean($runner->hasReports())->isTrue()
		;
	}

	public function testSetReport()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->setReport($report = new atoum\reports\realtime\cli()))->isIdenticalTo($runner)
				->array($runner->getReports())->isEqualTo(array($report))
				->array($runner->getObservers())->contains($report)
				->object($runner->addReport($otherReport = new atoum\reports\realtime\cli()))->isIdenticalTo($runner)
				->array($runner->getReports())->isEqualTo(array($report))
				->array($runner->getObservers())->contains($report)
				->object($runner->setReport($otherReport))->isIdenticalTo($runner)
				->array($runner->getReports())->isEqualTo(array($otherReport))
				->array($runner->getObservers())->contains($otherReport)
				->object($runner->addReport($report))->isIdenticalTo($runner)
				->array($runner->getReports())->isEqualTo(array($otherReport))
				->array($runner->getObservers())->contains($otherReport)
		;
	}

	public function testAddReport()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->addReport($report = new atoum\reports\realtime\cli()))->isIdenticalTo($runner)
				->array($runner->getReports())->isEqualTo(array($report))
				->array($runner->getObservers())->contains($report)
			->if($runner->setReport($otherReport = new atoum\reports\realtime\cli()))
			->then
				->object($runner->addReport($report = new atoum\reports\realtime\cli()))->isIdenticalTo($runner)
				->array($runner->getReports())->isEqualTo(array($otherReport))
				->array($runner->getObservers())->contains($otherReport)
		;
	}

	public function testRemoveReport()
	{
		$this
			->if($runner = new testedClass())
			->then
				->array($runner->getReports())->isEmpty()
				->array($runner->getObservers())->isEmpty()
				->object($runner->removeReport(new atoum\reports\realtime\cli()))->isIdenticalTo($runner)
				->array($runner->getReports())->isEmpty()
				->array($runner->getObservers())->isEmpty()
			->if($report1 = new \mock\mageekguy\atoum\report())
			->and($report2 = new \mock\mageekguy\atoum\report())
			->and($runner->addReport($report1)->addReport($report2))
			->then
				->array($runner->getReports())->isEqualTo(array($report1, $report2))
				->array($runner->getObservers())->isEqualTo(array($report1, $report2))
				->object($runner->removeReport(new atoum\reports\realtime\cli()))->isIdenticalTo($runner)
				->array($runner->getReports())->isEqualTo(array($report1, $report2))
				->array($runner->getObservers())->isEqualTo(array($report1, $report2))
				->object($runner->removeReport($report1))->isIdenticalTo($runner)
				->array($runner->getReports())->isEqualTo(array($report2))
				->array($runner->getObservers())->isEqualTo(array($report2))
				->object($runner->removeReport($report2))->isIdenticalTo($runner)
				->array($runner->getReports())->isEmpty()
				->array($runner->getObservers())->isEmpty()
			->if($runner->setReport($otherReport = new atoum\reports\realtime\cli()))
			->then
				->array($runner->getReports())->isEqualTo(array($otherReport))
				->array($runner->getObservers())->isEqualTo(array($otherReport))
				->object($runner->removeReport($otherReport))->isIdenticalTo($runner)
				->array($runner->getReports())->isEmpty()
				->array($runner->getObservers())->isEmpty()
			->if($runner->addReport($report1)->addReport($report2))
			->then
				->array($runner->getReports())->isEqualTo(array($report1, $report2))
				->array($runner->getObservers())->isEqualTo(array($report1, $report2))
		;
	}

	public function testRemoveReports()
	{
		$this
			->if($runner = new testedClass())
			->then
				->array($runner->getReports())->isEmpty()
				->array($runner->getObservers())->isEmpty()
				->object($runner->removeReports())->isIdenticalTo($runner)
				->array($runner->getReports())->isEmpty()
				->array($runner->getObservers())->isEmpty()
			->if($report1 = new \mock\mageekguy\atoum\report())
			->and($report2 = new \mock\mageekguy\atoum\report())
			->and($runner->addReport($report1)->addReport($report2))
			->then
				->array($runner->getReports())->isEqualTo(array($report1, $report2))
				->array($runner->getObservers())->isEqualTo(array($report1, $report2))
				->object($runner->removeReports())->isIdenticalTo($runner)
				->array($runner->getReports())->isEmpty()
				->array($runner->getObservers())->isEmpty()
			->if($runner->setReport($otherReport = new atoum\reports\realtime\cli()))
			->then
				->array($runner->getReports())->isEqualTo(array($otherReport))
				->array($runner->getObservers())->isEqualTo(array($otherReport))
				->object($runner->removeReports())->isIdenticalTo($runner)
				->array($runner->getReports())->isEmpty()
				->array($runner->getObservers())->isEmpty()
			->if($runner->addReport($report1)->addReport($report2))
			->then
				->array($runner->getReports())->isEqualTo(array($report1, $report2))
				->array($runner->getObservers())->isEqualTo(array($report1, $report2))
			->given(
				$firstReport = new \mock\mageekguy\atoum\report(),
				$secondReport = new \mock\mageekguy\atoum\report(),
				$overrideReport = new \mock\mageekguy\atoum\report(),
				$runner->removeReports()
			)
			->if(
				$this->calling($firstReport)->isOverridableBy = function($report) use ($overrideReport) { return $report === $overrideReport; },
				$this->calling($secondReport)->isOverridableBy = function($report) use ($overrideReport) { return $report !== $overrideReport; },
				$runner->addReport($firstReport)
			)
			->when($runner->removeReports($secondReport))
			->then
				->array($runner->getReports())->isEmpty
				->array($runner->getObservers())->isEmpty
			->if(
				$runner->addReport($firstReport),
				$runner->addReport($secondReport)
			)
			->when($runner->removeReports($overrideReport))
			->then
				->array($runner->getReports())->isEqualTo(array($firstReport))
				->array($runner->getObservers())->isEqualTo(array($firstReport))
		;
	}

	public function testAddExtension()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->addExtension($extension = new \mock\mageekguy\atoum\extension()))->isIdenticalTo($runner)
				->array(iterator_to_array($runner->getExtensions()))->isEqualTo(array($extension))
				->array($runner->getObservers())->contains($extension)
				->mock($extension)
					->call('setRunner')->withArguments($runner)->once()
			->if($this->resetMock($extension))
			->then
				->object($runner->addExtension($extension))->isIdenticalTo($runner)
				->array(iterator_to_array($runner->getExtensions()))->isEqualTo(array($extension))
				->array($runner->getObservers())->contains($extension)
				->mock($extension)
					->call('setRunner')->never();
		;
	}

	public function testRemoveExtension()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->getExtensions())->isInstanceOf('mageekguy\atoum\extension\aggregator')
				->sizeOf($runner->getExtensions())->isZero
				->array($runner->getObservers())->isEmpty()
			->if($extension = new \mock\mageekguy\atoum\extension())
			->and(
				$this->mockClass('mageekguy\atoum\extension', 'otherMock', 'extension'),
				$otherExtension = new \otherMock\extension()
			)
			->and($runner->addExtension($extension)->addExtension($otherExtension))
			->then
				->array(iterator_to_array($runner->getExtensions()))->isEqualTo(array($extension, $otherExtension))
				->array($runner->getObservers())->isEqualTo(array($extension, $otherExtension))
				->object($runner->removeExtension(new \mock\mageekguy\atoum\extension()))->isIdenticalTo($runner)
				->array(iterator_to_array($runner->getExtensions()))->isEqualTo(array($otherExtension))
				->array($runner->getObservers())->isEqualTo(array($otherExtension))
			->if($runner->addExtension($extension))
			->then
				->array(iterator_to_array($runner->getExtensions()))->isEqualTo(array($otherExtension, $extension))
				->object($runner->removeExtension($extension))->isIdenticalTo($runner)
				->array(iterator_to_array($runner->getExtensions()))->isEqualTo(array($otherExtension))
				->array($runner->getObservers())->isEqualTo(array($otherExtension))
			->if($runner->addExtension($extension))
			->then
				->array(iterator_to_array($runner->getExtensions()))->isEqualTo(array($otherExtension, $extension))
				->object($runner->removeExtension('mock\mageekguy\atoum\extension'))->isIdenticalTo($runner)
				->array(iterator_to_array($runner->getExtensions()))->isEqualTo(array($otherExtension))
				->array($runner->getObservers())->isEqualTo(array($otherExtension))
				->object($runner->removeExtension($otherExtension))->isIdenticalTo($runner)
				->object($runner->getExtensions())->isInstanceOf('mageekguy\atoum\extension\aggregator')
				->sizeOf($runner->getExtensions())->isZero
				->array($runner->getObservers())->isEmpty()
			->if($extension = new \mock\mageekguy\atoum\extension())
			->then
				->exception(function() use ($runner, $extension) {
						$runner->removeExtension($extension);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Extension ' . get_class($extension) . ' is not loaded')
		;
	}

	public function testRemoveExtensions()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->getExtensions())->isInstanceOf('mageekguy\atoum\extension\aggregator')
				->sizeOf($runner->getExtensions())->isZero
				->array($runner->getObservers())->isEmpty()
				->object($runner->removeExtensions())->isIdenticalTo($runner)
				->object($runner->getExtensions())->isInstanceOf('mageekguy\atoum\extension\aggregator')
				->sizeOf($runner->getExtensions())->isZero
				->array($runner->getObservers())->isEmpty()
			->if($extension = new \mock\mageekguy\atoum\extension())
			->and(
				$this->mockClass('mageekguy\atoum\extension', 'otherMock', 'extension'),
				$otherExtension = new \otherMock\extension()
			)
			->and($runner->addExtension($extension)->addExtension($otherExtension))
			->then
				->array(iterator_to_array($runner->getExtensions()))->isEqualTo(array($extension, $otherExtension))
				->array($runner->getObservers())->isEqualTo(array($extension, $otherExtension))
				->object($runner->removeExtensions())->isIdenticalTo($runner)
				->object($runner->getExtensions())->isInstanceOf('mageekguy\atoum\extension\aggregator')
				->sizeOf($runner->getExtensions())->isZero
				->array($runner->getObservers())->isEmpty()
		;
	}

	public function testEnableCodeCoverage()
	{
		$this
			->if($runner = new testedClass())
			->and($runner->disableCodeCoverage())
			->then
				->boolean($runner->codeCoverageIsEnabled())->isFalse()
				->object($runner->enableCodeCoverage())->isIdenticalTo($runner)
				->boolean($runner->codeCoverageIsEnabled())->isTrue()
		;
	}

	public function testDisableCodeCoverage()
	{
		$this
			->if($runner = new testedClass())
			->and($runner->enableCodeCoverage())
			->then
				->boolean($runner->codeCoverageIsEnabled())->isTrue()
				->object($runner->disableCodeCoverage())->isIdenticalTo($runner)
				->boolean($runner->codeCoverageIsEnabled())->isFalse()
		;
	}

	public function testSetTestFactory()
	{
		$this
			->if($runner = new testedClass())
			->then
				->variable($runner->getTestFactory())->isCallable
				->object($runner->setTestFactory())->isIdenticalTo($runner)
				->object($runner->getTestFactory())->isCallable
			->if($factory = function() {})
			->then
				->object($runner->setTestFactory($factory))->isIdenticalTo($runner)
				->object($runner->getTestFactory())->isCallable
			->given($test = new \mock\mageekguy\atoum\test())
			->and($generator = new \mock\mageekguy\atoum\test\mock\generator($test))
			->and($test->setMockGenerator($generator))
			->if($runner->disallowUsageOfUndefinedMethodInMock())
			->and($runner->setTestFactory(function() use ($test) { return $test; }))
			->and($factory = $runner->getTestFactory())
			->then
				->object($factory('mock\mageekguy\atoum\test'))->isIdenticalTo($test)
				->mock($generator)
					->call('disallowUndefinedMethodUsage')->once
			->if($this->resetMock($generator))
			->if($runner->allowUsageOfUndefinedMethodInMock())
			->then
				->object($factory('mock\mageekguy\atoum\test'))->isIdenticalTo($test)
				->mock($generator)
					->call('disallowUndefinedMethodUsage')->never
		;
	}

	public function testSetPathAndVersionInScore()
	{
		$this
			->if($php = new \mock\mageekguy\atoum\php())
			->and($this->calling($php)->getBinaryPath = $phpPath = uniqid())
			->and($this->calling($php)->run = $php)
			->and($this->calling($php)->isRunning = false)
			->and($this->calling($php)->getExitCode = 0)
			->and($this->calling($php)->getStdout = $phpVersion = uniqid())
			->and($adapter = new atoum\test\adapter())
			->and($adapter->defined = true)
			->and($adapter->constant = function($constantName) use (& $atoumVersion, & $atoumDirectory) {
					switch ($constantName)
					{
						case atoum\runner::atoumVersionConstant:
							return $atoumVersion = uniqid();

						case atoum\runner::atoumDirectoryConstant:
							return $atoumDirectory = uniqid();
					}
				}
			)
			->and($runner = new testedClass())
			->and($runner->setPhp($php))
			->and($runner->setAdapter($adapter))
			->and($runner->setScore($score = new \mock\mageekguy\atoum\runner\score()))
			->then
				->object($runner->setPathAndVersionInScore())->isIdenticalTo($runner)
				->mock($score)
					->call('setAtoumVersion')->withArguments($atoumVersion)->once()
					->call('setAtoumPath')->withArguments($atoumDirectory)->once()
					->call('setPhpPath')->withArguments($phpPath)->once()
					->call('setPhpVersion')->withArguments($phpVersion)->once()
			->if($adapter->defined = false)
			->and($runner->setScore($score = new \mock\mageekguy\atoum\runner\score()))
			->then
				->object($runner->setPathAndVersionInScore())->isIdenticalTo($runner)
				->mock($score)
					->call('setAtoumVersion')->withArguments(null)->once()
					->call('setAtoumPath')->withArguments(null)->once()
					->call('setPhpPath')->withArguments($phpPath)->once()
					->call('setPhpVersion')->withArguments($phpVersion)->once()
			->if($this->calling($php)->getExitCode = rand(1, PHP_INT_MAX))
			->and($runner->setScore($score = new \mock\mageekguy\atoum\runner\score()))
			->then
				->exception(function() use ($runner) {
						$runner->setPathAndVersionInScore();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to get PHP version from \'' . $php . '\'')
		;
	}

	public function testGetCoverage()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->getCoverage())->isIdenticalTo($runner->getScore()->getCoverage())
		;
	}

	public function testAddTest()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->addTest($testPath1 = uniqid()))->isIdenticalTo($runner)
				->array($runner->getTestPaths())->isEqualTo(array($testPath1))
				->object($runner->addTest($testPath2 = uniqid()))->isIdenticalTo($runner)
				->array($runner->getTestPaths())->isEqualTo(array($testPath1, $testPath2))
				->object($runner->addTest($testPath1))->isIdenticalTo($runner)
				->array($runner->getTestPaths())->isEqualTo(array($testPath1, $testPath2))
				->object($runner->addTest($testPath3 = new \splFileInfo(__FILE__)))->isIdenticalTo($runner)
				->array($runner->getTestPaths())->isEqualTo(array($testPath1, $testPath2, (string) $testPath3))
			->if($runner->canNotAddTest())
			->then
				->object($runner->addTest($testPath4 = uniqid()))->isIdenticalTo($runner)
				->array($runner->getTestPaths())->isEqualTo(array($testPath1, $testPath2, (string) $testPath3))
			->if($runner->canAddTest())
			->then
				->object($runner->addTest($testPath4 = uniqid()))->isIdenticalTo($runner)
				->array($runner->getTestPaths())->isEqualTo(array($testPath1, $testPath2, (string) $testPath3, $testPath4))
		;
	}

	public function testCanAddTest()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->canAddTest())->isIdenticalTo($runner)
			->if($runner->canNotAddTest())
			->then
				->object($runner->canAddTest())->isIdenticalTo($runner)
			->if($runner->addTest(uniqid()))
			->then
				->array($runner->getTestPaths())->isNotEmpty()
		;
	}

	public function testCanNotAddTest()
	{
		$this
			->if($runner = new testedClass())
			->then
				->object($runner->canNotAddTest())->isIdenticalTo($runner)
			->if($runner->addTest(uniqid()))
			->then
				->array($runner->getTestPaths())->isEmpty()
			->if($runner->canAddTest())
			->then
				->object($runner->canNotAddTest())->isIdenticalTo($runner)
			->if($runner->addTest(uniqid()))
			->then
				->array($runner->getTestPaths())->isEmpty()
		;
	}

	public function testAcceptTestFileExtensions()
	{
		$this
			->if($runner = new testedClass())
			->and($runner->setTestDirectoryIterator($directoryIterator = new \mock\mageekguy\atoum\iterators\recursives\directory\factory()))
			->then
				->object($runner->acceptTestFileExtensions($testFileExtensions = array(uniqid(), uniqid())))->isIdenticalTo($runner)
				->mock($directoryIterator)->call('acceptExtensions')->withArguments($testFileExtensions)->once()
		;
	}
}
