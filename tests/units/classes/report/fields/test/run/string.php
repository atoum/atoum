<?php

namespace mageekguy\atoum\tests\units\report\fields\test\run;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\test;

require_once(__DIR__ . '/../../../../../runner.php');

class string extends \mageekguy\atoum\tests\units\report\fields\test\run
{
	public function testClass()
	{
		$this->assert
			->class('\mageekguy\atoum\report\fields\test\run\string')->isSubClassOf('\mageekguy\atoum\report\fields\test')
		;
	}

	public function testClassConstants()
	{
		$this->assert
			->string(test\run\string::titlePrompt)->isEqualTo('> ')
		;
	}

	public function test__construct()
	{
		$run = new test\run\string();

		$this->assert
			->object($run)->isInstanceOf('\mageekguy\atoum\report\fields\test')
			->variable($run->getTestClass())->isNull()
		;

		$run = new test\run\string($label = uniqid(), $prompt = uniqid(), $locale = new atoum\locale());

		$this->assert
			->string($run->getLabel())->isEqualTo($label)
			->string($run->getPrompt())->isEqualTo($prompt)
			->object($run->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetWithTest()
	{
		$run = new test\run\string();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\test');

		$adapter = new atoum\test\adapter();
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

	public function test__toString()
	{
		$run = new test\run\string(null, null, $locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\test');

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new mock\mageekguy\atoum\test(null, null, $adapter, $testController);

		$this->assert
			->castToString($run)->isEqualTo(test\run\string::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test))->isEqualTo(test\run\string::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::runStop))->isEqualTo(test\run\string::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::beforeSetUp))->isEqualTo(test\run\string::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::afterSetUp))->isEqualTo(test\run\string::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::beforeTestMethod))->isEqualTo(test\run\string::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::fail))->isEqualTo(test\run\string::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::error))->isEqualTo(test\run\string::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::exception))->isEqualTo(test\run\string::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::success))->isEqualTo(test\run\string::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::afterTestMethod))->isEqualTo(test\run\string::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::beforeTearDown))->isEqualTo(test\run\string::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::afterTearDown))->isEqualTo(test\run\string::titlePrompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::runStart))->isEqualTo(test\run\string::titlePrompt . sprintf($locale->_('Run %s...'), $test->getClass()) . PHP_EOL)
		;

		$run = new test\run\string($label = uniqid(), $prompt = uniqid(), $locale = new atoum\locale());

		$this->assert
			->castToString($run)->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::runStop))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::beforeSetUp))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::afterSetUp))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::beforeTestMethod))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::fail))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::error))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::exception))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::success))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::afterTestMethod))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::beforeTearDown))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::afterTearDown))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::runStart))->isEqualTo($prompt . $locale->_($label) . PHP_EOL)
		;

		$run = new test\run\string($label = uniqid() . ' %s', $prompt = uniqid(), $locale = new atoum\locale());

		$this->assert
			->castToString($run)->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::runStop))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::beforeSetUp))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::afterSetUp))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::beforeTestMethod))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::fail))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::error))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::exception))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::success))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::afterTestMethod))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::beforeTearDown))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::afterTearDown))->isEqualTo($prompt . $locale->_('There is currently no test running.') . PHP_EOL)
			->castToString($run->setWithTest($test, atoum\test::runStart))->isEqualTo($prompt . sprintf($locale->_($label), $test->getClass()) . PHP_EOL)
		;
	}
}

?>
