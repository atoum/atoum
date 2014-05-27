<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\diffs,
	mageekguy\atoum\tools\variable
;

require_once __DIR__ . '/../../runner.php';

class boolean extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserters\variable');
	}

	public function test__construct()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->getGenerator())->isEqualTo(new asserter\generator())
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new variable\analyzer())
				->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse()

			->given($this->newTestedInstance($generator = new asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
			->then
				->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
				->object($this->testedInstance->getAnalyzer())->isIdenticalTo($analyzer)
				->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse()
		;
	}

	public function testIsTrue()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->isTrue(); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')

			->if($asserter->setWith(true))
			->then
				->object($asserter->isTrue())->isIdenticalTo($asserter)
				->object($asserter->isTrue)->isIdenticalTo($asserter)

			->if(
				$asserter
					->setWith(false)
					->setLocale($locale = new \mock\atoum\locale())
					->setDiff($diff = new \mock\atoum\tools\diffs\variable()),
				$this->calling($locale)->_ = $notTrue = uniqid(),
				$this->calling($diff)->__toString = $diffValue = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isTrue(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notTrue . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not true', $asserter)->once
				->mock($diff)
					->call('setExpected')->withArguments(true)->once
					->call('setActual')->withArguments(false)->once

				->exception(function() use ($asserter) { $asserter->isTrue; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notTrue . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not true', $asserter)->twice
				->mock($diff)
					->call('setExpected')->withArguments(true)->twice
					->call('setActual')->withArguments(false)->twice

				->exception(function() use ($asserter, & $failMessage) { $asserter->isTrue($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage . PHP_EOL . $diffValue)
				->mock($diff)
					->call('setExpected')->withArguments(true)->thrice
					->call('setActual')->withArguments(false)->thrice
		;
	}

	public function testIsFalse()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->isFalse(); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')

			->if($asserter->setWith(false))
			->then
				->object($asserter->isFalse())->isIdenticalTo($asserter)
				->object($asserter->isFalse)->isIdenticalTo($asserter)

			->if(
				$asserter
					->setWith(true)
					->setLocale($locale = new \mock\atoum\locale())
					->setDiff($diff = new \mock\atoum\tools\diffs\variable()),
				$this->calling($locale)->_ = $notFalse = uniqid(),
				$this->calling($diff)->__toString = $diffValue = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isFalse(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notFalse . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not false', $asserter)->once
				->mock($diff)
					->call('setExpected')->withArguments(false)->once
					->call('setActual')->withArguments(true)->once

				->exception(function() use ($asserter) { $asserter->isFalse; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notFalse . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not false', $asserter)->twice
				->mock($diff)
					->call('setExpected')->withArguments(false)->twice
					->call('setActual')->withArguments(true)->twice

				->exception(function() use ($asserter, & $failMessage) { $asserter->isFalse($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage . PHP_EOL . $diffValue)
				->mock($diff)
					->call('setExpected')->withArguments(false)->thrice
					->call('setActual')->withArguments(true)->thrice
		;
	}

	public function testSetWith()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->object($asserter->setWith(true))->isIdenticalTo($asserter)
				->boolean($asserter->getValue())->isTrue()
				->object($asserter->setWith(false))->isIdenticalTo($asserter)
				->boolean($asserter->getValue())->isFalse()

			->if(
				$this->calling($locale)->_ = $notBoolean = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notBoolean)
				->mock($locale)->call('_')->withArguments('%s is not a boolean', $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments($value)->once
		;
	}
}
