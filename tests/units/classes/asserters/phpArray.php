<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once(__DIR__ . '/../../runner.php');

class phpArray extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\asserters\variable')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\phpArray($generator = new asserter\generator($this));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($this->getScore())
			->object($asserter->getLocale())->isIdenticalTo($this->getLocale())
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getValue())->isNull()
			->variable($asserter->getKey())->isNull()
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\phpArray(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = uniqid()); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not an array'), $asserter->getTypeOf($value)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($test->getLocale()->_('%s is not an array'), $asserter->getTypeOf($value))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->string($asserter->getValue())->isEqualTo($value)
		;

		$this->assert
			->object($asserter->setWith($value = array()))->isIdenticalTo($asserter); $line = __LINE__
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->array($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testAtKey()
	{
		$asserter = new asserters\phpArray(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
					$asserter->hasSize(rand(0, PHP_INT_MAX));
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Array is undefined')
		;

		$asserter->setWith(array());

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter, & $key) { $line = __LINE__; $asserter->atKey($key = rand(1, PHP_INT_MAX)); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s has no key %s'), $asserter, $asserter->getTypeOf($key)))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::atKey()',
						'fail' => $failMessage = sprintf($test->getLocale()->_('%s has no key %s'), $asserter, $asserter->getTypeOf($key))
					)
				)
			)
		;

		$asserter->setWith(array(uniqid(), uniqid(), $value = uniqid(), uniqid(), uniqid()));

		$score->reset();

		$this->assert
			->object($asserter->atKey(0))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(0)
			->object($asserter->atKey(1))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(0)
			->object($asserter->atKey(2))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(3)
			->integer($score->getFailNumber())->isEqualTo(0)
			->object($asserter->atKey(3))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(4)
			->integer($score->getFailNumber())->isEqualTo(0)
			->object($asserter->atKey(4))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(5)
			->integer($score->getFailNumber())->isEqualTo(0)
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->atKey(5); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s has no key %s'), $asserter, $asserter->getTypeOf(5)))
			->integer($score->getPassNumber())->isEqualTo(5)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::atKey()',
						'fail' => $failMessage = sprintf($test->getLocale()->_('%s has no key %s'), $asserter, $asserter->getTypeOf(5))
					)
				)
			)
		;
	}

	public function testHasSize()
	{
		$asserter = new asserters\phpArray(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
					$asserter->hasSize(rand(0, PHP_INT_MAX));
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Array is undefined')
		;

		$asserter->setWith(array());

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter, & $size) { $line = __LINE__; $asserter->hasSize($size = rand(1, PHP_INT_MAX)); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s has not size %d'), $asserter, $size))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::hasSize()',
						'fail' => $failMessage = sprintf($test->getLocale()->_('%s has not size %d'), $asserter, $size)
					)
				)
			)
		;

		$this->assert
			->object($asserter->hasSize(0))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;
	}

	public function testIsEmpty()
	{
		$asserter = new asserters\phpArray(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
					$asserter->isEmpty();
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Array is undefined')
		;

		$asserter->setWith(array(uniqid()));

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->isEmpty(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not empty'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::isEmpty()',
						'fail' => $failMessage = sprintf($test->getLocale()->_('%s is not empty'), $asserter)
					)
				)
			)
		;

		$asserter->setWith(array());

		$score->reset();

		$this->assert
			->object($asserter->isEmpty())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;
	}

	public function testIsNotEmpty()
	{
		$asserter = new asserters\phpArray(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
					$asserter->isNotEmpty();
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Array is undefined')
		;

		$asserter->setWith(array());

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->isNotEmpty(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is empty'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::isNotEmpty()',
						'fail' => $failMessage = sprintf($test->getLocale()->_('%s is empty'), $asserter)
					)
				)
			)
		;

		$asserter->setWith(array(uniqid()));

		$score->reset();

		$this->assert
			->object($asserter->isNotEmpty())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;
	}

	public function testContains()
	{
		$asserter = new asserters\phpArray(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
					$asserter->contains(uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Array is undefined')
		;

		$asserter->setWith(array(uniqid(), uniqid(), $value = uniqid(), uniqid(), uniqid()));

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter, & $notInArray) { $line = __LINE__; $asserter->contains($notInArray = uniqid()); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s does not contain %s'), $asserter, $asserter->getTypeOf($notInArray)))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::contains()',
						'fail' => $failMessage = sprintf($test->getLocale()->_('%s does not contain %s'), $asserter, $asserter->getTypeOf($notInArray))
					)
				)
			)
		;

		$this->assert
			->object($asserter->contains($value))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$asserter->atKey(2)->getScore()->reset();

		$this->assert
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(0)
			->object($asserter->contains($value))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(0)
			->exception(function() use (& $line, $asserter, & $notAtKey) { $line = __LINE__; $asserter->contains($notAtKey = uniqid()); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s does not contain %s at key %s'), $asserter, $asserter->getTypeOf($notAtKey), $asserter->getTypeOf(2)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::contains()',
						'fail' => $failMessage = sprintf($test->getLocale()->_('%s does not contain %s at key %s'), $asserter, $asserter->getTypeOf($notAtKey), $asserter->getTypeOf(2))
					)
				)
			)
		;
	}

	public function testNotContains()
	{
		$asserter = new asserters\phpArray(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
					$asserter->notContains(uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Array is undefined')
		;

		$asserter->setWith(array(uniqid(), uniqid(), $inArray = uniqid(), uniqid(), uniqid()));

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(0)
			->object($asserter->notContains(uniqid()))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(0)
			->exception(function() use (& $line, $asserter, $inArray) { $line = __LINE__; $asserter->notContains($inArray); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s contains %s'), $asserter, $asserter->getTypeOf($inArray)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::notContains()',
						'fail' => $failMessage = sprintf($test->getLocale()->_('%s contains %s'), $asserter, $asserter->getTypeOf($inArray))
					)
				)
			)
		;

		$asserter->atKey(2)->getScore()->reset();

		$this->assert
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(0)
			->object($asserter->notContains(uniqid()))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(0)
			->exception(function() use (& $line, $asserter, $inArray) { $line = __LINE__; $asserter->notContains($inArray); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s contains %s at key %s'), $asserter, $asserter->getTypeOf($inArray), $asserter->getTypeOf(2)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::notContains()',
						'fail' => $failMessage = sprintf($test->getLocale()->_('%s contains %s at key %s'), $asserter, $asserter->getTypeOf($inArray), $asserter->getTypeOf(2))
					)
				)
			)
		;
	}
}

?>
