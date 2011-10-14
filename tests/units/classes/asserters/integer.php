<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\tools\diffs
;

require_once __DIR__ . '/../../runner.php';

class integer extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\asserters\variable')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\integer($generator = new asserter\generator($this));

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
		$asserter = new asserters\integer(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = uniqid()); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not an integer'), $asserter->getTypeOf($value)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($test->getLocale()->_('%s is not an integer'), $asserter->getTypeOf($value))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->string($asserter->getValue())->isEqualTo($value)
		;

		$this->assert
			->object($asserter->setWith($value = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testIsEqualTo()
	{
		$asserter = new asserters\integer(new asserter\generator($test = new self($score = new atoum\score())));

		$asserter->setWith($value = rand(1, PHP_INT_MAX));

		$score->reset();

		$this->assert
			->object($asserter->isEqualTo($value))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$diff = new diffs\variable();

		$diff->setReference(- $value)->setData($value);

		$this->assert
			->exception(function() use ($asserter, $value, & $line) {
						$line = __LINE__; $asserter->isEqualTo(- $value);
					}
			)
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->getTypeOf(- $value)) . PHP_EOL . $diff)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $line,
							'asserter' => get_class($asserter) . '::isEqualTo()',
							'fail' => sprintf($test->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->getTypeOf(- $value) . PHP_EOL . $diff)
						)
					)
				)
			;
	}

	public function testIsGreaterThan()
	{
		$asserter = new asserters\integer(new asserter\generator($test = new self($score = new atoum\score())));

		$asserter->setWith($value = rand(1, PHP_INT_MAX - 1));

		$score->reset();

		$this->assert
			->object($asserter->isGreaterThan(0))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$diff = new diffs\variable();

		$diff->setReference(PHP_INT_MAX)->setData($value);

		$this->assert
			->exception(function() use ($asserter, $value, & $line) {
				$line = __LINE__; $asserter->isGreaterThan(PHP_INT_MAX);
			})
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not greater than %s'), $asserter, $asserter->getTypeOf(PHP_INT_MAX)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
				array(
					'case' => null,
					'class' => __CLASS__,
					'method' => $test->getCurrentMethod(),
					'file' => __FILE__,
					'line' => $line,
					'asserter' => get_class($asserter) . '::isGreaterThan()',
					'fail' => sprintf($test->getLocale()->_('%s is not greater than %s'), $asserter, $asserter->getTypeOf(PHP_INT_MAX))
				)
			))
		;

		$score->reset();

		$diff = new diffs\variable();

		$diff->setReference($value)->setData($value);

		$this->assert
			->exception(function() use ($asserter, $value, & $line) {
				$line = __LINE__; $asserter->isGreaterThan($value);
			})
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not greater than %s'), $asserter, $asserter->getTypeOf($value)))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
				array(
					'case' => null,
					'class' => __CLASS__,
					'method' => $test->getCurrentMethod(),
					'file' => __FILE__,
					'line' => $line,
					'asserter' => get_class($asserter) . '::isGreaterThan()',
					'fail' => sprintf($test->getLocale()->_('%s is not greater than %s'), $asserter, $asserter->getTypeOf($value))
				)
			))
		;
	}

	public function testIsLowerThan()
	{
		$asserter = new asserters\integer(new asserter\generator($test = new self($score = new atoum\score())));

		$asserter->setWith($value = - rand(1, PHP_INT_MAX - 1));

		$score->reset();

		$this->assert
			->object($asserter->isLowerThan(0))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$diff = new diffs\variable();

		$diff->setReference(- PHP_INT_MAX)->setData($value);

		$this->assert
			->exception(function() use ($asserter, $value, & $line) {
				$line = __LINE__; $asserter->isLowerThan(- PHP_INT_MAX);
			})
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not lower than %s'), $asserter, $asserter->getTypeOf(- PHP_INT_MAX)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
				array(
					'case' => null,
					'class' => __CLASS__,
					'method' => $test->getCurrentMethod(),
					'file' => __FILE__,
					'line' => $line,
					'asserter' => get_class($asserter) . '::isLowerThan()',
					'fail' => sprintf($test->getLocale()->_('%s is not lower than %s'), $asserter, $asserter->getTypeOf(- PHP_INT_MAX))
				)
			))
		;

		$score->reset();

		$diff = new diffs\variable();

		$diff->setReference($value)->setData($value);

		$this->assert
			->exception(function() use ($asserter, $value, & $line) {
				$line = __LINE__; $asserter->isLowerThan($value);
			})
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not lower than %s'), $asserter, $asserter->getTypeOf($value)))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
				array(
					'case' => null,
					'class' => __CLASS__,
					'method' => $test->getCurrentMethod(),
					'file' => __FILE__,
					'line' => $line,
					'asserter' => get_class($asserter) . '::isLowerThan()',
					'fail' => sprintf($test->getLocale()->_('%s is not lower than %s'), $asserter, $asserter->getTypeOf($value))
				)
			))
		;
	}

	public function testIsGreaterThanOrEqualTo()
	{
		$asserter = new asserters\integer(new asserter\generator($test = new self($score = new atoum\score())));

		$asserter->setWith($value = rand(1, PHP_INT_MAX - 1));

		$score->reset();

		$this->assert
			->object($asserter->isGreaterThanOrEqualTo(0))->isIdenticalTo($asserter)
			->object($asserter->isGreaterThanOrEqualTo($value))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isZero()
		;

		$diff = new diffs\variable();

		$diff->setReference(PHP_INT_MAX)->setData($value);

		$this->assert
			->exception(function() use ($asserter, $value, & $line) {
				$line = __LINE__; $asserter->isGreaterThanOrEqualTo(PHP_INT_MAX);
			})
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not greater than or equal to %s'), $asserter, $asserter->getTypeOf(PHP_INT_MAX)))
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
				array(
					'case' => null,
					'class' => __CLASS__,
					'method' => $test->getCurrentMethod(),
					'file' => __FILE__,
					'line' => $line,
					'asserter' => get_class($asserter) . '::isGreaterThanOrEqualTo()',
					'fail' => sprintf($test->getLocale()->_('%s is not greater than or equal to %s'), $asserter, $asserter->getTypeOf(PHP_INT_MAX))
				)
			))
		;
	}

	public function testIsLowerThanOrEqualTo()
	{
		$asserter = new asserters\integer(new asserter\generator($test = new self($score = new atoum\score())));

		$asserter->setWith($value = - rand(1, PHP_INT_MAX - 1));

		$score->reset();

		$this->assert
			->object($asserter->isLowerThanOrEqualTo(0))->isIdenticalTo($asserter)
			->object($asserter->isLowerThanOrEqualTo($value))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isZero()
		;

		$diff = new diffs\variable();

		$diff->setReference(- PHP_INT_MAX)->setData($value);

		$this->assert
			->exception(function() use ($asserter, $value, & $line) {
				$line = __LINE__; $asserter->isLowerThanOrEqualTo(- PHP_INT_MAX);
			})
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not lower than or equal to %s'), $asserter, $asserter->getTypeOf(- PHP_INT_MAX)))
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
				array(
					'case' => null,
					'class' => __CLASS__,
					'method' => $test->getCurrentMethod(),
					'file' => __FILE__,
					'line' => $line,
					'asserter' => get_class($asserter) . '::isLowerThanOrEqualTo()',
					'fail' => sprintf($test->getLocale()->_('%s is not lower than or equal to %s'), $asserter, $asserter->getTypeOf(- PHP_INT_MAX))
				)
			))
		;
	}

}

?>
