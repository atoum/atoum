<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\variable
;

require_once __DIR__ . '/../../runner.php';

class iterator extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserters\object');
	}

	public function test__construct()
	{
		$this
			->if($this->newTestedInstance())
			->then
				->object($this->testedInstance->getGenerator())->isEqualTo(new atoum\asserter\generator())
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new variable\analyzer())
				->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse()

			->if($this->newTestedInstance($generator = new asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
			->then
				->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
				->object($this->testedInstance->getAnalyzer())->isIdenticalTo($analyzer)
				->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)

			->if(
				$this->calling($locale)->_ = $notAnArray = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notAnArray)
				->mock($locale)->call('_')->withArguments('%s is not an object', $type)->once

				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = new \stdClass); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notAnArray)
				->mock($locale)->call('_')->withArguments('%s is not an object', $type)->once

				->object($asserter->setWith($value = new \mock\iterator()))->isIdenticalTo($asserter)
				->iterator($asserter->getValue())->isEqualTo($value)
				->boolean($asserter->isSetByReference())->isFalse()
		;
	}

	public function testHasSize()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')

			->if(
				$this->calling($locale)->_ = $badSize = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid(),
				$asserter->setWith(new \arrayIterator(array()))
			)
			->then
				->exception(function() use ($asserter, & $size) { $asserter->hasSize($size = rand(1, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($badSize)
				->mock($locale)->call('_')->withArguments('%s has size %d, expected size %d', $asserter, 0, $size)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->hasSize(rand(1, PHP_INT_MAX), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->object($asserter->hasSize(0))->isIdenticalTo($asserter)

			->if($asserter->setWith(new \arrayIterator(range(1, 5))))
			->then
				->object($asserter->hasSize(5))->isIdenticalTo($asserter)
		;
	}

	public function testIsEmpty()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isEmpty(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')

			->if(
				$this->calling($locale)->_ = $notEmpty = uniqid(),
				$asserter->setWith(new \arrayIterator(array(uniqid())))
			)
			->then
				->exception(function() use ($asserter) { $asserter->isEmpty(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notEmpty)
				->mock($locale)->call('_')->withArguments('%s is not empty', $asserter)->once

				->exception(function() use ($asserter) { $asserter->isEmpty; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notEmpty)
				->mock($locale)->call('_')->withArguments('%s is not empty', $asserter)->twice

				->exception(function() use ($asserter, & $failMessage) { $asserter->isEmpty($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($asserter->setWith(new \arrayIterator(array())))
			->then
				->object($asserter->isEmpty())->isIdenticalTo($asserter)
		;
	}

	public function testIsNotEmpty()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNotEmpty(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')

			->if(
				$this->calling($locale)->_ = $isEmpty = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid(),
				$asserter->setWith(new \arrayIterator(array()))
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNotEmpty(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($isEmpty)
				->mock($locale)->call('_')->withArguments('%s is empty', $asserter)->once

				->exception(function() use ($asserter) { $asserter->isNotEmpty; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($isEmpty)
				->mock($locale)->call('_')->withArguments('%s is empty', $asserter)->twice

				->exception(function() use ($asserter, & $failMessage) { $asserter->isNotEmpty($failMessage = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($failMessage)

				->if($asserter->setWith(new \arrayIterator(array(uniqid()))))
				->then
					->object($asserter->isNotEmpty())->isIdenticalTo($asserter)
		;
	}

	public function testSize()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->size; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')

			->if($asserter->setWith(new \arrayIterator(array())))
			->then
				->object($integer = $asserter->size)->isInstanceOf('mageekguy\atoum\asserters\integer')
				->integer($integer->getValue())->isEqualTo(0)

			->if($asserter->setWith(new \arrayIterator(array(uniqid(), uniqid()))))
			->then
				->object($integer = $asserter->size)->isInstanceOf('mageekguy\atoum\asserters\integer')
				->integer($integer->getValue())->isEqualTo(2)
		;
	}

	public function testIsEqualTo()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setDiff($diff = new \mock\atoum\tools\diffs\variable())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
				->setGenerator($generator = new \mock\atoum\asserter\generator())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isEqualTo(new \mock\iterator()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')

			->if($asserter->setWith(new \arrayIterator(array())))
			->then
				->object($asserter->isEqualTo(new \arrayIterator(array())))->isIdenticalTo($asserter)

			->if($asserter->setWith($iterator = new \arrayIterator(range(1, 5))))
			->then
				->object($asserter->isEqualTo($iterator))->isIdenticalTo($asserter)

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
				->mock($analyzer)->call('getTypeOf')->withArguments($notEqualValue)->once

				->object($asserter->isEqualTo($iterator))->isIdenticalTo($asserter)
		;
	}

	public function testIsNotEqualTo()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
				->setGenerator($generator = new \mock\atoum\asserter\generator())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNotEqualTo(new \mock\iterator()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')

			->if($asserter->setWith(new \arrayIterator(array())))
			->then
				->object($asserter->isNotEqualTo(new \arrayIterator(range(1, 2))))->isIdenticalTo($asserter)

			->if(
				$this->calling($locale)->_ = $localizedMessage = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNotEqualTo(new \arrayIterator(array())); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is equal to %s', $asserter, $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments(new \arrayIterator(array()))->once

			->if($asserter->setWith($iterator = new \arrayIterator(range(1, 5))))
			->then
				->object($asserter->isNotEqualTo(new \arrayIterator(array())))->isIdenticalTo($asserter)

				->exception(function() use ($asserter, $iterator) { $asserter->isNotEqualTo($iterator); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is equal to %s', $asserter, $type)->twice
				->mock($analyzer)->call('getTypeOf')->withArguments($iterator)->once
		;
	}

	public function testIsIdenticalTo()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setDiff($diff = new \mock\atoum\tools\diffs\variable())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
				->setGenerator($generator = new \mock\atoum\asserter\generator())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isIdenticalTo(new \mock\iterator()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')

			->if($asserter->setWith($iterator = new \mock\iterator()))
			->then
				->object($asserter->isIdenticalTo($iterator))->isIdenticalTo($asserter)

			->if(
				$this->calling($locale)->_ = $localizedMessage = uniqid(),
				$this->calling($diff)->__toString = $diffValue = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $notIdenticalValue) { $asserter->isIdenticalTo($notIdenticalValue = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not identical to %s', $asserter, $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments($notIdenticalValue)->once
		;
	}

	public function testIsNotIdenticalTo()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
				->setGenerator($generator = new \mock\atoum\asserter\generator())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNotIdenticalTo(new \mock\iterator()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')

			->if($asserter->setWith($iterator = new \mock\iterator()))
			->then
				->object($asserter->isNotIdenticalTo(new \mock\iterator()))->isIdenticalTo($asserter)

			->if(
				$this->calling($locale)->_ = $localizedMessage = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter, $iterator) { $asserter->isNotIdenticalTo($iterator); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($localizedMessage)
				->mock($locale)->call('_')->withArguments('%s is identical to %s', $asserter, $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments($iterator)->once
		;
	}
}
