<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\diffs,
	mageekguy\atoum\asserters\float as sut
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
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
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
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($value = (float) rand(1, PHP_INT_MAX)))
			->then
				->object($asserter->isEqualTo($value))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, & $notFloat) { $asserter->isEqualTo($notFloat = uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument of ' . get_class($asserter) . '::isEqualTo() must be a float')
			->if($diff = new diffs\variable())
			->and($diff->setExpected(- $value)->setActual($value))
			->then
				->exception(function() use ($asserter, $value) { $asserter->isEqualTo(- $value); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->getTypeOf(- $value)) . PHP_EOL . $diff)
		;
	}

	public function testIsGreaterThan()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($value = 1.2))
			->then
				->object($asserter->isGreaterThan(1.1))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, & $notFloat) { $asserter->isGreaterThan($notFloat = uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument of ' . get_class($asserter) . '::isGreaterThan() must be a float')
				->exception(function() use ($asserter, & $greaterValue) { $asserter->isGreaterThan($greaterValue = 1.3); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not greater than %s'), $asserter, $asserter->getTypeOf($greaterValue)))
		;
	}

	public function testIsLessThan()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($value = 1.2))
			->then
				->object($asserter->isLessThan(1.3))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, & $notFloat) { $asserter->isLessThan($notFloat = uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument of ' . get_class($asserter) . '::isLessThan() must be a float')
				->exception(function() use ($asserter, & $lessValue) { $asserter->isLessThan($lessValue = 1.1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not less than %s'), $asserter, $asserter->getTypeOf($lessValue)))
		;
	}

	public function testIsGreaterThanOrEqualTo()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($value = 1.2))
			->then
				->object($asserter->isGreaterThanOrEqualTo(1.1))->isIdenticalTo($asserter)
				->object($asserter->isGreaterThanOrEqualTo($value))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, & $notFloat) { $asserter->isGreaterThanOrEqualTo($notFloat = uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument of ' . get_class($asserter) . '::isGreaterThanOrEqualTo() must be a float')
				->exception(function() use ($asserter, & $greaterValue) { $asserter->isGreaterThanOrEqualTo($greaterValue = 1.3); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not greater than or equal to %s'), $asserter, $asserter->getTypeOf($greaterValue)))
		;
	}

	public function testIsLessThanOrEqualTo()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($value = 1.2))
			->then
				->object($asserter->isLessThanOrEqualTo(1.3))->isIdenticalTo($asserter)
				->object($asserter->isLessThanOrEqualTo($value))->isIdenticalTo($asserter)
				->exception(function() use ($asserter, & $notFloat) { $asserter->isLessThanOrEqualTo($notFloat = uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument of ' . get_class($asserter) . '::isLessThanOrEqualTo() must be a float')
				->exception(function() use ($asserter, & $lessValue) { $asserter->isLessThanOrEqualTo($lessValue = 1.1); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not less than or equal to %s'), $asserter, $asserter->getTypeOf($lessValue)))
		;
	}

	public function testIsZero()
	{
		$this
			->given($asserter = new sut($generator = new asserter\generator()))

			->if($asserter->setWith(0.0))
			->then
				->object($asserter->isZero())->isIdenticalTo($asserter)

			->if(
				$asserter->setWith($value = (float) rand(1, PHP_INT_MAX)),
				$diff = new diffs\variable(),
				$diff->setExpected(0.0)->setActual($value)
			)
			->then
				->exception(function() use ($asserter) { $asserter->isZero(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->getTypeOf(0.0)) . PHP_EOL . $diff)
		;
	}

	/**
	 * @dataProvider dataProviderNearlyEqualTo
	 */
	public function testIsNearlyEqualTo($value, $testValue, $epsilon, $pass)
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->and($asserter->setWith($value));

			if ($pass) {
				$this->object($asserter->isNearlyEqualTo($testValue, $epsilon))
					->isIdenticalTo($asserter);
			} else {
				$this->if($diff = new diffs\variable())
					->and($diff->setExpected($testValue)->setActual($value))
					->then
						->exception(function() use ($asserter, $testValue, $epsilon) { $asserter->isNearlyEqualTo($testValue, $epsilon); })
							->isInstanceOf('mageekguy\atoum\asserter\exception')
							->hasMessage(sprintf($generator->getLocale()->_('%s is not nearly equal to %s with epsilon %s'), $asserter, $asserter->getTypeOf($testValue), $epsilon) . PHP_EOL . $diff);
			}

		;
	}

	protected function dataProviderNearlyEqualTo()
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
			array((float) INF, (float) INF, 1, true),
		);
	}
}
