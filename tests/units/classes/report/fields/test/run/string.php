<?php

namespace mageekguy\atoum\tests\units\report\fields\test\run;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\test
;

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
			->string(test\run\string::defaultPrompt)->isEqualTo('> ')
		;
	}

	public function test__construct()
	{
		$field = new test\run\string();

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($field->getTestClass())->isNull()
		;

		$field = new test\run\string($locale = new atoum\locale(), $prompt = uniqid());

		$this->assert
			->object($field->getLocale())->isIdenticalTo($locale)
			->string($field->getPrompt())->isEqualTo($prompt)
		;
	}

	public function testSetPrompt()
	{
		$field = new test\run\string();

		$this->assert
			->object($field->setPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getPrompt())->isEqualTo($prompt)
			->object($field->setPrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getPrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetWithTest()
	{
		$field = new test\run\string();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\test');

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new mock\mageekguy\atoum\test(null, null, $adapter, $testController);

		$this->assert
			->object($field->setWithTest($test))->isIdenticalTo($field)
			->variable($field->getTestClass())->isNull()
			->object($field->setWithTest($test, atoum\test::runStop))->isIdenticalTo($field)
			->variable($field->getTestClass())->isNull()
			->object($field->setWithTest($test, atoum\test::beforeSetUp))->isIdenticalTo($field)
			->variable($field->getTestClass())->isNull()
			->object($field->setWithTest($test, atoum\test::afterSetUp))->isIdenticalTo($field)
			->variable($field->getTestClass())->isNull()
			->object($field->setWithTest($test, atoum\test::beforeTestMethod))->isIdenticalTo($field)
			->variable($field->getTestClass())->isNull()
			->object($field->setWithTest($test, atoum\test::fail))->isIdenticalTo($field)
			->variable($field->getTestClass())->isNull()
			->object($field->setWithTest($test, atoum\test::error))->isIdenticalTo($field)
			->variable($field->getTestClass())->isNull()
			->object($field->setWithTest($test, atoum\test::exception))->isIdenticalTo($field)
			->variable($field->getTestClass())->isNull()
			->object($field->setWithTest($test, atoum\test::success))->isIdenticalTo($field)
			->variable($field->getTestClass())->isNull()
			->object($field->setWithTest($test, atoum\test::afterTestMethod))->isIdenticalTo($field)
			->variable($field->getTestClass())->isNull()
			->object($field->setWithTest($test, atoum\test::beforeTearDown))->isIdenticalTo($field)
			->variable($field->getTestClass())->isNull()
			->object($field->setWithTest($test, atoum\test::afterTearDown))->isIdenticalTo($field)
			->variable($field->getTestClass())->isNull()
			->object($field->setWithTest($test, atoum\test::runStart))->isIdenticalTo($field)
			->string($field->getTestClass())->isEqualTo($test->getClass())
		;
	}

	public function test__toString()
	{

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\test');

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new mock\mageekguy\atoum\test(null, null, $adapter, $testController);

		$field = new test\run\string();

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('There is currently no test running.') . PHP_EOL)
			->castToString($field->setWithTest($test))->isEqualTo($field->getPrompt() . $field->getLocale()->_('There is currently no test running.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStop))->isEqualTo($field->getPrompt() . $field->getLocale()->_('There is currently no test running.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::beforeSetUp))->isEqualTo($field->getPrompt() . $field->getLocale()->_('There is currently no test running.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::afterSetUp))->isEqualTo($field->getPrompt() . $field->getLocale()->_('There is currently no test running.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::beforeTestMethod))->isEqualTo($field->getPrompt() . $field->getLocale()->_('There is currently no test running.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::fail))->isEqualTo($field->getPrompt() . $field->getLocale()->_('There is currently no test running.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::error))->isEqualTo($field->getPrompt() . $field->getLocale()->_('There is currently no test running.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::exception))->isEqualTo($field->getPrompt() . $field->getLocale()->_('There is currently no test running.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::success))->isEqualTo($field->getPrompt() . $field->getLocale()->_('There is currently no test running.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::afterTestMethod))->isEqualTo($field->getPrompt() . $field->getLocale()->_('There is currently no test running.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::beforeTearDown))->isEqualTo($field->getPrompt() . $field->getLocale()->_('There is currently no test running.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::afterTearDown))->isEqualTo($field->getPrompt() . $field->getLocale()->_('There is currently no test running.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStart))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('Run %s...'), $test->getClass()) . PHP_EOL)
		;
	}
}

?>
