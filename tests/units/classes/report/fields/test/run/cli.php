<?php

namespace mageekguy\atoum\tests\units\report\fields\test\run;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\locale,
	mageekguy\atoum\test\adapter,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\test
;

require_once __DIR__ . '/../../../../../runner.php';

class cli extends \mageekguy\atoum\tests\units\report\fields\test\run
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubClassOf('mageekguy\atoum\report\fields\test')
		;
	}

	public function test__construct()
	{
		$field = new test\run\cli();

		$this->assert
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getTestClass())->isNull()
		;

		$field = new test\run\cli($prompt = new prompt(), $colorizer = new colorizer(), $locale = new locale());

		$this->assert
			->object($field->getPrompt())->isIdenticalTo($prompt)
			->object($field->getColorizer())->isIdenticalTo($colorizer)
			->object($field->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetPrompt()
	{
		$field = new test\run\cli();

		$this->assert
			->object($field->setPrompt($prompt = new prompt()))->isIdenticalTo($field)
			->object($field->getPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetColorizer()
	{
		$field = new test\run\cli();

		$this->assert
			->object($field->setColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetWithTest()
	{
		$field = new test\run\cli();

		$this->mockGenerator
			->generate('mageekguy\atoum\test')
		;

		$adapter = new adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new \mock\mageekguy\atoum\test(null, null, $adapter, null, null, $testController);

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
		$this->mockGenerator
			->generate('mageekguy\atoum\test')
		;

		$adapter = new adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new \mock\mageekguy\atoum\test(null, null, $adapter, null, null, $testController);

		$field = new test\run\cli();

		$this->assert
			->castToString($field)->isEqualTo('There is currently no test running.' . PHP_EOL)
			->castToString($field->setWithTest($test))->isEqualTo('There is currently no test running.' . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStop))->isEqualTo('There is currently no test running.' . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::beforeSetUp))->isEqualTo('There is currently no test running.' . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::afterSetUp))->isEqualTo('There is currently no test running.' . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::beforeTestMethod))->isEqualTo('There is currently no test running.' . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::fail))->isEqualTo('There is currently no test running.' . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::error))->isEqualTo('There is currently no test running.' . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::exception))->isEqualTo('There is currently no test running.' . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::success))->isEqualTo('There is currently no test running.' . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::afterTestMethod))->isEqualTo('There is currently no test running.' . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::beforeTearDown))->isEqualTo('There is currently no test running.' . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::afterTearDown))->isEqualTo('There is currently no test running.' . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStart))->isEqualTo(sprintf('%s...', $test->getClass()) . PHP_EOL)
		;

		$field = new test\run\cli($prompt = new prompt(uniqid()), $colorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());

		$this->assert
			->castToString($field)->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->castToString($field->setWithTest($test))->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStop))->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::beforeSetUp))->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::afterSetUp))->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::beforeTestMethod))->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::fail))->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::error))->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::exception))->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::success))->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::afterTestMethod))->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::beforeTearDown))->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::afterTearDown))->isEqualTo($prompt . $colorizer->colorize($locale->_('There is currently no test running.')) . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStart))->isEqualTo($prompt . sprintf($locale->_('%s...'), $colorizer->colorize($test->getClass())) . PHP_EOL)
		;
	}
}

?>
