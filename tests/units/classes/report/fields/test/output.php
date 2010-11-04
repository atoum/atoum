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
		$output = new test\output();

		$this->assert
			->object($output)->isInstanceOf('\mageekguy\atoum\report\fields\test')
			->variable($output->getTest())->isNull()
			->object($output->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
		;
	}

	public function testSetWithTest()
	{
		$output = new test\output();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\test');

		$test = new mock\mageekguy\atoum\test();

		$this->assert
			->object($output->setWithTest($test))->isIdenticalTo($output)
			->object($output->getTest())->isIdenticalTo($test)
			->object($output->setWithTest($test, atoum\test::runStart))->isIdenticalTo($output)
			->object($output->getTest())->isIdenticalTo($test)
			->object($output->setWithTest($test, atoum\test::beforeSetUp))->isIdenticalTo($output)
			->object($output->getTest())->isIdenticalTo($test)
			->object($output->setWithTest($test, atoum\test::afterSetUp))->isIdenticalTo($output)
			->object($output->getTest())->isIdenticalTo($test)
			->object($output->setWithTest($test, atoum\test::beforeTestMethod))->isIdenticalTo($output)
			->object($output->getTest())->isIdenticalTo($test)
			->object($output->setWithTest($test, atoum\test::fail))->isIdenticalTo($output)
			->object($output->getTest())->isIdenticalTo($test)
			->object($output->setWithTest($test, atoum\test::error))->isIdenticalTo($output)
			->object($output->getTest())->isIdenticalTo($test)
			->object($output->setWithTest($test, atoum\test::exception))->isIdenticalTo($output)
			->object($output->getTest())->isIdenticalTo($test)
			->object($output->setWithTest($test, atoum\test::success))->isIdenticalTo($output)
			->object($output->getTest())->isIdenticalTo($test)
			->object($output->setWithTest($test, atoum\test::afterTestMethod))->isIdenticalTo($output)
			->object($output->getTest())->isIdenticalTo($test)
			->object($output->setWithTest($test, atoum\test::beforeTearDown))->isIdenticalTo($output)
			->object($output->getTest())->isIdenticalTo($test)
			->object($output->setWithTest($test, atoum\test::afterTearDown))->isIdenticalTo($output)
			->object($output->getTest())->isIdenticalTo($test)
			->object($output->setWithTest($test, atoum\test::runStop))->isIdenticalTo($output)
			->object($output->getTest())->isIdenticalTo($test)
		;
	}

	public function testToString()
	{
		$output = new test\output($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\test')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getOutputs = function() { return array(); };

		$test = new mock\mageekguy\atoum\test();
		$test->getMockController()->getScore = function() use ($score) { return $score; };

		$this->assert
			->string($output->toString())->isEmpty()
			->string($output->setWithTest($test)->toString())->isEmpty()
			->string($output->setWithTest($test, atoum\test::runStart)->toString())->isEmpty()
			->string($output->setWithTest($test, atoum\test::beforeSetUp)->toString())->isEmpty()
			->string($output->setWithTest($test, atoum\test::afterSetUp)->toString())->isEmpty()
			->string($output->setWithTest($test, atoum\test::beforeTestMethod)->toString())->isEmpty()
			->string($output->setWithTest($test, atoum\test::fail)->toString())->isEmpty()
			->string($output->setWithTest($test, atoum\test::error)->toString())->isEmpty()
			->string($output->setWithTest($test, atoum\test::exception)->toString())->isEmpty()
			->string($output->setWithTest($test, atoum\test::success)->toString())->isEmpty()
			->string($output->setWithTest($test, atoum\test::afterTestMethod)->toString())->isEmpty()
			->string($output->setWithTest($test, atoum\test::beforeTearDown)->toString())->isEmpty()
			->string($output->setWithTest($test, atoum\test::afterTearDown)->toString())->isEmpty()
			->string($output->setWithTest($test, atoum\test::runStop)->toString())->isEmpty()
		;

		$score->getMockController()->getOutputs = function() { return array(
				array(
					'class' => $class = uniqid(),
					'method' => $method = uniqid(),
					'value' => $value = uniqid()
				),
				array(
					'class' => $otherClass = uniqid(),
					'method' => $otherMethod = uniqid(),
					'value' => $otherValue = uniqid()
				)
			);
		};

		$output = new test\output($locale = new atoum\locale());

		$this->assert
			->string($output->toString())->isEmpty()
			->string($output->setWithTest($test)->toString())->isEqualTo($locale->_('Outputs:') . PHP_EOL)
		;
	}
}

?>
