<?php

namespace mageekguy\atoum\tests\units\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\test;

require_once(__DIR__ . '/../../../../runner.php');

class run extends atoum\test
{
	public function testClassConstants()
	{
		$this->assert
			->string(test\run::titlePrompt)->isEqualTo('> ')
		;
	}

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

		$adapter = new atoum\adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new mock\mageekguy\atoum\test(null, null, $adapter, $testController);

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

		$adapter = new atoum\adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new mock\mageekguy\atoum\test(null, null, $adapter, $testController);

		$this->assert
			->string($run->toString())->isEqualTo(test\run::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->string($run->setWithTest($test)->toString())->isEqualTo(test\run::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->string($run->setWithTest($test, atoum\test::runStop)->toString())->isEqualTo(test\run::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->string($run->setWithTest($test, atoum\test::beforeSetUp)->toString())->isEqualTo(test\run::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->string($run->setWithTest($test, atoum\test::afterSetUp)->toString())->isEqualTo(test\run::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->string($run->setWithTest($test, atoum\test::beforeTestMethod)->toString())->isEqualTo(test\run::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->string($run->setWithTest($test, atoum\test::fail)->toString())->isEqualTo(test\run::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->string($run->setWithTest($test, atoum\test::error)->toString())->isEqualTo(test\run::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->string($run->setWithTest($test, atoum\test::exception)->toString())->isEqualTo(test\run::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->string($run->setWithTest($test, atoum\test::success)->toString())->isEqualTo(test\run::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->string($run->setWithTest($test, atoum\test::afterTestMethod)->toString())->isEqualTo(test\run::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->string($run->setWithTest($test, atoum\test::beforeTearDown)->toString())->isEqualTo(test\run::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->string($run->setWithTest($test, atoum\test::afterTearDown)->toString())->isEqualTo(test\run::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->string($run->setWithTest($test, atoum\test::runStart)->toString())->isEqualTo(test\run::titlePrompt . sprintf($locale->_('Run %s...'), $test->getClass()) . PHP_EOL)
		;
	}
}

?>
