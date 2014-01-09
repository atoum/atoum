<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\diffs,
	mageekguy\atoum\asserters\variable as sut
;

require_once __DIR__ . '/../../runner.php';

class constant extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->if($this->newTestedInstance($generator = new asserter\generator()))
			->then
				->object($this->testedInstance->getLocale())->isIdenticalTo($generator->getLocale())
				->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse()
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
			->if($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isEqualTo(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($value = uniqid()))
			->then
				->object($asserter->isEqualTo($value))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->then
				->exception(function() use (& $line, $asserter, & $notEqualValue) { $line = __LINE__; $asserter->isEqualTo($notEqualValue = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->getTypeOf($notEqualValue)) . PHP_EOL . $diff->setExpected($notEqualValue)->setActual($asserter->getValue()))
			->if($asserter->setWith(1))
			->and($otherDiff = new diffs\variable())
			->then
				->object($asserter->isEqualTo(1))->isIdenticalTo($asserter)
				->exception(function() use (& $otherLine, $asserter, & $otherNotEqualValue, & $otherFailMessage) { $otherLine = __LINE__; $asserter->isEqualTo('1', $otherFailMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($otherFailMessage . PHP_EOL . $otherDiff->setExpected('1')->setActual($asserter->getValue()))
		;
	}

	public function testEqualTo()
	{
		$this
			->if($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->equalTo(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($value = uniqid()))
			->then
				->object($asserter->equalTo($value))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->then
				->exception(function() use (& $line, $asserter, & $notEqualValue) { $line = __LINE__; $asserter->equalTo($notEqualValue = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->getTypeOf($notEqualValue)) . PHP_EOL . $diff->setExpected($notEqualValue)->setActual($asserter->getValue()))
			->if($asserter->setWith(1))
			->and($otherDiff = new diffs\variable())
			->then
				->object($asserter->equalTo(1))->isIdenticalTo($asserter)
				->exception(function() use (& $otherLine, $asserter, & $otherNotEqualValue, & $otherFailMessage) { $otherLine = __LINE__; $asserter->equalTo('1', $otherFailMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($otherFailMessage . PHP_EOL . $otherDiff->setExpected('1')->setActual($asserter->getValue()))
		;
	}
}
