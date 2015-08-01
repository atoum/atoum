<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\diffs,
	mageekguy\atoum\tools\variable
;

require_once __DIR__ . '/../../runner.php';

class integer extends atoum\test
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

	public function testSetWith()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
				->setLocale($locale = new \mock\atoum\locale()),
			$this->calling($locale)->_ = $notAnInteger = uniqid()
		)
		->then
			->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage($notAnInteger)
			->mock($locale)->call('_')->withArguments('%s is not an integer', $asserter)->once
			->string($asserter->getValue())->isEqualTo($value)

			->object($asserter->setWith($value = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($asserter)
			->integer($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testIsEqualTo()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->isEqualTo(rand(- PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if($asserter->setWith($value = rand(1, PHP_INT_MAX)))
			->then
				->object($asserter->isEqualTo($value))->isIdenticalTo($asserter)
				->object($asserter->{'=='}($value))->isIdenticalTo($asserter)

			->if($this->testedInstance
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
					->setLocale($locale = new \mock\atoum\locale())
					->setDiff($diff = new \mock\atoum\tools\diffs\variable()),
				$this->calling($locale)->_ = $notEqual = uniqid(),
				$this->calling($diff)->__toString = $diffValue = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter, $value) { $asserter->isEqualTo(- $value); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notEqual . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not equal to %s', $asserter, $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments(- $value)->once
				->mock($diff)
					->call('setExpected')->withArguments(- $value)->once
					->call('setActual')->withArguments($value)->once

				->exception(function() use ($asserter, $value, & $failMessage) { $asserter->isEqualTo(- $value, $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage . PHP_EOL . $diffValue)
				->mock($diff)
					->call('setExpected')->withArguments(- $value)->twice
					->call('setActual')->withArguments($value)->twice
		;
	}

	public function testIsGreaterThan()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->isGreaterThan(rand(-PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if($asserter->setWith(PHP_INT_MAX))
			->then
				->object($asserter->isGreaterThan(0))->isIdenticalTo($asserter)
				->object($asserter->{'>'}(0))->isIdenticalTo($asserter)

			->if($asserter
					->setWith(- PHP_INT_MAX)
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
					->setLocale($locale = new \mock\atoum\locale())
					->setDiff($diff = new \mock\atoum\tools\diffs\variable()),
				$this->calling($locale)->_ = $notGreaterThan = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isGreaterThan(PHP_INT_MAX); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notGreaterThan)
				->mock($locale)->call('_')->withArguments('%s is not greater than %s', $asserter, $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments(PHP_INT_MAX)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->isGreaterThan(- PHP_INT_MAX, $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;
	}

	public function testIsGreaterThanOrEqualTo()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->isGreaterThanOrEqualTo(rand(-PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if($asserter->setWith(PHP_INT_MAX))
			->then
				->object($asserter->isGreaterThanOrEqualTo(0))->isIdenticalTo($asserter)
				->object($asserter->isGreaterThanOrEqualTo(PHP_INT_MAX))->isIdenticalTo($asserter)
				->object($asserter->{'>='}(PHP_INT_MAX))->isIdenticalTo($asserter)

			->if($asserter
					->setWith(- PHP_INT_MAX)
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
					->setLocale($locale = new \mock\atoum\locale())
					->setDiff($diff = new \mock\atoum\tools\diffs\variable()),
				$this->calling($locale)->_ = $notGreaterThanOrEqualTo = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isGreaterThanOrEqualTo(PHP_INT_MAX); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notGreaterThanOrEqualTo)
				->mock($locale)->call('_')->withArguments('%s is not greater than or equal to %s', $asserter, $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments(PHP_INT_MAX)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->isGreaterThanOrEqualTo(PHP_INT_MAX, $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;
	}

	public function testIsLessThan()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->isLessThan(rand(-PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if($asserter->setWith(0))
			->then
				->object($asserter->isLessThan(PHP_INT_MAX))->isIdenticalTo($asserter)
				->object($asserter->{'<'}(PHP_INT_MAX))->isIdenticalTo($asserter)

			->if($asserter
					->setWith(PHP_INT_MAX)
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
					->setLocale($locale = new \mock\atoum\locale())
					->setDiff($diff = new \mock\atoum\tools\diffs\variable()),
				$this->calling($locale)->_ = $notLessThan = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isLessThan(- PHP_INT_MAX); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notLessThan)
				->mock($locale)->call('_')->withArguments('%s is not less than %s', $asserter, $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments(- PHP_INT_MAX)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->isLessThan(PHP_INT_MAX, $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;

	}

	public function testIsLessThanOrEqualTo()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->isLessThanOrEqualTo(rand(-PHP_INT_MAX, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if($asserter->setWith(0))
			->then
				->object($asserter->isLessThanOrEqualTo(PHP_INT_MAX))->isIdenticalTo($asserter)
				->object($asserter->isLessThanOrEqualTo(0))->isIdenticalTo($asserter)
				->object($asserter->{'<='}(0))->isIdenticalTo($asserter)

			->if($asserter
					->setWith(PHP_INT_MAX)
					->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
					->setLocale($locale = new \mock\atoum\locale())
					->setDiff($diff = new \mock\atoum\tools\diffs\variable()),
				$this->calling($locale)->_ = $notLessThanOrEqualTo = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isLessThanOrEqualTo(- PHP_INT_MAX); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notLessThanOrEqualTo)
				->mock($locale)->call('_')->withArguments('%s is not less than or equal to %s', $asserter, $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments(- PHP_INT_MAX)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->isLessThanOrEqualTo(- PHP_INT_MAX, $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;

	}

	public function testIsZero()
	{
		$this
			->given($asserter = $this->newTestedInstance)

			->if($asserter->setWith(0))
			->then
				->object($asserter->isZero())->isIdenticalTo($asserter)
				->object($asserter->isZero)->isIdenticalTo($asserter)

			->if(
				$asserter
					->setWith($value = rand(1, PHP_INT_MAX))
					->setLocale($locale = new \mock\atoum\locale())
					->setDiff($diff = new \mock\atoum\tools\diffs\variable()),
				$this->calling($locale)->_ = $notZero = uniqid(),
				$this->calling($diff)->__toString = $diffValue = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isZero(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notZero . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not equal to %s', $asserter, 0)->once
				->mock($diff)
					->call('setExpected')->withArguments(0)->once
					->call('setActual')->withArguments($value)->once

				->exception(function() use ($asserter) { $asserter->isZero; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notZero . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not equal to %s', $asserter, 0)->twice
				->mock($diff)
					->call('setExpected')->withArguments(0)->twice
					->call('setActual')->withArguments($value)->twice

				->exception(function() use ($asserter, & $failMessage) { $asserter->isZero($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage . PHP_EOL . $diffValue)
				->mock($diff)
					->call('setExpected')->withArguments(0)->thrice
					->call('setActual')->withArguments($value)->thrice
		;
	}
}
