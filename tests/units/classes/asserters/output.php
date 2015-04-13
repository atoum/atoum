<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\variable
;

require_once __DIR__ . '/../../runner.php';

class output extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserters\phpString');
	}

	public function test__construct()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->getGenerator())->isEqualTo(new asserter\generator())
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new variable\analyzer())
				->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
				->string($this->testedInstance->getValue())->isEmpty()
				->boolean($this->testedInstance->wasSet())->isTrue()

			->if($this->newTestedInstance($generator = new asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
			->then
				->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
				->object($this->testedInstance->getAnalyzer())->isIdenticalTo($analyzer)
				->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
				->string($this->testedInstance->getValue())->isEmpty()
				->boolean($this->testedInstance->wasSet())->isTrue()
		;
	}

	public function testSetWith()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->setWith(function() use (& $output) { echo ($output = uniqid()); }))->isTestedInstance
				->string($this->testedInstance->getValue())->isEqualTo($output)
				->variable($this->testedInstance->getCharlist())->isNull()
				->object($this->testedInstance->setWith(function() use (& $output) { echo ($output = uniqid()); }, "\010"))->isTestedInstance
				->string($this->testedInstance->getValue())->isEqualTo($output)
				->string($this->testedInstance->getCharlist())->isEqualTo("\010")
		;
	}
}
