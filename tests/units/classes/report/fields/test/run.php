<?php

namespace mageekguy\atoum\tests\units\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\test;

require_once(__DIR__ . '/../../../../runner.php');

class run extends atoum\test
{
	public function test__construct()
	{
		$run = new test\run();

		$this->assert
			->object($run)->isInstanceOf('\mageekguy\atoum\report\fields\test')
			->variable($run->getTestClass())->isNull()
		;
	}

	public function testSetWithTest()
	{
		$run = new test\run();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\test');

		$test = new mock\mageekguy\atoum\test();

		$this->assert
			->object($run->setWithTest($test))->isIdenticalTo($run)
			->variable($run->getTestClass())->isNull()
			->object($run->setWithTest($test, atoum\test::runStop))->isIdenticalTo($run)
			->variable($run->getTestClass())->isNull()
			->object($run->setWithTest($test, atoum\test::beforeSetUp))->isIdenticalTo($run)
			->variable($run->getTestClass())->isNull()
			->object($run->setWithTest($test, atoum\test::afterSetUp))->isIdenticalTo($run)
			->variable($run->getTestClass())->isNull()
			->object($run->setWithTest($test, atoum\test::beforeTestMethod))->isIdenticalTo($run)
			->variable($run->getTestClass())->isNull()
			->object($run->setWithTest($test, atoum\test::fail))->isIdenticalTo($run)
			->variable($run->getTestClass())->isNull()
			->object($run->setWithTest($test, atoum\test::error))->isIdenticalTo($run)
			->variable($run->getTestClass())->isNull()
			->object($run->setWithTest($test, atoum\test::exception))->isIdenticalTo($run)
			->variable($run->getTestClass())->isNull()
			->object($run->setWithTest($test, atoum\test::success))->isIdenticalTo($run)
			->variable($run->getTestClass())->isNull()
			->object($run->setWithTest($test, atoum\test::afterTestMethod))->isIdenticalTo($run)
			->variable($run->getTestClass())->isNull()
			->object($run->setWithTest($test, atoum\test::beforeTearDown))->isIdenticalTo($run)
			->variable($run->getTestClass())->isNull()
			->object($run->setWithTest($test, atoum\test::afterTearDown))->isIdenticalTo($run)
			->variable($run->getTestClass())->isNull()
			->object($run->setWithTest($test, atoum\test::runStart))->isIdenticalTo($run)
			->string($run->getTestClass())->isEqualTo($test->getClass())
		;
	}

	public function testToString()
	{
		$run = new test\run($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\test');

		$test = new mock\mageekguy\atoum\test();

		$this->assert
			->string($run->toString())->isEqualTo($locale->_('There is currently no test running.'))
			->string($run->setWithTest($test)->toString())->isEqualTo($locale->_('There is currently no test running.'))
			->string($run->setWithTest($test, atoum\test::runStop)->toString())->isEqualTo($locale->_('There is currently no test running.'))
			->string($run->setWithTest($test, atoum\test::beforeSetUp)->toString())->isEqualTo($locale->_('There is currently no test running.'))
			->string($run->setWithTest($test, atoum\test::afterSetUp)->toString())->isEqualTo($locale->_('There is currently no test running.'))
			->string($run->setWithTest($test, atoum\test::beforeTestMethod)->toString())->isEqualTo($locale->_('There is currently no test running.'))
			->string($run->setWithTest($test, atoum\test::fail)->toString())->isEqualTo($locale->_('There is currently no test running.'))
			->string($run->setWithTest($test, atoum\test::error)->toString())->isEqualTo($locale->_('There is currently no test running.'))
			->string($run->setWithTest($test, atoum\test::exception)->toString())->isEqualTo($locale->_('There is currently no test running.'))
			->string($run->setWithTest($test, atoum\test::success)->toString())->isEqualTo($locale->_('There is currently no test running.'))
			->string($run->setWithTest($test, atoum\test::afterTestMethod)->toString())->isEqualTo($locale->_('There is currently no test running.'))
			->string($run->setWithTest($test, atoum\test::beforeTearDown)->toString())->isEqualTo($locale->_('There is currently no test running.'))
			->string($run->setWithTest($test, atoum\test::afterTearDown)->toString())->isEqualTo($locale->_('There is currently no test running.'))
			->string($run->setWithTest($test, atoum\test::runStart)->toString())->isEqualTo(sprintf($locale->_('Run %s...'), $test->getClass()))
		;
	}
}

?>
