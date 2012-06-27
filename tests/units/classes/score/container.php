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
			->if($proxy = new testedClass($score = new atoum\score()))
			->then
				->integer($proxy->getPassNumber())->isZero()
				->array($proxy->getFailAssertions())->isEmpty()
				->array($proxy->getExceptions())->isEmpty()
				->array($proxy->getRuntimeExceptions())->isEmpty()
				->array($proxy->getErrors())->isEmpty()
				->array($proxy->getOutputs())->isEmpty()
				->array($proxy->getDurations())->isEmpty()
				->array($proxy->getMemoryUsages())->isEmpty()
				->array($proxy->getUncompletedMethods())->isEmpty()
				->object($proxy->getCoverage())->isEqualTo($score->getCoverage()->getContainer())
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
			->and($proxy = new testedClass($score))
			->then
				->integer($proxy->getPassNumber())->isEqualTo($score->getPassNumber())
				->array($proxy->getFailAssertions())->isEqualTo($score->getFailAssertions())
				->array($proxy->getExceptions())->isEqualTo($score->getExceptions())
				->array($proxy->getRuntimeExceptions())->isEqualTo($score->getRuntimeExceptions())
				->array($proxy->getErrors())->isEqualTo($score->getErrors())
				->array($proxy->getOutputs())->isEqualTo($score->getOutputs())
				->array($proxy->getDurations())->isEqualTo($score->getDurations())
				->array($proxy->getMemoryUsages())->isEqualTo($score->getMemoryUsages())
				->array($proxy->getUncompletedMethods())->isEqualTo($score->getUncompletedMethods())
				->object($proxy->getCoverage())->isEqualTo($score->getCoverage()->getContainer())
		;
	}
}
