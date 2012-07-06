<?php

namespace mageekguy\atoum\tests\units\score;

use
	mageekguy\atoum,
	mageekguy\atoum\score\container as testedClass
;

require_once __DIR__ . '/../../runner.php';

class container extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($container = new testedClass())
			->then
				->integer($container->getPassNumber())->isZero()
				->array($container->getFailAssertions())->isEmpty()
				->array($container->getExceptions())->isEmpty()
				->array($container->getRuntimeExceptions())->isEmpty()
				->array($container->getErrors())->isEmpty()
				->array($container->getOutputs())->isEmpty()
				->array($container->getDurations())->isEmpty()
				->array($container->getMemoryUsages())->isEmpty()
				->array($container->getUncompletedMethods())->isEmpty()
				->variable($container->getCoverage())->isNull()
		;
	}
}
