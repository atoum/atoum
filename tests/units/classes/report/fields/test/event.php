<?php

namespace mageekguy\atoum\tests\units\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\test;

require_once(__DIR__ . '/../../../../runner.php');

class event extends atoum\test
{
	public function test__construct()
	{
		$event = new test\event();

		$this->assert
			->object($event)->isInstanceOf('\mageekguy\atoum\report\fields\test')
			->variable($event->getValue())->isNull()
		;
	}

	public function testSetWithTest()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\test');

		$test = new mock\mageekguy\atoum\test();

		$event = new test\event();

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
}

?>
