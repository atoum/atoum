<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class dateTime extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\asserters\object')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\dateTime($generator = new asserter\generator($this));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($this->getScore())
			->object($asserter->getLocale())->isIdenticalTo($this->getLocale())
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getValue())->isNull()
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\dateTime(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = uniqid()); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not an instance of \\dateTime'), $asserter->getTypeOf($value)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($test->getLocale()->_('%s is not an instance of \\dateTime'), $asserter->getTypeOf($value))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->string($asserter->getValue())->isEqualTo($value)
		;

		$this->assert
			->object($asserter->setWith($value = new \dateTime()))->isIdenticalTo($asserter);
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->object($asserter->getValue())->isIdenticalTo($value)
		;

		$this->assert
			->object($asserter->setWith($value = uniqid(), false))->isIdenticalTo($asserter)
			->string($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testHasTimezone()
	{
		$asserter = new asserters\dateTime(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->hasSize(rand(0, PHP_INT_MAX));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Instance of \\dateTime is undefined')
		;

		$asserter->setWith($dateTime = new \dateTime('now', $timezone = new \dateTimezone('Europe/Paris')));

		$score->reset();

		$this->assert
			->exception(function() use (& $line, & $requiredTimezone, $asserter) { $line = __LINE__; $asserter->hasTimezone($requiredTimezone = new \DateTimezone('Europe/London')); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('Timezone is %s instead of %s'), $timezone->getName(), $requiredTimezone->getName()))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::hasTimezone()',
						'fail' => sprintf($test->getLocale()->_('Timezone is %s instead of %s'), $timezone->getName(), $requiredTimezone->getName())
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$this->assert
			->object($asserter->hasTimezone($dateTime->getTimezone()))->isIdenticalTo($asserter);
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
		;
	}

	public function testIsInYear()
	{
		$asserter = new asserters\dateTime(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->isInYear(rand(0, PHP_INT_MAX));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Instance of \\dateTime is undefined')
		;

		$asserter->setWith($dateTime = new \dateTime('1976-10-06'));

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->isInYear(2011); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('Year is %s instead of %s'), 2011, 1976))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::isInYear()',
						'fail' => sprintf($test->getLocale()->_('Year is %s instead of %s'), 2011, 1976)
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->isInYear(76); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('Year is %s instead of %s'), 76, 1976))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::isInYear()',
						'fail' => sprintf($test->getLocale()->_('Year is %s instead of %s'), 76, 1976)
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$this->assert
			->object($asserter->isInYear(1976))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
		;
	}

	public function testIsInMonth()
	{
		$asserter = new asserters\dateTime(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->isInMonth(rand(0, PHP_INT_MAX));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Instance of \\dateTime is undefined')
		;

		$asserter->setWith($dateTime = new \dateTime('1976-10-06'));

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->isInMonth(1); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('Month is %s instead of %s'), 1, 10))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::isInMonth()',
						'fail' => sprintf($test->getLocale()->_('Month is %s instead of %s'), 1, 10)
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$this->assert
			->object($asserter->isInMonth(10))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
		;

		$asserter->setWith($dateTime = new \dateTime('1980-08-14'));

		$score->reset();

		$this->assert
			->object($asserter->isInMonth('08'))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isZero()
			->integer($score->getPassNumber())->isEqualTo(1)
			->object($asserter->isInMonth('8'))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isZero()
			->integer($score->getPassNumber())->isEqualTo(2)
			->object($asserter->isInMonth(8))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isZero()
			->integer($score->getPassNumber())->isEqualTo(3)
		;
	}

	public function testIsInDay()
	{
		$asserter = new asserters\dateTime(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->isInDay(rand(0, PHP_INT_MAX));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Instance of \\dateTime is undefined')
		;

		$asserter->setWith($dateTime = new \dateTime('1976-10-06'));

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->isInDay(1); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('Day is %s instead of %s'), 1, 6))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::isInDay()',
						'fail' => sprintf($test->getLocale()->_('Day is %s instead of %s'), 1, 6)
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$this->assert
			->object($asserter->isInDay('06'))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->object($asserter->isInDay('6'))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(2)
			->object($asserter->isInDay(6))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(3)
		;
	}

	public function testHasDate()
	{
		$asserter = new asserters\dateTime(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->hasDate(1976, 10, 6);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Instance of \\dateTime is undefined')
		;

		$asserter->setWith($dateTime = new \dateTime('1976-10-06'));

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->hasDate(1980, 8, 14); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('Date is %s instead of %s'), '1980-08-14', '1976-10-06'))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::hasDate()',
						'fail' => sprintf($test->getLocale()->_('Date is %s instead of %s'), '1980-08-14', '1976-10-06')
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$this->assert
			->object($asserter->hasDate(1976, 10, 6))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->object($asserter->hasDate('1976', '10', '6'))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(2)
			->object($asserter->hasDate('1976', '10', '06'))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(3)
		;

		$asserter->setWith($dateTime = new \dateTime('1980-08-14'));

		$score->reset();

		$this->assert
			->object($asserter->hasDate(1980, 8, 14))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isZero()
			->integer($score->getPassNumber())->isEqualTo(1)
			->object($asserter->hasDate('1980', '8', '14'))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isZero()
			->integer($score->getPassNumber())->isEqualTo(2)
			->object($asserter->hasDate('1980', '08', '14'))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isZero()
			->integer($score->getPassNumber())->isEqualTo(3)
		;
	}
}

?>
