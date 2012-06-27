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
			->if($container = new testedClass($score = new atoum\score()))
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
				->object($container->getCoverage())->isEqualTo($score->getCoverage()->getContainer())
			->if($score = new atoum\score())
			->and($score->addPass())
			->and($score->addFail(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer(new atoum\asserter\generator()), uniqid()))
			->and($score->addException(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new \exception()))
			->and($score->addRuntimeException(new atoum\exceptions\runtime()))
			->and($score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), E_ERROR, uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($score->addOutput(uniqid(), uniqid(), uniqid()))
			->and($score->addDuration(uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($score->addMemoryUsage(uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($score->addUncompletedMethod(uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))
			->and($container = new testedClass($score))
			->then
				->integer($container->getPassNumber())->isEqualTo($score->getPassNumber())
				->array($container->getFailAssertions())->isEqualTo($score->getFailAssertions())
				->array($container->getExceptions())->isEqualTo($score->getExceptions())
				->array($container->getRuntimeExceptions())->isEqualTo($score->getRuntimeExceptions())
				->array($container->getErrors())->isEqualTo($score->getErrors())
				->array($container->getOutputs())->isEqualTo($score->getOutputs())
				->array($container->getDurations())->isEqualTo($score->getDurations())
				->array($container->getMemoryUsages())->isEqualTo($score->getMemoryUsages())
				->array($container->getUncompletedMethods())->isEqualTo($score->getUncompletedMethods())
				->object($container->getCoverage())->isEqualTo($score->getCoverage()->getContainer())
		;
	}
}
