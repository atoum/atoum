<?php

namespace mageekguy\atoum\tests\units\score\coverage;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\score,
	mageekguy\atoum\score\coverage\container as testedClass,
	mock\mageekguy\atoum\score\coverage\container as mockedTestedClass
;

require_once __DIR__ . '/../../../runner.php';

class container extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($container = new testedClass())
			->then
				->array($container->getClasses())->isEmpty()
				->array($container->getMethods())->isEmpty()
		;
	}

	public function testMerge()
	{
		$this
			->if($container = new testedClass())
			->and($otherContainer = new mockedTestedClass())
			->then
				->object($container->merge($otherContainer))->isIdenticalTo($container)
				->array($container->getClasses())->isEmpty()
				->array($container->getMethods())->isEmpty()
			->if($otherContainer->getMockController()->getClasses = $classes = array(uniqid(), uniqid()))
			->and($otherContainer->getMockController()->getMethods = $methods = array(uniqid(), uniqid()))
			->then
				->object($container->merge($otherContainer))->isIdenticalTo($container)
				->array($container->getClasses())->isEqualTo($classes)
				->array($container->getMethods())->isEqualTo($methods)
		;
	}
}
