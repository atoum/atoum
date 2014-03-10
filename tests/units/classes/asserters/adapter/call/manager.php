<?php

namespace mageekguy\atoum\tests\units\asserters\adapter\call;

require __DIR__ . '/../../../../runner.php';

use
	atoum
;

class manager extends atoum
{
	public function testAdd()
	{
		$this
			->given($manager = $this->newTestedInstance)
			->then
				->object($this->testedInstance->add($call = new \mock\atoum\asserters\adapter\call()))->isTestedInstance
				->object($this->testedInstance->add($call))->isTestedInstance
		;
	}

	public function testRemove()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->remove($call = new \mock\atoum\asserters\adapter\call()))->isTestedInstance
				->object($this->testedInstance->add($call = new \mock\atoum\asserters\adapter\call()))->isTestedInstance
		;
	}

	public function testCheck()
	{
		$this
			->given($manager = $this->newTestedInstance)
			->then
				->object($this->testedInstance->check())->isTestedInstance

			->if(
				$this->testedInstance->add($call = new \mock\atoum\asserters\adapter\call()),
				$this->calling($call)->getLastAssertionFile = $file = uniqid(),
				$this->calling($call)->getLastAssertionLine = $line = rand(1, PHP_INT_MAX)
			)
			->then
				->exception(function() use ($manager) { $manager->check(); })
					->isInstanceOf('mageekguy\atoum\asserters\adapter\call\manager\exception')
					->hasMessage('Asserter ' . get_class($call) . ' is not evaluated in file \'' . $file . '\' on line ' . $line);
		;
	}
}
