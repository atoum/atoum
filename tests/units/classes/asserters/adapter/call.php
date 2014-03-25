<?php

namespace mageekguy\atoum\tests\units\asserters\adapter;

require __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum
;

class call extends atoum
{
	public function testClass()
	{
		$this->testedClass
			->isAbstract
			->extends('mageekguy\atoum\asserter')
		;
	}

	public function test__construct()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getLastAssertionFile())->isNull()
				->variable($this->testedInstance->getLastAssertionLine())->isNull()
		;
	}

	public function test__get()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->{rand(0, PHP_INT_MAX)}; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')

			->given($adapter = new \mock\atoum\test\adapter())
			->if($asserter->setWith($adapter))
			->then
				->exception(function() use ($asserter) { $asserter->{rand(0, PHP_INT_MAX)}; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Call is undefined')

			->if(
				$asserter
					->setCall($call = new \mock\atoum\test\adapter\call())
					->setLocale($locale = new \mock\atoum\locale()),
				$call->setFunction(uniqid()),
				$this->calling($adapter)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
				$this->calling($calls)->count = 0,
				$this->calling($call)->__toString = $callAsString = uniqid(),
				$this->calling($locale)->__ = $notCalled = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $callNumber) { $asserter->{$callNumber = rand(1, PHP_INT_MAX)}; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, $callNumber)->once

			->if(
				$this->calling($calls)->count = $count = rand(1, PHP_INT_MAX),
				$this->calling($adapter)->getCallsEqualTo = $callsEqualTo = new \mock\atoum\test\adapter\calls(),
				$this->calling($callsEqualTo)->count = $count,
				$this->calling($callsEqualTo)->__toString = $callsEqualToAsString = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->{0}; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notCalled . PHP_EOL . $callsEqualToAsString)
				->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 0)->once

				->object($this->testedInstance->{$count})->isTestedInstance
		;
	}

	public function testBefore()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->before($asserter1 = new \mock\atoum\asserters\adapter\call()))->isTestedInstance
				->array($this->testedInstance->getBefore())->isEqualTo(array($asserter1))
				->variable($this->testedInstance->getLastAssertionFile())->isNotNull()
				->variable($this->testedInstance->getLastAssertionLine())->isNotNull()

				->object($this->testedInstance->before(
							$asserter2 = new \mock\atoum\asserters\adapter\call(),
							$asserter3 = new \mock\atoum\asserters\adapter\call()
						)
					)->isTestedInstance
				->array($this->testedInstance->getBefore())->isEqualTo(array($asserter1, $asserter2, $asserter3))
				->variable($this->testedInstance->getLastAssertionFile())->isNotNull()
				->variable($this->testedInstance->getLastAssertionLine())->isNotNull()
		;
	}

	public function testAfter()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->after($asserter1 = new \mock\atoum\asserters\adapter\call()))->isTestedInstance
				->array($this->testedInstance->getAfter())->isEqualTo(array($asserter1))
				->variable($this->testedInstance->getLastAssertionFile())->isNotNull()
				->variable($this->testedInstance->getLastAssertionLine())->isNotNull()

				->object($this->testedInstance->after(
							$asserter2 = new \mock\atoum\asserters\adapter\call(),
							$asserter3 = new \mock\atoum\asserters\adapter\call()
						)
					)->isTestedInstance
				->array($this->testedInstance->getAfter())->isEqualTo(array($asserter1, $asserter2, $asserter3))
				->variable($this->testedInstance->getLastAssertionFile())->isNotNull()
				->variable($this->testedInstance->getLastAssertionLine())->isNotNull()
		;
	}
}
