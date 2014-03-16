<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\diffs,
	mageekguy\atoum\tools\variable
;

require_once __DIR__ . '/../../runner.php';

class constant extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->getGenerator())->isEqualTo(new asserter\generator())
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new variable\analyzer())
				->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
				->object($this->testedInstance->getDiff())->isEqualTo(new diffs\variable())
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse()

			->given($this->newTestedInstance($generator = new asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
			->then
				->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
				->object($this->testedInstance->getAnalyzer())->isIdenticalTo($analyzer)
				->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
				->object($this->testedInstance->getDiff())->isEqualTo(new diffs\variable())
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse()
		;
	}

	public function testSetDiff()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setDiff($diff = new diffs\variable()))->isTestedInstance
				->object($this->testedInstance->getDiff())->isIdenticalTo($diff)
				->object($this->testedInstance->setDiff())->isTestedInstance
				->object($this->testedInstance->getDiff())
					->isNotIdenticalTo($diff)
					->isEqualTo(new diffs\variable())
		;
	}

	public function test__get()
	{
		$this
			->if($asserter = $this->newTestedInstance(new asserter\generator()))
			->then
				->exception(function() use ($asserter, & $property) { $asserter->{$property = uniqid()}; })
					->isInstanceOf('logicException')
					->hasMessage('Asserter \'' . $property . '\' does not exist')
				->variable($asserter->getValue())->isNull()

			->if($asserter->setWith($value = uniqid()))
			->then
				->string($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testReset()
	{
		$this
			->given($this->newTestedInstance(new asserter\generator()))

			->if($this->testedInstance->setWith(uniqid()))
			->then
				->object($this->testedInstance->reset())->isTestedInstance
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$this
			->given($this->newTestedInstance(new asserter\generator()))

			->if($value = uniqid())
			->then
				->object($this->testedInstance->setWith($value = uniqid()))->isTestedInstance
				->variable($this->testedInstance->getValue())->isIdenticalTo($value)
				->boolean($this->testedInstance->wasSet())->isTrue()
		;
	}

	public function testIsEqualTo()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setDiff($diff = new \mock\atoum\tools\diffs\variable())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isEqualTo(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')

			->if($asserter->setWith($value = uniqid()))
			->then
				->object($asserter->isEqualTo($value))->isIdenticalTo($asserter)

			->if(
				$this->calling($locale)->_ = $isNotEqual = uniqid(),
				$this->calling($diff)->__toString = $diffValue = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $notEqualValue) { $asserter->isEqualTo($notEqualValue = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($isNotEqual . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not equal to %s', $asserter, $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments($notEqualValue)->once
				->mock($diff)
					->call('setExpected')->withArguments($value)->once
					->call('setActual')->withArguments($notEqualValue)->once
		;
	}

	public function testEqualTo()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setDiff($diff = new \mock\atoum\tools\diffs\variable())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $asserter->equalTo(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')

			->if($asserter->setWith($value = uniqid()))
			->then
				->object($asserter->equalTo($value))->isIdenticalTo($asserter)

			->if(
				$this->calling($locale)->_ = $isNotEqual = uniqid(),
				$this->calling($diff)->__toString = $diffValue = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $notEqualValue) { $asserter->isEqualTo($notEqualValue = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($isNotEqual . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not equal to %s', $asserter, $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments($notEqualValue)->once
				->mock($diff)
					->call('setExpected')->withArguments($value)->once
					->call('setActual')->withArguments($notEqualValue)->once
		;
	}
}
