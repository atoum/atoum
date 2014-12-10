<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\tools,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\diffs
;

require_once __DIR__ . '/../../runner.php';

class variable extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->getGenerator())->isEqualTo(new atoum\asserter\generator())
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new atoum\tools\variable\analyzer())
				->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
				->object($this->testedInstance->getDiff())->isEqualTo(new diffs\variable())
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse

			->if($this->newTestedInstance($generator = new asserter\generator(), $analyzer = new tools\variable\analyzer(), $locale = new atoum\locale()))
			->then
				->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
				->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new atoum\tools\variable\analyzer())
				->object($this->testedInstance->getDiff())->isEqualTo(new diffs\variable())
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse
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
			->given($this->newTestedInstance($generator = new \mock\atoum\asserter\generator()))

			->if($this->calling($generator)->__get = $asserterInstance = new \mock\atoum\asserter())
			->then
				->object($this->testedInstance->{$asserterClass = uniqid()})->isIdenticalTo($asserterInstance)
				->mock($generator)->call('__get')->withArguments($asserterClass)->once
		;
	}

	public function testReset()
	{
		$this
			->given($this->newTestedInstance)

			->if($this->testedInstance->setWith(uniqid()))
			->then
				->object($this->testedInstance->reset())->isTestedInstance
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse
				->boolean($this->testedInstance->isSetByReference())->isFalse

			->if(
				$reference = uniqid(), // Mandatory because "Only variables should be passed by reference"
				$this->testedInstance->setByReferenceWith($reference)
			)
			->then
				->object($this->testedInstance->reset())->isTestedInstance
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse
				->boolean($this->testedInstance->isSetByReference())->isFalse
		;
	}

	public function testSetWith()
	{
		$this
			->given($this->newTestedInstance)

			->if($value = uniqid())
			->then
				->object($this->testedInstance->setWith($value))->isTestedInstance
				->variable($this->testedInstance->getValue())->isIdenticalTo($value)
				->boolean($this->testedInstance->isSetByReference())->isFalse
		;
	}

	public function testSetByReferenceWith()
	{
		$this
			->given($this->newTestedInstance)

			->if($value = uniqid())
			->then
				->object($this->testedInstance->setByReferenceWith($value))->isTestedInstance
				->variable($this->testedInstance->getValue())->isIdenticalTo($value)
				->boolean($this->testedInstance->isSetByReference())->isTrue
		;
	}

	public function testIsSetByReference()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->boolean($this->testedInstance->isSetByReference())->isFalse

			->if($this->testedInstance->setWith(uniqid()))
			->then
				->boolean($this->testedInstance->isSetByReference())->isFalse

			->if(
				$value = uniqid(), // Mandatory because "Only variables should be passed by reference"
				$this->testedInstance->setByReferenceWith($value)
			)
			->then
				->boolean($this->testedInstance->isSetByReference())->isTrue
		;
	}

	public function testIsEqualTo()
	{
		$this
			->given(
				$asserter = $this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
					->setDiff($diff = new \mock\atoum\tools\diffs\variable())
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isEqualTo(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')

			->if($asserter->setWith($value = rand(- PHP_INT_MAX, PHP_INT_MAX)))
			->then
				->object($asserter->isEqualTo($value))->isIdenticalTo($asserter)
				->object($asserter->isEqualTo((string) $value))->isIdenticalTo($asserter)
				->object($asserter->{'=='}($value))->isIdenticalTo($asserter)

			->if(
				$this->calling($locale)->_ = $localizedMessage = uniqid(),
				$this->calling($diff)->__toString = $diffValue = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $notEqualValue) { $asserter->isEqualTo($notEqualValue = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not equal to %s', $asserter, $type)->once
				->mock($diff)
					->call('setExpected')->withArguments($notEqualValue)->once
					->call('setActual')->withArguments($value)->once
		;
	}

	public function testIsNotEqualTo()
	{
		$this
			->given(
				$asserter = $this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNotEqualTo(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')

			->if($asserter->setWith($value = uniqid()))
			->then
				->object($asserter->isNotEqualTo(uniqid()))->isIdenticalTo($asserter)
				->object($asserter->{'!='}(uniqid()))->isIdenticalTo($asserter)

			->if(
				$this->calling($locale)->_ = $localizedMessage = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter, $value) { $asserter->isNotEqualTo($value); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is equal to %s', $asserter, $type)->once
		;
	}

	public function testIsIdenticalTo()
	{
		$this
			->given(
				$asserter = $this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
					->setDiff($diff = new \mock\atoum\tools\diffs\variable())
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isIdenticalTo(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')

			->if($asserter->setWith($value = rand(- PHP_INT_MAX, PHP_INT_MAX)))
			->then
				->object($asserter->isIdenticalTo($value))->isIdenticalTo($asserter)
				->object($asserter->{'==='}($value))->isIdenticalTo($asserter)

			->if(
				$this->calling($locale)->_ = $localizedMessage = uniqid(),
				$this->calling($diff)->__toString = $diffValue = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter, $value, & $notIdenticalValue) { $asserter->isIdenticalTo($notIdenticalValue = (string) $value); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not identical to %s', $asserter, $type)->once
				->mock($diff)
					->call('setExpected')->withArguments($notIdenticalValue)->once
					->call('setActual')->withArguments($value)->once

				->exception(function() use ($asserter, $value, & $notIdenticalValue) { $asserter->isIdenticalTo($notIdenticalValue = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not identical to %s', $asserter, $type)->twice
				->mock($diff)
					->call('setExpected')->withArguments($notIdenticalValue)->once
					->call('setActual')->withArguments($value)->twice
		;
	}

	public function testIsNotIdenticalTo()
	{
		$this
			->given(
				$asserter = $this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNotIdenticalTo(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')

			->if($asserter->setWith($value = rand(- PHP_INT_MAX, PHP_INT_MAX)))
			->then
				->object($asserter->isNotIdenticalTo((string) $value))->isIdenticalTo($asserter)
				->object($asserter->isNotIdenticalTo(uniqid()))->isIdenticalTo($asserter)
				->object($asserter->{'!=='}(uniqid()))->isIdenticalTo($asserter)

			->if(
				$this->calling($locale)->_ = $localizedMessage = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter, $value) { $asserter->isNotIdenticalTo($value); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is identical to %s', $asserter, $type)->once
		;
	}

	public function testIsNull()
	{
		$this
			->given(
				$asserter = $this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNull(); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
				->exception(function() use ($asserter) { $asserter->isNull; })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')

			->if($asserter->setWith(null))
			->then
				->object($asserter->isNull())->isIdenticalTo($asserter)
				->object($asserter->isNull)->isIdenticalTo($asserter)

			->if(
				$asserter->setWith(uniqid()),
				$this->calling($locale)->_ = $localizedMessage = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNull(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is not null', $asserter)->once
				->exception(function() use ($asserter, & $failMessage) { $asserter->isNull($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
				->exception(function() use ($asserter) { $asserter->isNull; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is not null', $asserter)->twice

			->if(
				$asserter->setWith(''),
				$this->calling($locale)->_ = $localizedMessage = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNull(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is not null', $asserter)->thrice()
				->exception(function() use ($asserter, & $failMessage) { $asserter->isNull($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
				->exception(function() use ($asserter) { $asserter->isNull; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is not null', $asserter)->exactly(4)

			->if(
				$asserter->setWith(0),
				$this->calling($locale)->_ = $localizedMessage = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNull(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is not null', $asserter)->exactly(5)
				->exception(function() use ($asserter, & $failMessage) { $asserter->isNull($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
				->exception(function() use ($asserter) { $asserter->isNull; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is not null', $asserter)->exactly(6)

			->if(
				$asserter->setWith(false),
				$this->calling($locale)->_ = $localizedMessage = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNull(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is not null', $asserter)->exactly(7)
				->exception(function() use ($asserter, & $failMessage) { $asserter->isNull($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
				->exception(function() use ($asserter) { $asserter->isNull; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is not null', $asserter)->exactly(8)
		;
	}

	public function testIsNotNull()
	{
		$this
			->given(
				$asserter = $this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNotNull(); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
				->exception(function() use ($asserter) { $asserter->isNotNull; })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')

			->if($asserter->setWith(uniqid()))
			->then
				->object($asserter->isNotNull())->isIdenticalTo($asserter)
				->object($asserter->isNotNull)->isIdenticalTo($asserter)

			->if(
				$asserter->setWith(null),
				$this->calling($locale)->_ = $localizedMessage = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNotNull(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is null', $asserter)->once
				->exception(function() use ($asserter) { $asserter->isNotNull; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is null', $asserter)->twice
				->exception(function() use ($asserter, & $failMessage) { $asserter->isNotNull($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;
	}

	public function testIsReferenceTo()
	{
		$this
			->given(
				$asserter = $this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $value = uniqid(); $asserter->isReferenceTo($value); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')

			->if($asserter->setWith($value = uniqid()))
			->then
				->exception(function() use ($asserter) { $value = uniqid(); $asserter->isReferenceTo($value); })
					->isInstanceOf('logicException')
					->hasMessage('Value is not set by reference')

			->if(
				$asserter->setByReferenceWith($value),
				$reference = & $value,
				$this->calling($locale)->_ = $localizedMessage = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->object($asserter->isReferenceTo($reference))->isIdenticalTo($asserter)

				->exception(function() use ($asserter, & $notReference) { $notReference = uniqid(); $asserter->isReferenceTo($notReference); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is not a reference to %s', $asserter, $type)->once

			->if(
				$value = new \exception(),
				$asserter->setByReferenceWith($value)
			)
			->then
				->object($asserter->isReferenceTo($reference))->isIdenticalTo($asserter)

				->exception(function() use ($asserter, & $notReference) { $notReference = new \exception(); $asserter->isReferenceTo($notReference); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is not a reference to %s', $asserter, $type)->twice

				->exception(function() use ($asserter, & $notReference, & $failMessage) { $notReference = new \exception(); $asserter->isReferenceTo($notReference, $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;
	}

	public function testIsNotFalse()
	{
		$this
			->given(
				$asserter = $this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNotFalse(); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
				->exception(function() use ($asserter) { $asserter->isNotFalse; })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')

			->if($asserter->setWith(uniqid()))
			->then
				->object($asserter->isNotFalse())->isIdenticalTo($asserter)
				->object($asserter->isNotFalse)->isIdenticalTo($asserter)

			->if(
				$asserter->setWith(false),
				$this->calling($locale)->_ = $localizedMessage = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNotFalse(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is false', $asserter)->atLeastOnce()

				->exception(function() use ($asserter) { $asserter->isNotFalse; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is false', $asserter)->atLeastOnce()

				->exception(function() use ($asserter, & $failMessage) { $asserter->isNotFalse($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;
	}

	public function testIsNotTrue()
	{
		$this
			->given(
				$asserter = $this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNotTrue(); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
				->exception(function() use ($asserter) { $asserter->isNotTrue; })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')

			->if($asserter->setWith(uniqid()))
			->then
				->object($asserter->isNotTrue())->isIdenticalTo($asserter)
				->object($asserter->isNotTrue)->isIdenticalTo($asserter)

			->if(
				$asserter->setWith(true),
				$this->calling($locale)->_ = $localizedMessage = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNotTrue(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is true', $asserter)->atLeastOnce()

				->exception(function() use ($asserter) { $asserter->isNotTrue; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is true', $asserter)->atLeastOnce()

				->exception(function() use ($asserter, & $failMessage) { $asserter->isNotTrue($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;
	}

	public function testIsCallable()
	{
		$this
			->given(
				$asserter = $this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isCallable(); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
				->exception(function() use ($asserter) { $asserter->isCallable; })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')

			->if($asserter->setWith(function() {}))
			->then
				->object($asserter->isCallable())->isIdenticalTo($asserter)
				->object($asserter->isCallable)->isIdenticalTo($asserter)

			->if(
				$asserter->setWith(uniqid()),
				$this->calling($locale)->_ = $localizedMessage = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isCallable(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is not callable', $asserter)->atLeastOnce()

				->exception(function() use ($asserter) { $asserter->isCallable; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is not callable', $asserter)->atLeastOnce()

				->exception(function() use ($asserter, & $failMessage) { $asserter->isCallable($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;
	}

	public function testIsNotCallable()
	{
		$this
			->given(
				$asserter = $this->newTestedInstance
					->setLocale($locale = new \mock\atoum\locale())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNotCallable(); })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
				->exception(function() use ($asserter) { $asserter->isNotCallable; })
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')

			->if($asserter->setWith(uniqid()))
			->then
				->object($asserter->isNotCallable())->isIdenticalTo($asserter)
				->object($asserter->isNotCallable)->isIdenticalTo($asserter)

			->if(
				$asserter->setWith(function() {}),
				$this->calling($locale)->_ = $localizedMessage = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNotCallable(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is callable', $asserter)->atLeastOnce()

				->exception(function() use ($asserter) { $asserter->isNotCallable; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is callable', $asserter)->atLeastOnce()

				->exception(function() use ($asserter, & $message) { $asserter->isNotCallable($message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
		;
	}
}
