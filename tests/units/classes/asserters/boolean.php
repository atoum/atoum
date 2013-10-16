<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\diffs,
	mageekguy\atoum\asserters\boolean as sut
;

require_once __DIR__ . '/../../runner.php';

class boolean extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserters\variable');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->variable($asserter->getValue())->isNull()
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testIsTrue()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isTrue(); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
			->if($asserter->setWith(true))
			->then
				->object($asserter->isTrue())->isIdenticalTo($asserter)
			->if($asserter->setWith(false))
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter) { $asserter->isTrue(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not true'), $asserter) . PHP_EOL . $diff->setExpected(true)->setActual(false))
			->if($asserter->setWith(true))
			->then
				->object($asserter->isTrue)->isIdenticalTo($asserter)
			->if($asserter->setWith(false))
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter) { $asserter->isTrue; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not true'), $asserter) . PHP_EOL . $diff->setExpected(true)->setActual(false))
		;
	}

	public function testIsFalse()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isFalse(); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
			->if($asserter->setWith(false))
			->then
				->object($asserter->isFalse())->isIdenticalTo($asserter)
			->if($asserter->setWith(true))
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter) { $asserter->isFalse(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not false'), $asserter) . PHP_EOL . $diff->setExpected(false)->setActual(true))
			->if($asserter->setWith(false))
			->then
				->object($asserter->isFalse)->isIdenticalTo($asserter)
			->if($asserter->setWith(true))
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter) { $asserter->isFalse; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not false'), $asserter) . PHP_EOL . $diff->setExpected(false)->setActual(true))
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->assert('Set the asserter with something else than a boolean throw an exception')
					->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s is not a boolean'), $asserter->getTypeOf($value)))
				->assert('The asserter was returned when it set with a boolean')
					->string($asserter->getValue())->isEqualTo($value)
					->object($asserter->setWith(true))->isIdenticalTo($asserter)
					->boolean($asserter->getValue())->isTrue()
		;
	}
}
