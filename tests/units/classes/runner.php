<?php

namespace mageekguy\atoum\tests\units;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;

require_once(__DIR__ . '/../runner.php');

class runner extends atoum\test
{
	public function test__construct()
	{
		$runner = new atoum\runner();

		$this->assert
			->object($runner->getScore())->isInstanceOf('\mageekguy\atoum\score')
			->object($runner->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
			->object($runner->getSuperglobals())->isInstanceOf('\mageekguy\atoum\superglobals')
			->variable($runner->getRunningDuration())->isNull()
			->boolean($runner->codeCoverageIsEnabled())->isTrue()
		;

		$runner = new atoum\runner($score = new atoum\score(), $adapter = new atoum\test\adapter());

		$this->assert
			->object($runner->getScore())->isIdenticalTo($score)
			->object($runner->getAdapter())->isIdenticalTo($adapter)
			->object($runner->getSuperglobals())->isInstanceOf('\mageekguy\atoum\superglobals')
			->variable($runner->getRunningDuration())->isNull()
			->boolean($runner->codeCoverageIsEnabled())->isTrue()
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

	public function testSetSuperglobals()
	{
		$runner = new atoum\runner();

		$this->assert
			->object($runner->setSuperglobals($superglobals = new atoum\superglobals()))->isIdenticalTo($runner)
			->object($runner->getSuperglobals())->isIdenticalTo($superglobals);
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

	public function testGetPhp()
	{
		$superglobals = new atoum\superglobals();

		$runner = new atoum\runner();
		$runner->setSuperglobals($superglobals);

		$superglobals->_SERVER['_'] = $php = uniqid();

		$this->assert
			->string($runner->getPhp())->isEqualTo($php)
		;

		unset($superglobals->_SERVER['_']);

		$runner = new atoum\runner();
		$runner->setSuperglobals($superglobals);

		$this->assert
			->exception(function() use ($runner) {
					$runner->getPhp();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
		;

		$runner->setPhp($php = uniqid());

		$this->assert
			->string($runner->getPhp())->isEqualTo($php)
		;
	}

	public function testSetPhp()
	{
		$runner = new atoum\runner();

		$this->assert
			->object($runner->setPhp($php = uniqid()))->isIdenticalTo($runner)
			->string($runner->getPhp())->isIdenticalTo($php)
		;

		$this->assert
			->object($runner->setPhp($php = rand(1, PHP_INT_MAX)))->isIdenticalTo($runner)
			->string($runner->getPhp())->isIdenticalTo((string) $php)
		;
	}

	public function testAddObserver()
	{
		$runner = new atoum\runner();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\observers\runner');

		$this->assert
			->array($runner->getObservers())->isEmpty()
			->object($runner->addObserver($observer = new mock\mageekguy\atoum\observers\runner()))->isIdenticalTo($runner)
			->array($runner->getObservers())->isEqualTo(array($observer))
		;
	}

	public function testRemoveObserver()
	{
		$runner = new atoum\runner();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\observers\runner');

		$this->assert
			->array($runner->getObservers())->isEmpty()
			->object($runner->removeObserver(new mock\mageekguy\atoum\observers\runner()))->isIdenticalTo($runner)
			->array($runner->getObservers())->isEmpty()
		;

		$runner->addObserver($observer1 = new mock\mageekguy\atoum\observers\runner());
		$runner->addObserver($observer2 = new mock\mageekguy\atoum\observers\runner());

		$this->assert
			->array($runner->getObservers())->isEqualTo(array($observer1, $observer2))
			->object($runner->removeObserver(new mock\mageekguy\atoum\observers\runner()))->isIdenticalTo($runner)
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

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\observers\runner');

		$runner->addObserver($observer = new mock\mageekguy\atoum\observers\runner());

		$this->assert
			->object($runner->callObservers(atoum\runner::runStart))->isIdenticalTo($runner)
			->mock($observer)->call(atoum\runner::runStart, array($runner))
		;
	}

	public function testAddTestObserver()
	{
		$runner = new atoum\runner();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\observers\test');

		$this->assert
			->array($runner->getTestObservers())->isEmpty()
			->object($runner->addTestObserver($observer = new mock\mageekguy\atoum\observers\test()))->isIdenticalTo($runner)
			->array($runner->getTestObservers())->isEqualTo(array($observer))
		;
	}

	public function testRemoveTestObserver()
	{
		$runner = new atoum\runner();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\observers\test');

		$this->assert
			->array($runner->getTestObservers())->isEmpty()
			->object($runner->removeTestObserver(new mock\mageekguy\atoum\observers\test()))->isIdenticalTo($runner)
			->array($runner->getTestObservers())->isEmpty()
		;

		$runner->addTestObserver($observer1 = new mock\mageekguy\atoum\observers\test());
		$runner->addTestObserver($observer2 = new mock\mageekguy\atoum\observers\test());

		$this->assert
			->array($runner->getTestObservers())->isEqualTo(array($observer1, $observer2))
			->object($runner->removeTestObserver(new mock\mageekguy\atoum\observers\test()))->isIdenticalTo($runner)
			->array($runner->getTestObservers())->isEqualTo(array($observer1, $observer2))
			->object($runner->removeTestObserver($observer1))->isIdenticalTo($runner)
			->array($runner->getTestObservers())->isEqualTo(array($observer2))
			->object($runner->removeTestObserver($observer2))->isIdenticalTo($runner)
			->array($runner->getTestObservers())->isEmpty()
		;
	}

	public function testGetRunningDuration()
	{
		$adapter = new atoum\test\adapter();
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
	}

	public function testGetTestNumber()
	{
		$adapter = new atoum\test\adapter();

		$adapter->get_declared_classes = array();

		$runner = new atoum\runner(null, $adapter);

		$this->assert
			->variable($runner->getTestNumber())->isNull();
		;

		$runner->run();

		$this->assert
			->integer($runner->getTestNumber())->isZero()
		;
	}

	public function testGetTestMethodNumber()
	{
		$adapter = new atoum\test\adapter();

		$adapter->get_declared_classes = array();

		$runner = new atoum\runner(null, $adapter);

		$this->assert
			->variable($runner->getTestMethodNumber())->isNull();
		;

		$runner->run();

		$this->assert
			->variable($runner->getTestMethodNumber())->isNull();
		;
	}

	public function testGetObserverEvents()
	{
		$this->assert
			->array(atoum\runner::getObserverEvents())->isEqualTo(array(atoum\runner::runStart, atoum\runner::runStop))
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
			->array($runner->getObservers())->contain($report)
			->array($runner->getTestObservers())->contain($report)
		;
	}

	public function testRemoveReport()
	{
		$runner = new atoum\runner();

		$this->assert
			->array($runner->getReports())->isEmpty()
			->array($runner->getObservers())->isEmpty()
			->array($runner->getTestObservers())->isEmpty()
			->object($runner->removeReport(new atoum\reports\realtime\cli()))->isIdenticalTo($runner)
			->array($runner->getReports())->isEmpty()
			->array($runner->getObservers())->isEmpty()
			->array($runner->getTestObservers())->isEmpty()
		;

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\report');

		$report1 = new mock\mageekguy\atoum\report();
		$report2 = new mock\mageekguy\atoum\report();

		$runner
			->addReport($report1)
			->addReport($report2)
		;

		$this->assert
			->array($runner->getReports())->isEqualTo(array($report1, $report2))
			->array($runner->getObservers())->isEqualTo(array($report1, $report2))
			->array($runner->getTestObservers())->isEqualTo(array($report1, $report2))
			->object($runner->removeReport(new atoum\reports\realtime\cli()))->isIdenticalTo($runner)
			->array($runner->getReports())->isEqualTo(array($report1, $report2))
			->array($runner->getObservers())->isEqualTo(array($report1, $report2))
			->array($runner->getTestObservers())->isEqualTo(array($report1, $report2))
			->object($runner->removeReport($report1))->isIdenticalTo($runner)
			->array($runner->getReports())->isEqualTo(array($report2))
			->array($runner->getObservers())->isEqualTo(array($report2))
			->array($runner->getTestObservers())->isEqualTo(array($report2))
			->object($runner->removeReport($report2))->isIdenticalTo($runner)
			->array($runner->getReports())->isEmpty()
			->array($runner->getObservers())->isEmpty()
			->array($runner->getTestObservers())->isEmpty()
		;
	}

	public function testRemoveReports()
	{
		$runner = new atoum\runner();

		$this->assert
			->array($runner->getReports())->isEmpty()
			->array($runner->getObservers())->isEmpty()
			->array($runner->getTestObservers())->isEmpty()
			->object($runner->removeReports())->isIdenticalTo($runner)
			->array($runner->getReports())->isEmpty()
			->array($runner->getObservers())->isEmpty()
			->array($runner->getTestObservers())->isEmpty()
		;

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\report');

		$report1 = new mock\mageekguy\atoum\report();
		$report2 = new mock\mageekguy\atoum\report();

		$runner
			->addReport($report1)
			->addReport($report2)
		;

		$this->assert
			->array($runner->getReports())->isEqualTo(array($report1, $report2))
			->array($runner->getObservers())->isEqualTo(array($report1, $report2))
			->array($runner->getTestObservers())->isEqualTo(array($report1, $report2))
			->object($runner->removeReports())->isIdenticalTo($runner)
			->array($runner->getReports())->isEmpty()
			->array($runner->getObservers())->isEmpty()
			->array($runner->getTestObservers())->isEmpty()
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

	public function testRun()
	{
	}
}

?>
