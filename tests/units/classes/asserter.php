<?php

namespace mageekguy\atoum\tests\units;

require __DIR__ . '/../runner.php';

use
	atoum,
	atoum\tools\variable
;

class asserter extends atoum
{
	public function testClass()
	{
		$this->testedClass
			->isAbstract
			->implements('mageekguy\atoum\asserter\definition')
		;
	}

	public function test__construct()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->getGenerator())->isEqualTo(new atoum\asserter\generator())
				->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new atoum\tools\variable\analyzer())

			->given($this->newTestedInstance($generator = new atoum\asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
			->then
				->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
				->object($this->testedInstance->getAnalyzer())->isEqualTo($analyzer)
				->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
		;
	}

	public function test__get()
	{
		$this
			->given($this->newTestedInstance($generator = new \mock\atoum\asserter\generator()))

			->if($this->calling($generator)->__get = $asserterInstance = new \mock\atoum\asserter())
			->then
				->object($this->testedInstance->{$asserterClass = uniqid()})->isIdenticalTo($asserterInstance)
				->mock($generator)->call('__get')->withArguments($asserterClass)->once
		;
	}

	public function test__call()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->integer($integer = rand(1, PHP_INT_MAX)))->isEqualTo($this->testedInstance->getGenerator()->integer($integer))
				->integer($this->testedInstance->integer($integer = rand(1, PHP_INT_MAX))->getValue())->isEqualTo($integer)
		;
	}

	public function testReset()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->reset())->isTestedInstance
		;
	}

	public function testSetLocale()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->setLocale($locale = new atoum\locale()))->isTestedInstance
				->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
				->object($this->testedInstance->setLocale())->isTestedInstance
				->object($this->testedInstance->getLocale())
					->isNotIdenticalTo($locale)
					->isEqualTo(new atoum\locale())
		;
	}

	public function testSetGenerator()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setGenerator($generator = new atoum\asserter\generator()))->isTestedInstance
				->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)

				->object($this->testedInstance->setGenerator())->isTestedInstance
				->object($this->testedInstance->getGenerator())
					->isNotIdenticalTo($generator)
					->isEqualTo(new atoum\asserter\generator())
		;
	}

	public function testSetWithTest()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setWithTest($this))->isTestedInstance
		;
	}

	public function testSetWithArguments()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setWithArguments(array()))->isTestedInstance
				->mock($this->testedInstance)->call('setWith')->never()
				->object($this->testedInstance->setWithArguments(array($argument = uniqid())))->isTestedInstance
				->mock($this->testedInstance)->call('setWith')->withArguments($argument)->once
		;
	}
}
