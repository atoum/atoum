<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\tools\diffs
;

require_once __DIR__ . '/../../runner.php';

class variable extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new asserters\variable($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function test__get()
	{
		$this
			->if($asserter = new asserters\variable($generator = new asserter\generator()))
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
			->if($asserter = new asserters\variable($generator = new asserter\generator()))
			->and($asserter->setWith(uniqid()))
			->then
				->variable($asserter->getValue())->isNotNull()
				->boolean($asserter->wasSet())->isTrue()
				->object($asserter->reset())->isIdenticalTo($asserter)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new asserters\variable($generator = new asserter\generator()))
			->and($value = uniqid())
			->then
				->boolean($asserter->isSetByReference())->isFalse()
				->object($asserter->setWith($value))->isIdenticalTo($asserter)
				->variable($asserter->getValue())->isIdenticalTo($value)
				->boolean($asserter->isSetByReference())->isFalse()
		;
	}

	public function testSetByReferenceWith()
	{
		$this
			->if($asserter = new asserters\variable($generator = new asserter\generator()))
			->and($value = uniqid())
			->then
				->boolean($asserter->isSetByReference())->isFalse()
				->object($asserter->setByReferenceWith($value))->isIdenticalTo($asserter)
				->variable($asserter->getValue())->isIdenticalTo($value)
				->boolean($asserter->isSetByReference())->isTrue()
		;
	}

	public function testIsSetByReference()
	{
		$this
			->if($asserter = new asserters\variable($generator = new asserter\generator()))
			->then
				->boolean($asserter->isSetByReference())->isFalse()
			->if($asserter->setWith(uniqid()))
			->then
				->boolean($asserter->isSetByReference())->isFalse()
			->if($asserter->setWith(uniqid()))
			->then
				->boolean($asserter->isSetByReference())->isFalse()
			->if($value = uniqid())
			->and($asserter->setByReferenceWith($value))
			->then
				->boolean($asserter->isSetByReference())->isTrue()
		;
	}

	public function testIsEqualTo()
	{
		$this
			->if($asserter = new asserters\variable($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
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
					->hasMessage(sprintf($generator->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->getTypeOf($notEqualValue)) . PHP_EOL . $diff->setReference($notEqualValue)->setData($asserter->getValue()))
			->if($asserter->setWith(1))
			->and($otherDiff = new diffs\variable())
			->then
				->object($asserter->isEqualTo('1'))->isIdenticalTo($asserter)
				->exception(function() use (& $otherLine, $asserter, & $otherNotEqualValue, & $otherFailMessage) { $otherLine = __LINE__; $asserter->isEqualTo($otherNotEqualValue = uniqid(), $otherFailMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($otherFailMessage . PHP_EOL . $otherDiff->setReference($otherNotEqualValue)->setData($asserter->getValue()))
		;
	}

	public function testIsNotEqualTo()
	{
		$this
			->if($asserter = new asserters\variable($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->isNotEqualTo(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
				->if($asserter->setWith($value = uniqid()))
				->then
					->object($asserter->isNotEqualTo(uniqid()))->isIdenticalTo($asserter)
					->exception(function() use ($asserter, $value) { $asserter->isNotEqualTo($value); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($generator->getLocale()->_('%s is equal to %s'), $asserter, $asserter->getTypeOf($value)))
					->exception(function() use ($asserter, $value, & $failMessage) { $asserter->isNotEqualTo($value, $failMessage = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($failMessage)
		;
	}

	public function testIsIdenticalTo()
	{
		$this
			->if($asserter = new asserters\variable($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->isIdenticalTo(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($value = rand(- PHP_INT_MAX, PHP_INT_MAX)))
			->then
				->object($asserter->isIdenticalTo($value))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, & $notIdenticalValue, $value) { $asserter->isIdenticalTo($notIdenticalValue = (string) $value); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not identical to %s'), $asserter, $asserter->getTypeOf($notIdenticalValue)) . PHP_EOL . $diff->setReference($notIdenticalValue)->setData($asserter->getValue()))
				->exception(function() use ($asserter, $notIdenticalValue, & $failMessage) { $asserter->isIdenticalTo($notIdenticalValue, $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage . PHP_EOL . $diff->setReference($notIdenticalValue)->setData($asserter->getValue()))
		;
	}

	public function testIsNotIdenticalTo()
	{
		$this
			->if($asserter = new asserters\variable($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->isNotIdenticalTo(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($value = rand(- PHP_INT_MAX, PHP_INT_MAX)))
			->then
			->object($asserter->isNotIdenticalTo(uniqid()))->isIdenticalTo($asserter)
			->exception(function() use ($asserter, & $notIdenticalValue, $value) { $asserter->isNotIdenticalTo($value); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($generator->getLocale()->_('%s is identical to %s'), $asserter, $asserter->getTypeOf($value)))
		;
	}

	public function testIsNull()
	{
		$this
			->if($asserter = new asserters\variable($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->isNull(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
			->if($asserter->setWith(null))
			->then
				->object($asserter->isNull())->isIdenticalTo($asserter)
			->if($asserter->setWith(''))
			->then
				->exception(function() use ($asserter) { $asserter->isNull(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not null'), $asserter))
			->if($asserter->setWith(uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->isNull(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not null'), $asserter))
			->if($asserter->setWith(0))
			->then
				->exception(function() use ($asserter) { $asserter->isNull(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not null'), $asserter))
			->if($asserter->setWith(false))
			->then
				->exception(function() use ($asserter) { $asserter->isNull(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not null'), $asserter))
		;
	}

	public function testIsNotNull()
	{
		$this
			->if($asserter = new asserters\variable($generator = new asserter\generator()))
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter) { $asserter->isNotNull(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
			->if($asserter->setWith(uniqid()))
			->then
				->object($asserter->isNotNull())->isIdenticalTo($asserter)
			->if($asserter->setWith(null))
			->then
				->exception(function() use ($asserter) { $asserter->isNotNull(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is null'), $asserter))
		;
	}

	public function testIsReferenceTo()
	{
		$this
			->if($asserter = new asserters\variable($generator = new asserter\generator()))
			->and($value = uniqid())
			->then
				->boolean($asserter->wasSet())->isFalse()
				->exception(function() use ($asserter, $value) { $asserter->isReferenceTo($value); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
			->if($asserter->setWith($value))
			->then
				->boolean($asserter->isSetByReference())->isFalse()
				->exception(function() use ($asserter, $value) { $asserter->isReferenceTo($value); })
					->isInstanceOf('logicException')
					->hasMessage('Value is not set by reference')
			->if($asserter->setByReferenceWith($value))
			->and($reference = & $value)
			->then
				->boolean($asserter->wasSet())->isTrue()
				->boolean($asserter->isSetByReference())->isTrue()
				->object($asserter->isReferenceTo($reference))->isIdenticalTo($asserter)
			->if($notReference = uniqid())
			->then
				->exception(function() use ($asserter, $notReference) { $asserter->isReferenceTo($notReference); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not a reference to %s'), $asserter, $asserter->getTypeOf($notReference)))
			->if($value = new \exception())
			->and($reference = $value)
			->and($asserter->setByReferenceWith($value))
			->then
				->boolean($asserter->wasSet())->isTrue()
				->boolean($asserter->isSetByReference())->isTrue()
				->object($asserter->isReferenceTo($reference))->isIdenticalTo($asserter)
			->if($notReference = new \exception())
			->then
				->exception(function() use ($asserter, $notReference) { $asserter->isReferenceTo($notReference); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not a reference to %s'), $asserter, $asserter->getTypeOf($notReference)))
		;
	}
}
