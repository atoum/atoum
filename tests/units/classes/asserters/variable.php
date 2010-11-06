<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserters;

require_once(__DIR__ . '/../../runner.php');

/** @isolation off */
class variable extends atoum\test
{
	public function test__construct()
	{
		$score = new atoum\score();
		$locale = new atoum\locale();

		$asserter = new asserters\variable($score, $locale);

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($locale)
			->variable($asserter->getVariable())->isNull()
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function test__get()
	{
		$asserter = new asserters\variable(new atoum\score(), new atoum\locale());

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
		$asserter = new asserters\variable(new atoum\score(), new atoum\locale());

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
		$asserter = new asserters\variable($score = new atoum\score(), $locale = new atoum\locale());

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
		$asserter = new asserters\variable($score = new atoum\score(), $locale = new atoum\locale());

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
		$asserter = new asserters\variable($score = new atoum\score(), $locale = new atoum\locale());

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
		$asserter = new asserters\variable($score = new atoum\score(), $locale = new atoum\locale());

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

		$this->assert
			->exception(function() use (& $line, $asserter, & $notEqualVariable) { $line = __LINE__; $asserter->isEqualTo($notEqualVariable = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not equal to %s'), $asserter, $asserter->toString($notEqualVariable)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => substr(__METHOD__, strrpos(__METHOD__, ':') + 1),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::isEqualTo()',
						'fail' => $failMessage = sprintf($locale->_('%s is not equal to %s'), $asserter, $asserter->toString($notEqualVariable))
					)
				)
			)
		;

		$asserter->setWith(1);

		$this->assert
			->object($asserter->isEqualTo('1'))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$this->assert
			->exception(function() use (& $otherLine, $asserter, & $otherNotEqualVariable, & $otherFailMessage) { $otherLine = __LINE__; $asserter->isEqualTo($otherNotEqualVariable = uniqid(), $otherFailMessage = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($otherFailMessage)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(2)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => substr(__METHOD__, strrpos(__METHOD__, ':') + 1),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::isEqualTo()',
						'fail' => $failMessage
					),
					array(
						'class' => __CLASS__,
						'method' => substr(__METHOD__, strrpos(__METHOD__, ':') + 1),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::isEqualTo()',
						'fail' => $otherFailMessage
					)
				)
			)
		;
	}

	public function testIsNotEqualTo()
	{
		$asserter = new asserters\variable($score = new atoum\score(), $locale = new atoum\locale());

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
				->hasMessage(sprintf($locale->_('%s is equal to %s'), $asserter, $asserter->toString($variable)))
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
		$asserter = new asserters\variable($score = new atoum\score(), $locale = new atoum\locale());

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
				->hasMessage(sprintf($locale->_('%s is not identical to %s'), $asserter, $asserter->toString($notIdenticalVariable)))
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
		$asserter = new asserters\variable($score = new atoum\score(), $locale = new atoum\locale());

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
				->hasMessage(sprintf($locale->_('%s is not null'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$asserter->setWith(uniqid());

		$this->assert
			->exception(function() use ($asserter) { $asserter->isNull(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not null'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;

		$asserter->setWith(0);

		$this->assert
			->exception(function() use ($asserter) { $asserter->isNull(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not null'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(3)
		;

		$asserter->setWith(false);

		$this->assert
			->exception(function() use ($asserter) { $asserter->isNull(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not null'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(4)
		;
	}

	public function testIsReferenceTo()
	{
		$asserter = new asserters\variable($score = new atoum\score(), $locale = new atoum\locale());

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
				->hasMessage(sprintf($locale->_('%s is not a reference to %s'), $asserter, $asserter->toString($notReference)))
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
				->hasMessage(sprintf($locale->_('%s is not a reference to %s'), $asserter, $asserter->toString($notReference)))
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}
}

?>
