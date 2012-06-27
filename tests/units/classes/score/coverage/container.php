<?php

namespace mageekguy\atoum\tests\units\score\coverage;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\score,
	mageekguy\atoum\score\coverage\container as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class container extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($coverage = new score\coverage($factory = new atoum\factory()))
			->and($container = new testedClass($coverage))
			->then
				->array($container->getClasses())->isEmpty()
				->array($container->getMethods())->isEmpty()
			->if($coverage = new \mock\mageekguy\atoum\score\coverage())
			->and($coverage->getMockController()->getClasses = array(
					$className = uniqid() => $classFile = uniqid()
				)
			)
			->and($coverage->getMockController()->getMethods = array(
					$className => array(
						$methodName = uniqid() => array(
							6 => -1,
							7 => 1,
							8 => -2
						)
					)
				)
			)
			->and($container = new testedClass($coverage))
			->then
				->array($container->getClasses())->isEqualTo($coverage->getClasses())
				->array($container->getMethods())->isEqualTo($coverage->getMethods())
		;
	}
}
