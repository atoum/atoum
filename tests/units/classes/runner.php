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
			->variable($runner->getRunningDuration())->isNull()
		;

		$runner = new atoum\runner($score = new atoum\score(), $adapter = new atoum\adapter());

		$this->assert
			->object($runner->getScore())->isIdenticalTo($score)
			->object($runner->getAdapter())->isIdenticalTo($adapter)
			->variable($runner->getRunningDuration())->isNull()
		;
	}

	public function testAddObserver()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\observer');
	}

	public function testGetRunningDuration()
	{
		$adapter = new atoum\adapter();
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
		$adapter = new atoum\adapter();
		$adapter->microtime = function() { static $call = 0; return (++$call * 100); };
		$adapter->get_declared_classes = function() { return array(); };

		$runner = new atoum\runner(null, $adapter);

		$this->assert
			->variable($runner->getTestNumber())->isNull();
		;

		$runner->run();

		$this->assert
			->integer($runner->getTestNumber())->isZero()
		;

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\test');

		$run = function() {};

		$test1 = new mock\mageekguy\atoum\test();
		$test1->getMockController()->run = $run;

		$test2 = new mock\mageekguy\atoum\test();
		$test2->getMockController()->run = $run;

		$test3 = new mock\mageekguy\atoum\test();
		$test3->getMockController()->run = $run;

		$testClasses = array($test1, $test2, $test3);

		$adapter->get_declared_classes = function() use ($testClasses) { return $testClasses; };

		$runner->run();

		$this->assert
			->integer($runner->getTestNumber())->isEqualTo(sizeof($testClasses))
		;
	}

	public function testGetTestMethodNumber()
	{
		$adapter = new atoum\adapter();
		$adapter->get_declared_classes = function() { return array(); };

		$runner = new atoum\runner(null, $adapter);

		$this->assert
			->variable($runner->getTestMethodNumber())->isNull();
		;

		$runner->run();

		$this->assert
			->variable($runner->getTestMethodNumber())->isNull();
		;

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\test');

		$adapter->get_declared_classes = function() { return array('\mageekguy\atoum\mock\mageekguy\atoum\test'); };

		$runner->run();

		$this->assert
			->integer($runner->getTestMethodNumber())->isZero()
		;
	}
}

?>
