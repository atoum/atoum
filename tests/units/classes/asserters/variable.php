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
			->variable($asserter->getVariable())->isNull()
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
			->variable($asserter->getVariable())->isNull()
		;

		$asserter->setWith($variable = uniqid());

		$this->assert
			->string($asserter->getVariable())->isEqualTo($variable)
		;
	}

	public function testReset()
	{
		$asserter = new asserters\variable(new asserter\generator($this));

		$asserter->setWith(uniqid());

		$this->assert
			->variable($asserter->variable)->isNotNull()
			->boolean($asserter->wasSet())->isTrue()
			->object($asserter->reset())->isIdenticalTo($asserter)
			->variable($asserter->getVariable())->isNull()
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\variable(new asserter\generator($test = new self($score = new atoum\score())));

		$variable = uniqid();

		$this->assert
			->boolean($asserter->isSetByReference())->isFalse()
			->object($asserter->setWith($variable))->isIdenticalTo($asserter)
			->variable($asserter->getVariable())->isIdenticalTo($variable)
			->boolean($asserter->isSetByReference())->isFalse()
		;
	}

	public function testSetByReferenceWith()
	{
		$asserter = new asserters\variable(new asserter\generator($test = new self($score = new atoum\score())));

		$variable = uniqid();

		$this->assert
			->boolean($asserter->isSetByReference())->isFalse()
			->object($asserter->setByReferenceWith($variable))->isIdenticalTo($asserter)
			->variable($asserter->getVariable())->isIdenticalTo($variable)
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

		$variable = uniqid();

		$asserter->setByReferenceWith($variable);

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
					->hasMessage('Variable is undefined')
		;

		$asserter->setWith($variable = uniqid());

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->isEqualTo($variable))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$diff = new diffs\variable();

		$this->assert
			->exception(function() use (& $line, $asserter, & $notEqualVariable) { $line = __LINE__; $asserter->isEqualTo($notEqualVariable = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->toString($notEqualVariable)) . PHP_EOL . $diff->setReference($notEqualVariable)->setData($asserter->getVariable()))
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
						'fail' => $failMessage = sprintf($test->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->toString($notEqualVariable)) . PHP_EOL . $diff
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
			->exception(function() use (& $otherLine, $asserter, & $otherNotEqualVariable, & $otherFailMessage) {
					$otherLine = __LINE__; $asserter->isEqualTo($otherNotEqualVariable = uniqid(), $otherFailMessage = uniqid());
				}
			)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($otherFailMessage . PHP_EOL . $otherDiff->setReference($otherNotEqualVariable)->setData($asserter->getVariable()))
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
					->hasMessage('Variable is undefined')
		;

		$asserter->setWith($variable = uniqid());

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->isNotEqualTo(uniqid()))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$this->assert
			->exception(function() use ($asserter, $variable) { $asserter->isNotEqualTo($variable); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is equal to %s'), $asserter, $asserter->toString($variable)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$this->assert
			->exception(function() use ($asserter, $variable, & $failMessage) { $asserter->isNotEqualTo($variable, $failMessage = uniqid()); })
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
						$asserter->isEqualTo(rand(- PHP_INT_MAX, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Variable is undefined')
		;

		$asserter->setWith($variable = rand(- PHP_INT_MAX, PHP_INT_MAX));

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->isIdenticalTo($variable))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$this->assert
			->exception(function() use ($asserter, & $notIdenticalVariable, $variable) { $asserter->isIdenticalTo($notIdenticalVariable = (string) $variable); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not identical to %s'), $asserter, $asserter->toString($notIdenticalVariable)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$this->assert
			->exception(function() use ($asserter, $notIdenticalVariable, & $failMessage) { $asserter->isIdenticalTo($notIdenticalVariable, $failMessage = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}

	public function testIsNull()
	{
		$asserter = new asserters\variable(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
						$asserter->isEqualTo(rand(- PHP_INT_MAX, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Variable is undefined')
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

	public function testIsReferenceTo()
	{
		$asserter = new asserters\variable(new asserter\generator($test = new self($score = new atoum\score())));

		$variable = uniqid();

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter, $variable) {
						$asserter->isReferenceTo($variable);
					}
				)
				->isInstanceOf('\logicException')
				->hasMessage('Variable is undefined')
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->setWith($variable);

		$this->assert
			->boolean($asserter->wasSet())->isTrue()
			->boolean($asserter->isSetByReference())->isFalse()
			->exception(function() use ($asserter, $variable) {
						$asserter->isReferenceTo($variable);
					}
				)
				->isInstanceOf('\logicException')
				->hasMessage('Variable is not set by reference')
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->setByReferenceWith($variable);

		$reference = & $variable;

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

		$variable = new \exception();
		$reference = $variable;

		$asserter->setByReferenceWith($variable);

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
