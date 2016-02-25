<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\diffs,
	mageekguy\atoum\tools\variable
;

require_once __DIR__ . '/../../runner.php';

class phpFloat extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserters\integer');
	}

	public function test__construct()
	{
		$this
			->if($this->newTestedInstance)
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
				$this->calling($locale)->_ = $notFloat = uniqid(),
				$this->calling($analyzer)->getTypeOf = $badType = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notFloat)
				->mock($locale)->call('_')->withArguments('%s is not a float', $badType)->once
				->mock($analyzer)->call('getTypeOf')->withArguments($value)->once
				->string($asserter->getValue())->isEqualTo($value)

				->object($asserter->setWith($value = (float) rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($asserter)
				->float($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testIsZero()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setDiff($diff = new \mock\atoum\tools\diffs\variable())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isZero(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

				->exception(function() use ($asserter) { $asserter->isZero; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

				->exception(function() use ($asserter) { $asserter->ISZerO; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if($asserter->setWith(0.0))
			->then
				->object($asserter->isZero())->isIdenticalTo($asserter)
				->object($asserter->isZero)->isIdenticalTo($asserter)

			->if(
				$this->calling($locale)->_ = $notZero = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid(),
				$this->calling($diff)->__toString = $diffValue = uniqid(),
				$asserter->setWith($value = (float) rand(1, PHP_INT_MAX))
			)
			->then
				->exception(function() use ($asserter) { $asserter->isZero(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notZero . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not equal to %s', $asserter, $type)->once
				->mock($analyzer)->call('getTypeOf')->withArguments(0.0)->once
				->mock($diff)
					->call('setExpected')->withArguments(0.0)->once
					->call('setActual')->withArguments($value)->once

				->exception(function() use ($asserter) { $asserter->isZero; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notZero . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not equal to %s', $asserter, $type)->twice
				->mock($analyzer)->call('getTypeOf')->withArguments(0.0)->twice
				->mock($diff)
					->call('setExpected')->withArguments(0.0)->twice
					->call('setActual')->withArguments($value)->twice

				->exception(function() use ($asserter, & $failMessage) { $asserter->isZero($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage . PHP_EOL . $diffValue)
		;
	}

	public function testIsNearlyEqualTo()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setDiff($diff = new \mock\atoum\tools\diffs\variable())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->then
				->exception(function() use ($asserter) { $asserter->isNearlyEqualTo(1.1, 0.1); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')

			->if(
				$asserter->setWith(100.0),
				$this->calling($diff)->__toString = $diffValue = uniqid(),
				$this->calling($locale)->_ = $notNearlyEqualTo = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->object($asserter->isNearlyEqualTo(100.0, 0.0))->isIdenticalTo($asserter)
				->object($asserter->isNearlyEqualTo(100.0, 0.1))->isIdenticalTo($asserter)
				->object($asserter->isNearlyEqualTo(100.05, 0.1))->isIdenticalTo($asserter)
				->object($asserter->isNearlyEqualTo(100.1, 0.1))->isIdenticalTo($asserter)
				->object($asserter->isNearlyEqualTo(99.95, 0.1))->isIdenticalTo($asserter)
				->object($asserter->isNearlyEqualTo(99.99, 0.1))->isIdenticalTo($asserter)
				->object($asserter->isNearlyEqualTo(100.0, 1))->isIdenticalTo($asserter)
				->object($asserter->isNearlyEqualTo(101.0, 0.01))->isIdenticalTo($asserter)

				->exception(function() use ($asserter, & $lessValue) { $asserter->isNearlyEqualTo(101.0, 0.001); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notNearlyEqualTo . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not nearly equal to %s with epsilon %s', $asserter, $type, 0.001)->once
				->mock($analyzer)->call('getTypeOf')->withArguments(101.0)->once
				->mock($diff)
					->call('setExpected')->withArguments(101.0)->once
					->call('setActual')->withArguments(100.0)->once

			->if($asserter->setWith(101.0))
			->then
				->object($asserter->isNearlyEqualTo(100.0, 0.01))->isIdenticalTo($asserter)

				->exception(function() use ($asserter, & $lessValue) { $asserter->isNearlyEqualTo(100.0, 0.001); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notNearlyEqualTo . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not nearly equal to %s with epsilon %s', $asserter, $type, 0.001)->twice
				->mock($analyzer)->call('getTypeOf')->withArguments(100.0)->once
				->mock($diff)
					->call('setExpected')->withArguments(100.0)->once
					->call('setActual')->withArguments(100.0)->once

			->if($asserter->setWith(- 10001.0))
			->then
				->object($asserter->isNearlyEqualTo(- 10000.0, 0.0001))->isIdenticalTo($asserter)

				->exception(function() use ($asserter, & $lessValue) { $asserter->isNearlyEqualTo(- 10000.0, 0.00001); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notNearlyEqualTo . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not nearly equal to %s with epsilon %s', $asserter, $type, 0.00001)->once
				->mock($analyzer)->call('getTypeOf')->withArguments(- 10000.0)->once
				->mock($diff)
					->call('setExpected')->withArguments(- 10000.0)->once
					->call('setActual')->withArguments(- 10001.0)->once

			->if($asserter->setWith(- 1.0001))
			->then
				->object($asserter->isNearlyEqualTo(- 1.0, 0.0001))->isIdenticalTo($asserter)

				->exception(function() use ($asserter, & $lessValue) { $asserter->isNearlyEqualTo(- 1.0, 0.00001); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notNearlyEqualTo . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not nearly equal to %s with epsilon %s', $asserter, $type, 0.00001)->twice
				->mock($analyzer)->call('getTypeOf')->withArguments(- 1.0)->once
				->mock($diff)
					->call('setExpected')->withArguments(- 1.0)->once
					->call('setActual')->withArguments(- 1.0001)->once

			->if($asserter->setWith(0.0001))
			->then
				->object($asserter->isNearlyEqualTo(- 0.0001, 0.0001))->isIdenticalTo($asserter)
			->if($asserter->setWith(0.))
			->then
				->object($asserter->isNearlyEqualTo(0))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, & $lessValue) { $asserter->isNearlyEqualTo(0.0001); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notNearlyEqualTo . PHP_EOL . $diffValue)
			->if($asserter->setWith(0.0001))
			->then
				->exception(function() use ($asserter, & $lessValue) { $asserter->isNearlyEqualTo(0); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notNearlyEqualTo . PHP_EOL . $diffValue)
		;
	}

	/** @php 5.4 */
	public function testIsNearlyEqualToWithINF()
	{
		$this
			->given($asserter = $this->newTestedInstance
				->setLocale($locale = new \mock\atoum\locale())
				->setDiff($diff = new \mock\atoum\tools\diffs\variable())
				->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
			)
			->if(
				$asserter->setWith(INF),
				$this->calling($diff)->__toString = $diffValue = uniqid(),
				$this->calling($locale)->_ = $notNearlyEqualTo = uniqid(),
				$this->calling($analyzer)->getTypeOf = $type = uniqid()
			)
			->then
				->object($asserter->isNearlyEqualTo(INF, 1))->isIdenticalTo($asserter)

				->exception(function() use ($asserter, & $lessValue) { $asserter->isNearlyEqualTo(- INF, 1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notNearlyEqualTo . PHP_EOL . $diffValue)
				->mock($locale)->call('_')->withArguments('%s is not nearly equal to %s with epsilon %s', $asserter, $type, 1)->once
				->mock($analyzer)->call('getTypeOf')->withArguments(- INF)->once
				->mock($diff)
					->call('setExpected')->withArguments(- INF)->once
					->call('setActual')->withArguments(INF)->once
		;
	}
}
