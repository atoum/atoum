<?php

namespace mageekguy\atoum\tests\units\report\fields\test\event;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report\fields\test
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends \mageekguy\atoum\tests\units\report\fields\test\event
{
	public function test__construct()
	{
		$event = new test\event\cli();

		$this->assert
			->object($event)->isInstanceOf('\mageekguy\atoum\report\fields\test')
			->variable($event->getValue())->isNull()
		;
	}

	public function testGetProgressBar()
	{
		$event = new test\event\cli();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\test');

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new mock\mageekguy\atoum\test(null, null, $adapter, $testController);
		$this->assert
			->variable($event->getTest())->isNull()
			->exception(function() use ($event) { $event->getProgressBar(); })
				->isInstanceOf('\logicException')
				->hasMessage('Unable to get progress bar because test is undefined')
			->object($event->setWithTest($test)->getProgressBar())->isInstanceOf('\mageekguy\atoum\cli\progressBar')
		;
	}

	public function testSetWithTest()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\test');

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new mock\mageekguy\atoum\test(null, null, $adapter, $testController);

		$event = new test\event\cli();

		$this->assert
			->object($event->setWithTest($test))->isIdenticalTo($event)
			->variable($event->getValue())->isNull()
			->object($event->setWithTest($test, atoum\test::runStart))->isIdenticalTo($event)
			->string($event->getValue())->isEqualTo(atoum\test::runStart)
			->object($event->setWithTest($test, atoum\test::beforeSetUp))->isIdenticalTo($event)
			->string($event->getValue())->isEqualTo(atoum\test::beforeSetUp)
			->object($event->setWithTest($test, atoum\test::afterSetUp))->isIdenticalTo($event)
			->string($event->getValue())->isEqualTo(atoum\test::afterSetUp)
			->object($event->setWithTest($test, atoum\test::beforeTestMethod))->isIdenticalTo($event)
			->string($event->getValue())->isEqualTo(atoum\test::beforeTestMethod)
			->object($event->setWithTest($test, atoum\test::fail))->isIdenticalTo($event)
			->string($event->getValue())->isEqualTo(atoum\test::fail)
			->object($event->setWithTest($test, atoum\test::error))->isIdenticalTo($event)
			->string($event->getValue())->isEqualTo(atoum\test::error)
			->object($event->setWithTest($test, atoum\test::exception))->isIdenticalTo($event)
			->string($event->getValue())->isEqualTo(atoum\test::exception)
			->object($event->setWithTest($test, atoum\test::success))->isIdenticalTo($event)
			->string($event->getValue())->isEqualTo(atoum\test::success)
			->object($event->setWithTest($test, atoum\test::afterTestMethod))->isIdenticalTo($event)
			->string($event->getValue())->isEqualTo(atoum\test::afterTestMethod)
			->object($event->setWithTest($test, atoum\test::beforeTearDown))->isIdenticalTo($event)
			->string($event->getValue())->isEqualTo(atoum\test::beforeTearDown)
			->object($event->setWithTest($test, atoum\test::afterTearDown))->isIdenticalTo($event)
			->string($event->getValue())->isEqualTo(atoum\test::afterTearDown)
			->object($event->setWithTest($test, atoum\test::runStop))->isIdenticalTo($event)
			->string($event->getValue())->isEqualTo(atoum\test::runStop)
		;
	}

	public function testSetProgressBarInjector()
	{
		$event = new test\event\cli();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\test');

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new mock\mageekguy\atoum\test(null, null, $adapter, $testController);

		$this->assert
			->object($event->setProgressBarInjector(function($test) use (& $progressBar) { return $progressBar = new atoum\cli\progressBar($test); }))->isIdenticalTo($event)
			->object($event->setWithTest($test)->getProgressBar())->isIdenticalTo($progressBar)
		;

		$this->assert
			->exception(function() use ($event) {
						$event->setProgressBarInjector(function() {});
					}
				)
				->isInstanceOf('\invalidArgumentException')
				->hasMessage('Progress bar injector must take one argument')
		;
	}

	public function test__toString()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\test')
		;

		$event = new test\event\cli();

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new mock\mageekguy\atoum\test(null, null, $adapter, $testController);

		$count = rand(1, PHP_INT_MAX);
		$test->getMockController()->count = function() use ($count) { return $count; };

		$progressBar = new atoum\cli\progressBar($test);

		$this->assert
			->string($event->__toString($progressBar))->isEmpty()
			->object($event->setWithTest($test, atoum\test::runStart))->isIdenticalTo($event)
			->castToString($event)->isEqualTo((string) $progressBar)
			->object($event->setWithTest($test, atoum\test::beforeSetUp))->isIdenticalTo($event)
			->castToString($event)->isEqualTo((string) $progressBar)
			->object($event->setWithTest($test, atoum\test::afterSetUp))->isIdenticalTo($event)
			->castToString($event)->isEqualTo((string) $progressBar)
			->object($event->setWithTest($test, atoum\test::beforeTestMethod))->isIdenticalTo($event)
			->castToString($event)->isEqualTo((string) $progressBar)
			->object($event->setWithTest($test, atoum\test::fail))->isIdenticalTo($event)
			->castToString($event)->isEqualTo((string) $progressBar->refresh('F'))
			->object($event->setWithTest($test, atoum\test::error))->isIdenticalTo($event)
			->castToString($event)->isEqualTo((string) $progressBar->refresh('e'))
			->object($event->setWithTest($test, atoum\test::exception))->isIdenticalTo($event)
			->castToString($event)->isEqualTo((string) $progressBar->refresh('E'))
			->object($event->setWithTest($test, atoum\test::success))->isIdenticalTo($event)
			->castToString($event)->isEqualTo((string) $progressBar->refresh('S'))
			->object($event->setWithTest($test, atoum\test::afterTestMethod))->isIdenticalTo($event)
			->castToString($event)->isEqualTo((string) $progressBar)
			->object($event->setWithTest($test, atoum\test::beforeTearDown))->isIdenticalTo($event)
			->castToString($event)->isEqualTo((string) $progressBar)
			->castToString($event)->isEqualTo((string) $progressBar)
			->object($event->setWithTest($test, atoum\test::afterTearDown))->isIdenticalTo($event)
			->castToString($event)->isEqualTo((string) $progressBar)
			->object($event->setWithTest($test, atoum\test::runStop))->isIdenticalTo($event)
			->castToString($event)->isEqualTo(PHP_EOL)
		;
	}
}

?>
