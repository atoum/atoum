<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mageekguy\atoum\mock
;

require_once __DIR__ . '/../runner.php';

class runner extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->hasInterface('mageekguy\atoum\observable')
				->hasInterface('mageekguy\atoum\adapter\aggregator')
			->string(atoum\runner::atoumVersionConstant)->isEqualTo('mageekguy\atoum\version')
			->string(atoum\runner::atoumDirectoryConstant)->isEqualTo('mageekguy\atoum\directory')
			->string(atoum\runner::runStart)->isEqualTo('runnerStart')
			->string(atoum\runner::runStop)->isEqualTo('runnerStop')
		;
	}

	public function test__construct()
	{
		$this
			->if($runner = new atoum\runner())
			->then
				->object($runner->getScore())->isInstanceOf('mageekguy\atoum\score')
				->object($runner->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($runner->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($runner->getIncluder())->isInstanceOf('mageekguy\atoum\includer')
				->object($runner->getTestDirectoryIterator())->isInstanceOf('mageekguy\atoum\iterators\recursives\directory')
				->object($runner->getFactory())->isInstanceOf('mageekguy\atoum\factory')
				->object($runner->getFactory()->build('mageekguy\atoum\adapter'))->isIdenticalTo($runner->getAdapter())
				->object($runner->getFactory()->build('mageekguy\atoum\locale'))->isIdenticalTo($runner->getLocale())
				->object($runner->getFactory()->build('mageekguy\atoum\includer'))->isIdenticalTo($runner->getIncluder())
				->variable($runner->getRunningDuration())->isNull()
				->boolean($runner->codeCoverageIsEnabled())->isTrue()
				->variable($runner->getDefaultReportTitle())->isNull()
				->array($runner->getObservers())->isEmpty()
			->if($runner = new atoum\runner($factory = new \mock\mageekguy\atoum\factory()))
			->then
				->object($runner->getScore())->isInstanceOf('mageekguy\atoum\score')
				->object($runner->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($runner->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($runner->getIncluder())->isInstanceOf('mageekguy\atoum\includer')
				->object($runner->getTestDirectoryIterator())->isInstanceOf('mageekguy\atoum\iterators\recursives\directory')
				->object($runner->getFactory())->isIdenticalTo($factory)
				->object($runner->getFactory()->build('mageekguy\atoum\adapter'))->isIdenticalTo($runner->getAdapter())
				->object($runner->getFactory()->build('mageekguy\atoum\locale'))->isIdenticalTo($runner->getLocale())
				->object($runner->getFactory()->build('mageekguy\atoum\includer'))->isIdenticalTo($runner->getIncluder())
				->variable($runner->getRunningDuration())->isNull()
				->boolean($runner->codeCoverageIsEnabled())->isTrue()
				->variable($runner->getDefaultReportTitle())->isNull()
				->array($runner->getObservers())->isEmpty()
			->if($factory['mageekguy\atoum\score'] = $score = new atoum\score())
			->if($factory['mageekguy\atoum\adapter'] = $adapter = new atoum\adapter())
			->if($factory['mageekguy\atoum\locale'] = $locale = new atoum\locale())
			->if($factory['mageekguy\atoum\includer'] = $includer = new atoum\includer())
			->if($factory['mageekguy\atoum\iterators\recursives\directory'] = $testDirectoryIterator = new atoum\iterators\recursives\directory())
			->and($runner = new atoum\runner($factory))
			->then
				->object($runner->getScore())->isIdenticalTo($score)
				->object($runner->getAdapter())->isIdenticalTo($adapter)
				->object($runner->getLocale())->isIdenticalTo($locale)
				->object($runner->getIncluder())->isIdenticalTo($includer)
				->object($runner->getTestDirectoryIterator())->isIdenticalTo($testDirectoryIterator)
				->object($runner->getFactory())->isIdenticalTo($factory)
				->object($runner->getFactory()->build('mageekguy\atoum\adapter'))->isIdenticalTo($runner->getAdapter())
				->object($runner->getFactory()->build('mageekguy\atoum\locale'))->isIdenticalTo($runner->getLocale())
				->object($runner->getFactory()->build('mageekguy\atoum\includer'))->isIdenticalTo($runner->getIncluder())
				->variable($runner->getRunningDuration())->isNull()
				->boolean($runner->codeCoverageIsEnabled())->isTrue()
				->variable($runner->getDefaultReportTitle())->isNull()
				->array($runner->getObservers())->isEmpty()
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($runner = new atoum\runner())
			->then
				->object($runner->setAdapter($adapter = new atoum\test\adapter()))->isIdenticalTo($runner)
				->object($runner->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetScore()
	{
		$this
			->if($runner = new atoum\runner())
			->then
				->object($runner->setScore($score = new atoum\score()))->isIdenticalTo($runner)
				->object($runner->getScore())->isIdenticalTo($score);
		;
	}

	public function testSetDefaultReportTtitle()
	{
		$this
			->if($runner = new atoum\runner())
			->then
				->object($runner->setDefaultReportTitle($title = uniqid()))->isIdenticalTo($runner)
				->string($runner->getDefaultReportTitle())->isEqualTo($title)
		;
	}

	public function testGetPhpPath()
	{
		$this
			->if($runner = new atoum\runner())
			->and($runner->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->getenv = function($variable) use (& $pearPhpPath) { return ($variable != 'PHP_PEAR_PHP_BIN' ? false : $pearPhpPath = uniqid()); })
			->then
				->string($runner->getPhpPath())->isEqualTo($pearPhpPath)
			->if($runner = new atoum\runner())
			->and($runner->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->getenv = function($variable) use (& $phpBinPath) {
					switch ($variable)
					{
						case 'PHPBIN':
							return ($phpBinPath = uniqid());

						default:
							return false;
					}
				}
			)
			->then
				->string($runner->getPhpPath())->isEqualTo($phpBinPath)
		;
	}

	public function testSetPhpPath()
	{
		$this
			->if($runner = new atoum\runner())
			->then
				->object($runner->setPhpPath($phpPath = uniqid()))->isIdenticalTo($runner)
				->string($runner->getPhpPath())->isIdenticalTo($phpPath)
				->object($runner->setPhpPath($phpPath = rand(1, PHP_INT_MAX)))->isIdenticalTo($runner)
				->string($runner->getPhpPath())->isIdenticalTo((string) $phpPath)
		;
	}

	public function testAddObserver()
	{
		$this
			->if($runner = new atoum\runner())
			->then
				->array($runner->getObservers())->isEmpty()
				->object($runner->addObserver($observer = new \mock\mageekguy\atoum\observers\runner()))->isIdenticalTo($runner)
				->array($runner->getObservers())->isEqualTo(array($observer))
		;
	}

	public function testRemoveObserver()
	{
		$this
			->if($runner = new atoum\runner())
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
			->if($runner = new atoum\runner())
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
			->and($adapter->get_declared_classes = array())
			->and($adapter->proc_open = function() {})
			->and($adapter->stream_get_contents = '')
			->and($adapter->getenv = function($variable) { return ($variable != 'PHP_PEAR_PHP_BIN' ? false : 'PHP_PEAR_PHP_BIN'); })
			->and($adapter->realpath = function($path) { return $path; })
			->and($adapter->fclose = function() {})
			->and($adapter->proc_get_status = array('exitcode' => 0, 'running' => false))
			->and($adapter->proc_close = function() {})
			->and($adapter->proc_terminate = function() {})
			->and($adapter->microtime = function() { static $call = 0; return (++$call * 100); })
			->and($adapter->get_declared_classes = function() { return array(); })
			->and($runner = new atoum\runner())
			->and($runner->setAdapter($adapter))
			->then
				->variable($runner->getRunningDuration())->isNull()
			->if($runner->run())
			->then
				->integer($runner->getRunningDuration())->isEqualTo(100)
		;
	}

	public function testGetTestNumber()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->get_declared_classes = array())
			->and($adapter->proc_open = function() {})
			->and($adapter->stream_get_contents = '')
			->and($adapter->getenv = function($variable) { return ($variable != 'PHP_PEAR_PHP_BIN' ? false : 'PHP_PEAR_PHP_BIN'); })
			->and($adapter->realpath = function($path) { return $path; })
			->and($adapter->fclose = function() {})
			->and($adapter->proc_get_status = array('exitcode' => 0, 'running' => false))
			->and($adapter->proc_close = function() {})
			->and($adapter->proc_terminate = function() {})
			->and($runner = new atoum\runner())
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
			->and($adapter->proc_open = function() {})
			->and($adapter->stream_get_contents = '')
			->and($adapter->getenv = function($variable) { return ($variable != 'PHP_PEAR_PHP_BIN' ? false : 'PHP_PEAR_PHP_BIN'); })
			->and($adapter->realpath = function($path) { return $path; })
			->and($adapter->fclose = function() {})
			->and($adapter->proc_get_status = array('exitcode' => 0, 'running' => false))
			->and($adapter->proc_terminate = function() {})
			->and($adapter->proc_close = function() {})
			->and($adapter->get_declared_classes = array())
			->and($runner = new atoum\runner())
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
			->if($runner = new atoum\runner())
			->and($includer = new \mock\mageekguy\atoum\includer())
			->and($includer->getMockController()->includePath = function() {})
			->and($runner->setIncluder($includer))
			->then
				->object($runner->setBootstrapFile($path = uniqid()))->isIdenticalTo($runner)
				->string($runner->getBootstrapFile())->isEqualTo($path)
				->mock($includer)->call('includePath')->withArguments($path)->once()
		;
	}

	public function testHasReports()
	{
		$this
			->if($runner = new atoum\runner())
			->then
				->boolean($runner->hasReports())->isFalse()
			->if($runner->addReport(new atoum\reports\realtime\cli()))
			->then
				->boolean($runner->hasReports())->isTrue()
		;
	}

	public function testAddReport()
	{
		$this
			->if($runner = new atoum\runner())
			->then
				->object($runner->addReport($report = new atoum\reports\realtime\cli()))->isIdenticalTo($runner)
				->array($runner->getReports())->isEqualTo(array($report))
				->array($runner->getObservers())->contains($report)
		;
	}

	public function testRemoveReport()
	{
		$this
			->if($runner = new atoum\runner())
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
		;
	}

	public function testRemoveReports()
	{
		$this
			->if($runner = new atoum\runner())
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
		;
	}

	public function testEnableCodeCoverage()
	{
		$this
			->if($runner = new atoum\runner())
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
			->if($runner = new atoum\runner())
			->and($runner->enableCodeCoverage())
			->then
				->boolean($runner->codeCoverageIsEnabled())->isTrue()
				->object($runner->disableCodeCoverage())->isIdenticalTo($runner)
				->boolean($runner->codeCoverageIsEnabled())->isFalse()
		;
	}

	public function testSetPathAndVersionInScore()
	{
		$this
			->if($score = new \mock\mageekguy\atoum\score())
			->and($scoreController = $score->getMockController())
			->and($adapter = new atoum\test\adapter())
			->and($adapter->defined = false)
			->and($adapter->proc_open = false)
			->and($adapter->getenv = function($variable) use (& $phpPath) { return ($variable != 'PHP_PEAR_PHP_BIN' ? false : $phpPath = uniqid()); })
			->and($adapter->realpath = function($path) { return $path; })
			->and($runner = new atoum\runner())
			->and($runner->setScore($score))
			->and($runner->setAdapter($adapter))
			->then
				->exception(function() use ($runner) {
						$runner->setPathAndVersionInScore();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to open \'' . $phpPath . '\'')
				->adapter($adapter)
					->call('realpath')->withArguments($phpPath)->once()
					->call('defined')->withArguments(atoum\runner::atoumVersionConstant)->once()
					->call('defined')->withArguments(atoum\runner::atoumDirectoryConstant)->once()
				->mock($score)
					->call('setAtoumVersion')->withArguments(null)->once()
					->call('setAtoumPath')->withArguments(null)->once()
					->call('setPhpPath')->never()
					->call('setPhpVersion')->never()
			->if($adapter->realpath = false)
			->and($adapter->resetCalls())
			->and($score->reset())
			->and($scoreController->resetCalls())
			->then
				->exception(function() use ($runner) {
						$runner->setPathAndVersionInScore();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to find \'' . $phpPath . '\'')
				->adapter($adapter)
					->call('realpath')->withArguments($phpPath)->once()
					->call('defined')->withArguments(atoum\runner::atoumVersionConstant)->once()
					->call('defined')->withArguments(atoum\runner::atoumDirectoryConstant)->once()
				->mock($score)
					->call('setAtoumVersion')->withArguments(null)->once()
					->call('setAtoumPath')->withArguments(null)->once()
					->call('setPhpPath')->never()
					->call('setPhpVersion')->never()
			->if($adapter->resetCalls())
			->and($adapter->realpath = function($path) { return $path; })
			->and($adapter->proc_open = function($cmd, $descriptors, & $pipes) use (& $php, & $stdOut, & $stdErr) {
					$pipes = array(
						1 => $stdOut = uniqid(),
						2 => $stErr = uniqid(),
					);
					return $php = uniqid();
				}
			)
			->and($adapter->stream_get_contents = $phpVersion = uniqid())
			->and($adapter->fclose = function() {})
			->and($adapter->proc_close = function() {})
			->and($adapter->proc_terminate = function() {})
			->and($adapter->proc_get_status = array('running' => false, 'exitcode' => 126))
			->and($score->reset())
			->and( $scoreController->resetCalls())
			->then
				->exception(function() use ($runner) {
						$runner->setPathAndVersionInScore();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to get PHP version from \'' . $phpPath . '\'')
				->adapter($adapter)
					->call('realpath')->withArguments($phpPath)->once()
					->call('defined')->withArguments(atoum\runner::atoumVersionConstant)->once()
					->call('defined')->withArguments(atoum\runner::atoumDirectoryConstant)->once()
					->call('proc_close')->withArguments($php)->once()
				->mock($score)
					->call('setAtoumVersion')->withArguments(null)->once()
					->call('setAtoumPath')->withArguments(null)->once()
					->call('setPhpPath')->never()
					->call('setPhpVersion')->never()
			->if($adapter->resetCalls())
			->and($adapter->proc_get_status = array('running' => false, 'exitcode' => 127))
			->and($score->reset())
			->and($scoreController->reset())
			->then
				->exception(function() use ($runner) {
						$runner->setPathAndVersionInScore();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to get PHP version from \'' . $phpPath . '\'')
				->adapter($adapter)
					->call('realpath')->withArguments($phpPath)->once()
					->call('defined')->withArguments(atoum\runner::atoumVersionConstant)->once()
					->call('defined')->withArguments(atoum\runner::atoumDirectoryConstant)->once()
					->call('proc_close')->withArguments($php)->once()
				->mock($score)
					->call('setAtoumVersion')->withArguments(null)->once()
					->call('setAtoumPath')->withArguments(null)->once()
					->call('setPhpPath')->never()
					->call('setPhpVersion')->never()
			->if($adapter->resetCalls())
			->and($adapter->proc_get_status = array('exitcode' => 0, 'running' => false))
			->and($score->reset())
			->and($scoreController->resetCalls())
			->then
				->object($runner->setPathAndVersionInScore())->isIdenticalTo($runner)
				->adapter($adapter)
					->call('realpath')->withArguments($phpPath)->once()
					->call('defined')->withArguments(atoum\runner::atoumVersionConstant)->once()
					->call('defined')->withArguments(atoum\runner::atoumDirectoryConstant)->once()
					->call('stream_get_contents')->withArguments($stdOut)->once()
					->call('fclose')->withArguments($stdOut)->once()
					->call('proc_close')->withArguments($php)->once()
				->mock($score)
					->call('setAtoumVersion')->withArguments(null)->once()
					->call('setAtoumPath')->withArguments(null)->once()
					->call('setPhpPath')->withArguments($phpPath)->once()
					->call('setPhpVersion')->withArguments($phpVersion)->once()
			->if($adapter->defined = true)
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
			->and($adapter->resetCalls())
			->and($score->reset())
			->and($scoreController->resetCalls())
			->then
				->object($runner->setPathAndVersionInScore())->isIdenticalTo($runner)
				->adapter($adapter)
					->call('realpath')->withArguments($phpPath)->once()
					->call('defined')->withArguments(atoum\runner::atoumVersionConstant)->once()
					->call('constant')->withArguments(atoum\runner::atoumVersionConstant)->once()
					->call('defined')->withArguments(atoum\runner::atoumDirectoryConstant)->once()
					->call('constant')->withArguments(atoum\runner::atoumDirectoryConstant)->once()
					->call('stream_get_contents')->withArguments($stdOut)->once()
					->call('fclose')->withArguments($stdOut)->once()
					->call('proc_close')->withArguments($php)->once()
				->mock($score)
					->call('setAtoumVersion')->withArguments($atoumVersion)->once()
					->call('setAtoumPath')->withArguments($atoumDirectory)->once()
					->call('setPhpPath')->withArguments($phpPath)->once()
					->call('setPhpVersion')->withArguments($phpVersion)->once()
		;
	}

	public function testGetCoverage()
	{
		$this
			->if($runner = new atoum\runner())
			->then
				->object($runner->getCoverage())->isIdenticalTo($runner->getScore()->getCoverage())
		;
	}
}
