<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\tools\diffs
;

require_once __DIR__ . '/../../runner.php';

class float extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserters\integer');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new asserters\float($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new asserters\float($generator = new asserter\generator()))
			->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = uniqid()); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($generator->getLocale()->_('%s is not a float'), $asserter->getTypeOf($value)))
			->string($asserter->getValue())->isEqualTo($value)
			->object($asserter->setWith($value = (float) rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($asserter)
			->float($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testIsEqualTo()
	{
		$this
			->if($asserter = new asserters\float($generator = new asserter\generator()))
			->and($asserter->setWith($value = (float) rand(1, PHP_INT_MAX)))
			->then
				->object($asserter->isEqualTo($value))->isIdenticalTo($asserter)
			->if($diff = new diffs\variable())
			->and($diff->setReference(- $value)->setData($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isEqualTo(- $value); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->getTypeOf(- $value)) . PHP_EOL . $diff)
		;
	}

	/**
	 * @dataProvider dataProviderNearlyEqualTo
	 */
	public function testIsNearlyEqualTo($value, $testValue, $epsilon, $pass)
	{
		$this
			->if($asserter = new asserters\float($generator = new asserter\generator()))
			->and($asserter->setWith($value));

			if ($pass) {
				$this->object($asserter->isNearlyEqualTo($testValue, $epsilon))
					->isIdenticalTo($asserter);
			} else {
				$this->if($diff = new diffs\variable())
					->and($diff->setReference($testValue)->setData($value))
					->then
						->exception(function() use ($asserter, $testValue, $epsilon) { $asserter->isNearlyEqualTo($testValue, $epsilon); })
							->isInstanceOf('mageekguy\atoum\asserter\exception')
							->hasMessage(sprintf($generator->getLocale()->_('%s is not nearly equal to %s with epsilon %s'), $asserter, $asserter->getTypeOf($testValue), $epsilon) . PHP_EOL . $diff);
			}

		;
	}

	public function dataProviderNearlyEqualTo()
	{
		return array(
			array((float) 100, (float) 100, 1, true),
			array((float) 100, (float) 101, pow(10, -2), true),
			array((float) 101, (float) 100, pow(10, -2), true),
			array((float) 100, (float) 101, pow(10, -3), false),
			array((float) 101, (float) 100, pow(10, -3), false),
			array((float) -10001, (float) -10000, pow(10, -5), false),
			array((float) -10001, (float) -10000, pow(10, -4), true),
			array((float) -1.0001, (float) -1, pow(10, -4), true),
			array((float) -1.0001, (float) -1, pow(10, -5), false),
			array((float) 0.0001, (float) -0.0001, pow(10, -4), true),
            array((float) INF, (float) -INF, 1, false),
		);
	}
}
