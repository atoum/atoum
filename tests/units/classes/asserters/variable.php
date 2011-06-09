<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	\mageekguy\atoum,
	\mageekguy\atoum\asserter,
	\mageekguy\atoum\asserters,
	\mageekguy\atoum\tools\diffs
;

require_once(__DIR__ . '/../../runner.php');

class variable extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('\mageekguy\atoum\asserter')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\variable($generator = new asserter\generator($this));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($this->getScore())
			->object($asserter->getLocale())->isIdenticalTo($this->getLocale())
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getValue())->isNull()
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function test__get()
	{
		$asserter = new asserters\variable(new asserter\generator($this));

		$this->assert
			->exception(function() use ($asserter, & $property) {
						$asserter->{$property = uniqid()};
					}
				)
				->isInstanceOf('\logicException')
				->hasMessage('Asserter \'mageekguy\atoum\asserters\\' . $property . '\' does not exist')
			->variable($asserter->getValue())->isNull()
		;

		$asserter->setWith($value = uniqid());

		$this->assert
			->string($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testReset()
	{
		$asserter = new asserters\variable(new asserter\generator($this));

		$asserter->setWith(uniqid());

		$this->assert
			->variable($asserter->getValue())->isNotNull()
			->boolean($asserter->wasSet())->isTrue()
			->object($asserter->reset())->isIdenticalTo($asserter)
			->variable($asserter->getValue())->isNull()
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\variable(new asserter\generator($test = new self($score = new atoum\score())));

		$value = uniqid();

		$this->assert
			->boolean($asserter->isSetByReference())->isFalse()
			->object($asserter->setWith($value))->isIdenticalTo($asserter)
			->variable($asserter->getValue())->isIdenticalTo($value)
			->boolean($asserter->isSetByReference())->isFalse()
		;
	}

	public function testSetByReferenceWith()
	{
		$asserter = new asserters\variable(new asserter\generator($test = new self($score = new atoum\score())));

		$value = uniqid();

		$this->assert
			->boolean($asserter->isSetByReference())->isFalse()
			->object($asserter->setByReferenceWith($value))->isIdenticalTo($asserter)
			->variable($asserter->getValue())->isIdenticalTo($value)
			->boolean($asserter->isSetByReference())->isTrue()
		;
	}

	public function testIsSetByReference()
	{
		$asserter = new asserters\variable(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->isSetByReference())->isFalse()
		;

		$asserter->setWith(uniqid());

		$this->assert
			->boolean($asserter->isSetByReference())->isFalse()
		;

		$asserter->setWith(uniqid());

		$this->assert
			->boolean($asserter->isSetByReference())->isFalse()
		;

		$value = uniqid();

		$asserter->setByReferenceWith($value);

		$this->assert
			->boolean($asserter->isSetByReference())->isTrue()
		;
	}

	public function testIsEqualTo()
	{
		$asserter = new asserters\variable(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
						$asserter->isEqualTo(rand(- PHP_INT_MAX, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Value is undefined')
		;

		$asserter->setWith($value = uniqid());

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->isEqualTo($value))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$diff = new diffs\variable();

		$this->assert
			->exception(function() use (& $line, $asserter, & $notEqualValue) { $line = __LINE__; $asserter->isEqualTo($notEqualValue = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->toString($notEqualValue)) . PHP_EOL . $diff->setReference($notEqualValue)->setData($asserter->getValue()))
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
						'fail' => $failMessage = sprintf($test->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->toString($notEqualValue)) . PHP_EOL . $diff
					)
				)
			)
		;

		$asserter->setWith(1);

		$otherDiff = new diffs\variable();

		$this->assert
			->object($asserter->isEqualTo('1'))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$this->assert
			->exception(function() use (& $otherLine, $asserter, & $otherNotEqualValue, & $otherFailMessage) {
					$otherLine = __LINE__; $asserter->isEqualTo($otherNotEqualValue = uniqid(), $otherFailMessage = uniqid());
				}
			)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($otherFailMessage . PHP_EOL . $otherDiff->setReference($otherNotEqualValue)->setData($asserter->getValue()))
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(2)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::isEqualTo()',
						'fail' => $failMessage
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::isEqualTo()',
						'fail' => $otherFailMessage . PHP_EOL . $otherDiff
					)
				)
			)
		;
	}

	public function testIsNotEqualTo()
	{
		$asserter = new asserters\variable(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
						$asserter->isNotEqualTo(rand(- PHP_INT_MAX, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Value is undefined')
		;

		$asserter->setWith($value = uniqid());

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->isNotEqualTo(uniqid()))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$this->assert
			->exception(function() use ($asserter, $value) { $asserter->isNotEqualTo($value); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is equal to %s'), $asserter, $asserter->toString($value)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$this->assert
			->exception(function() use ($asserter, $value, & $failMessage) { $asserter->isNotEqualTo($value, $failMessage = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}

	public function testIsIdenticalTo()
	{
		$asserter = new asserters\variable(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
						$asserter->isIdenticalTo(rand(- PHP_INT_MAX, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Value is undefined')
		;

		$asserter->setWith($value = rand(- PHP_INT_MAX, PHP_INT_MAX));

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->isIdenticalTo($value))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$this->assert
			->exception(function() use ($asserter, & $notIdenticalValue, $value) { $asserter->isIdenticalTo($notIdenticalValue = (string) $value); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not identical to %s'), $asserter, $asserter->toString($notIdenticalValue)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$this->assert
			->exception(function() use ($asserter, $notIdenticalValue, & $failMessage) { $asserter->isIdenticalTo($notIdenticalValue, $failMessage = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}

	public function testIsNotIdenticalTo()
	{
		$asserter = new asserters\variable(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
						$asserter->isNotIdenticalTo(rand(- PHP_INT_MAX, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Value is undefined')
		;

		$asserter->setWith($value = rand(- PHP_INT_MAX, PHP_INT_MAX));

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->isNotIdenticalTo(uniqid()))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$this->assert
			->exception(function() use ($asserter, & $notIdenticalValue, $value) { $asserter->isNotIdenticalTo($value); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is identical to %s'), $asserter, $asserter->toString($value)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;
	}

	public function testIsNull()
	{
		$asserter = new asserters\variable(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
						$asserter->isNull(rand(- PHP_INT_MAX, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Value is undefined')
		;

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->setWith(null);

		$this->assert
			->object($asserter->isNull())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->setWith('');

		$this->assert
			->exception(function() use ($asserter) { $asserter->isNull(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not null'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$asserter->setWith(uniqid());

		$this->assert
			->exception(function() use ($asserter) { $asserter->isNull(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not null'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;

		$asserter->setWith(0);

		$this->assert
			->exception(function() use ($asserter) { $asserter->isNull(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not null'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(3)
		;

		$asserter->setWith(false);

		$this->assert
			->exception(function() use ($asserter) { $asserter->isNull(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not null'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(4)
		;
	}

	public function testIsNotNull()
	{
		$asserter = new asserters\variable(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
						$asserter->isNotNull(rand(- PHP_INT_MAX, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Value is undefined')
		;

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->setWith(uniqid());

		$this->assert
			->object($asserter->isNotNull())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->setWith(null);

		$this->assert
			->exception(function() use ($asserter) { $asserter->isNotNull(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is null'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;
	}

	public function testIsReferenceTo()
	{
		$asserter = new asserters\variable(new asserter\generator($test = new self($score = new atoum\score())));

		$value = uniqid();

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter, $value) {
						$asserter->isReferenceTo($value);
					}
				)
				->isInstanceOf('\logicException')
				->hasMessage('Value is undefined')
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->setWith($value);

		$this->assert
			->boolean($asserter->wasSet())->isTrue()
			->boolean($asserter->isSetByReference())->isFalse()
			->exception(function() use ($asserter, $value) {
						$asserter->isReferenceTo($value);
					}
				)
				->isInstanceOf('\logicException')
				->hasMessage('Value is not set by reference')
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->setByReferenceWith($value);

		$reference = & $value;

		$this->assert
			->boolean($asserter->wasSet())->isTrue()
			->boolean($asserter->isSetByReference())->isTrue()
			->object($asserter->isReferenceTo($reference))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$notReference = uniqid();

		$this->assert
			->exception(function() use ($asserter, $notReference) {
						$asserter->isReferenceTo($notReference);
					}
				)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not a reference to %s'), $asserter, $asserter->toString($notReference)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$value = new \exception();
		$reference = $value;

		$asserter->setByReferenceWith($value);

		$this->assert
			->boolean($asserter->wasSet())->isTrue()
			->boolean($asserter->isSetByReference())->isTrue()
			->object($asserter->isReferenceTo($reference))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$notReference = new \exception();

		$this->assert
			->exception(function() use ($asserter, $notReference) {
						$asserter->isReferenceTo($notReference);
					}
				)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not a reference to %s'), $asserter, $asserter->toString($notReference)))
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}
}

?>
