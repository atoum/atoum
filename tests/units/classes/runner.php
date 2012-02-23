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
		$runner = new atoum\runner();

		$this->assert
			->object($runner->getScore())->isInstanceOf('mageekguy\atoum\score')
			->object($runner->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			->object($runner->getLocale())->isInstanceOf('mageekguy\atoum\locale')
			->object($runner->getIncluder())->isInstanceOf('mageekguy\atoum\includer')
			->variable($runner->getRunningDuration())->isNull()
			->boolean($runner->codeCoverageIsEnabled())->isTrue()
			->variable($runner->getDefaultReportTitle())->isNull()
			->array($runner->getObservers())->isEmpty()
		;

		$runner = new atoum\runner($score = new atoum\score(), $adapter = new atoum\test\adapter(), $locale = new atoum\locale(), $includer = new atoum\includer());

		$this->assert
			->object($runner->getScore())->isIdenticalTo($score)
			->object($runner->getAdapter())->isIdenticalTo($adapter)
			->object($runner->getLocale())->isIdenticalTo($locale)
			->object($runner->getIncluder())->isIdenticalTo($includer)
			->variable($runner->getRunningDuration())->isNull()
			->boolean($runner->codeCoverageIsEnabled())->isTrue()
			->variable($runner->getDefaultReportTitle())->isNull()
			->array($runner->getObservers())->isEmpty()
		;
	}

	public function testSetAdapter()
	{
		$runner = new atoum\runner();

		$this->assert
			->object($runner->setAdapter($adapter = new atoum\test\adapter()))->isIdenticalTo($runner)
			->object($runner->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetScore()
	{
		$runner = new atoum\runner();

		$this->assert
			->object($runner->setScore($score = new atoum\score()))->isIdenticalTo($runner)
			->object($runner->getScore())->isIdenticalTo($score);
		;
	}

	public function testSetDefaultReportTtitle()
	{
		$runner = new atoum\runner();

		$this->assert
			->object($runner->setDefaultReportTitle($title = uniqid()))->isIdenticalTo($runner)
			->string($runner->getDefaultReportTitle())->isEqualTo($title)
		;
	}

	public function testGetPhpPath()
	{
		$runner = new atoum\runner();
		$runner->setAdapter($adapter = new atoum\test\adapter());

		$adapter->getenv = function($variable) use (& $pearPhpPath) { return ($variable != 'PHP_PEAR_PHP_BIN' ? false : $pearPhpPath = uniqid()); };

		$this->assert
			->string($runner->getPhpPath())->isEqualTo($pearPhpPath)
		;

		$runner = new atoum\runner();
		$runner->setAdapter($adapter = new atoum\test\adapter());

		$adapter->getenv = function($variable) use (& $phpBinPath) {
			switch ($variable)
			{
				case 'PHPBIN':
					return ($phpBinPath = uniqid());

				default:
					return false;
			}
		};

		$this->assert
			->string($runner->getPhpPath())->isEqualTo($phpBinPath)
		;
	}

	public function testSetPhpPath()
	{
		$runner = new atoum\runner();

		$this->assert
			->object($runner->setPhpPath($phpPath = uniqid()))->isIdenticalTo($runner)
			->string($runner->getPhpPath())->isIdenticalTo($phpPath)
		;

		$this->assert
			->object($runner->setPhpPath($phpPath = rand(1, PHP_INT_MAX)))->isIdenticalTo($runner)
			->string($runner->getPhpPath())->isIdenticalTo((string) $phpPath)
		;
	}

	public function testAddObserver()
	{
		$runner = new atoum\runner();

		$this->mockGenerator
			->generate('mageekguy\atoum\observers\runner')
		;

		$this->assert
			->array($runner->getObservers())->isEmpty()
			->object($runner->addObserver($observer = new \mock\mageekguy\atoum\observers\runner()))->isIdenticalTo($runner)
			->array($runner->getObservers())->isEqualTo(array($observer))
		;
	}

	public function testRemoveObserver()
	{
		$runner = new atoum\runner();

		$this->mockGenerator
			->generate('mageekguy\atoum\observers\runner')
		;

		$this->assert
			->array($runner->getObservers())->isEmpty()
			->object($runner->removeObserver(new \mock\mageekguy\atoum\observers\runner()))->isIdenticalTo($runner)
			->array($runner->getObservers())->isEmpty()
		;

		$runner->addObserver($observer1 = new \mock\mageekguy\atoum\observers\runner());
		$runner->addObserver($observer2 = new \mock\mageekguy\atoum\observers\runner());

		$this->assert
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
		$runner = new atoum\runner();

		$this->assert
			->object($runner->callObservers(atoum\runner::runStart))->isIdenticalTo($runner)
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\observers\runner')
		;

		$runner->addObserver($observer = new \mock\mageekguy\atoum\observers\runner());

		$this->assert
			->object($runner->callObservers(atoum\runner::runStart))->isIdenticalTo($runner)
			->mock($observer)->call(atoum\runner::runStart, array($runner))
		;
	}

	public function testGetRunningDuration()
	{
		$adapter = new atoum\test\adapter();
		$adapter->get_declared_classes = array();
		$adapter->proc_open = function() {};
		$adapter->stream_get_contents = '';
		$adapter->getenv = function($variable) { return ($variable != 'PHP_PEAR_PHP_BIN' ? false : 'PHP_PEAR_PHP_BIN'); };
		$adapter->realpath = function($path) { return $path; };
		$adapter->fclose = function() {};
		$adapter->proc_get_status = array('exitcode' => 0, 'running' => false);
		$adapter->proc_close = function() {};
		$adapter->proc_terminate = function() {};
		$adapter->microtime = function() { static $call = 0; return (++$call * 100); };
		$adapter->get_declared_classes = function() { return array(); };

		$runner = new atoum\runner(null, $adapter);

		$this->assert
			->variable($runner->getRunningDuration())->isNull()
		;

		$runner->run();

		$this->assert
			->integer($runner->getRunningDuration())->isEqualTo(100)
		;

		$adapter->defined = true;
	}

	public function testGetTestNumber()
	{
		$adapter = new atoum\test\adapter();
		$adapter->get_declared_classes = array();
		$adapter->proc_open = function() {};
		$adapter->stream_get_contents = '';
		$adapter->getenv = function($variable) { return ($variable != 'PHP_PEAR_PHP_BIN' ? false : 'PHP_PEAR_PHP_BIN'); };
		$adapter->realpath = function($path) { return $path; };
		$adapter->fclose = function() {};
		$adapter->proc_get_status = array('exitcode' => 0, 'running' => false);
		$adapter->proc_close = function() {};
		$adapter->proc_terminate = function() {};

		$runner = new atoum\runner(null, $adapter);

		$this->assert
			->integer($runner->getTestNumber())->isZero();
		;

		$runner->run();

		$this->assert
			->integer($runner->getTestNumber())->isZero();
		;
	}

	public function testGetTestMethodNumber()
	{
		$adapter = new atoum\test\adapter();
		$adapter->get_declared_classes = array();
		$adapter->proc_open = function() {};
		$adapter->stream_get_contents = '';
		$adapter->getenv = function($variable) { return ($variable != 'PHP_PEAR_PHP_BIN' ? false : 'PHP_PEAR_PHP_BIN'); };
		$adapter->realpath = function($path) { return $path; };
		$adapter->fclose = function() {};
		$adapter->proc_get_status = array('exitcode' => 0, 'running' => false);
		$adapter->proc_terminate = function() {};
		$adapter->proc_close = function() {};
		$adapter->get_declared_classes = array();

		$runner = new atoum\runner(null, $adapter);

		$this->assert
			->integer($runner->getTestMethodNumber())->isZero();
		;

		$runner->run();

		$this->assert
			->integer($runner->getTestMethodNumber())->isZero();
		;
	}

	public function testGetBootstrapFile()
	{
		$this
			->mock('mageekguy\atoum\includer')
			->assert
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
		$runner = new atoum\runner();

		$this->assert
			->boolean($runner->hasReports())->isFalse()
		;

		$runner->addReport(new atoum\reports\realtime\cli());

		$this->assert
			->boolean($runner->hasReports())->isTrue()
		;
	}

	public function testAddReport()
	{
		$runner = new atoum\runner();

		$this->assert
			->object($runner->addReport($report = new atoum\reports\realtime\cli()))->isIdenticalTo($runner)
			->array($runner->getReports())->isEqualTo(array($report))
			->array($runner->getObservers())->contains($report)
		;
	}

	public function testRemoveReport()
	{
		$runner = new atoum\runner();

		$this->assert
			->array($runner->getReports())->isEmpty()
			->array($runner->getObservers())->isEmpty()
			->object($runner->removeReport(new atoum\reports\realtime\cli()))->isIdenticalTo($runner)
			->array($runner->getReports())->isEmpty()
			->array($runner->getObservers())->isEmpty()
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\report')
		;

		$report1 = new \mock\mageekguy\atoum\report();
		$report2 = new \mock\mageekguy\atoum\report();

		$runner
			->addReport($report1)
			->addReport($report2)
		;

		$this->assert
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
		$runner = new atoum\runner();

		$this->assert
			->array($runner->getReports())->isEmpty()
			->array($runner->getObservers())->isEmpty()
			->object($runner->removeReports())->isIdenticalTo($runner)
			->array($runner->getReports())->isEmpty()
			->array($runner->getObservers())->isEmpty()
		;

		$this
			->mock('mageekguy\atoum\report')
		;

		$report1 = new \mock\mageekguy\atoum\report();
		$report2 = new \mock\mageekguy\atoum\report();

		$runner
			->addReport($report1)
			->addReport($report2)
		;

		$this->assert
			->array($runner->getReports())->isEqualTo(array($report1, $report2))
			->array($runner->getObservers())->isEqualTo(array($report1, $report2))
			->object($runner->removeReports())->isIdenticalTo($runner)
			->array($runner->getReports())->isEmpty()
			->array($runner->getObservers())->isEmpty()
		;
	}

	public function testEnableCodeCoverage()
	{
		$runner = new atoum\runner();

		$runner->disableCodeCoverage();

		$this->assert
			->boolean($runner->codeCoverageIsEnabled())->isFalse()
			->object($runner->enableCodeCoverage())->isIdenticalTo($runner)
			->boolean($runner->codeCoverageIsEnabled())->isTrue()
		;
	}

	public function testDisableCodeCoverage()
	{
		$runner = new atoum\runner();

		$runner->enableCodeCoverage();

		$this->assert
			->boolean($runner->codeCoverageIsEnabled())->isTrue()
			->object($runner->disableCodeCoverage())->isIdenticalTo($runner)
			->boolean($runner->codeCoverageIsEnabled())->isFalse()
		;
	}

	public function testSetPathAndVersionInScore()
	{
		$this->mockGenerator
			->generate('mageekguy\atoum\score')
		;

		$score = new \mock\mageekguy\atoum\score();
		$scoreController = $score->getMockController();

		$adapter = new atoum\test\adapter();
		$adapter->defined = false;

		$runner = new atoum\runner($score, $adapter);

		$adapter->proc_open = false;
		$adapter->getenv = function($variable) use (& $phpPath) { return ($variable != 'PHP_PEAR_PHP_BIN' ? false : $phpPath = uniqid()); };
		$adapter->realpath = function($path) { return $path; };

		$this->assert
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
		;

		$adapter->realpath = false;

		$score->reset();
		$scoreController->resetCalls();

		$this->assert
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
		;

		$adapter->resetCalls();

		$adapter->realpath = function($path) { return $path; };
		$adapter->proc_open = function($cmd, $descriptors, & $pipes) use (& $php, & $stdOut, & $stdErr) {
			$pipes = array(
				1 => $stdOut = uniqid(),
				2 => $stErr = uniqid(),
			);
			return $php = uniqid();
		};
		$adapter->stream_get_contents = $phpVersion = uniqid();
		$adapter->fclose = function() {};
		$adapter->proc_close = function() {};
		$adapter->proc_terminate = function() {};
		$adapter->proc_get_status = array(
			'running' => false,
			'exitcode' => 126
		);

		$score->reset();
		$scoreController->resetCalls();

		$this->assert
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
		;

		$adapter->resetCalls();

		$adapter->proc_get_status = array(
			'running' => false,
			'exitcode' => 127
		);

		$score->reset();
		$scoreController->reset();

		$this->assert
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
		;

		$adapter->resetCalls();
		$adapter->proc_get_status = array(
			'exitcode' => 0,
			'running' => false
		);

		$score->reset();
		$scoreController->resetCalls();

		$this->assert
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
		;

		$adapter->defined = true;
		$adapter->constant = function($constantName) use (& $atoumVersion, & $atoumDirectory) {
			switch ($constantName)
			{
				case atoum\runner::atoumVersionConstant:
					return $atoumVersion = uniqid();

				case atoum\runner::atoumDirectoryConstant:
					return $atoumDirectory = uniqid();
			}
		};

		$adapter->resetCalls();

		$score->reset();
		$scoreController->resetCalls();

		$this->assert
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
}

?>
