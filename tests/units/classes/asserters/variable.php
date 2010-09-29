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
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\variable(new atoum\score(), new atoum\locale());

		$variable = uniqid();

		$this->assert
			->object($asserter->setWith($variable))->isIdenticalTo($asserter)
			->string($asserter->getVariable())->isEqualTo($variable)
			->object($asserter->setWith($this))->isIdenticalTo($asserter)
			->object($asserter->getVariable())->isIdenticalTo($this)
		;
	}

	public function testIsEqualTo()
	{
		$score = new atoum\score();
		$locale = new atoum\locale();

		$asserter = new asserters\variable($score, new atoum\locale());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->isEqualTo(rand(- PHP_INT_MAX, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Variable is undefined')
		;

		$variable = uniqid();

		$asserter->setWith($variable);

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
		;

		$this->assert
			->object($asserter->isEqualTo($variable))->isIdenticalTo($asserter); $line = __LINE__
		;

		$this->assert
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$notEqualVariable = uniqid();

		$this->assert
			->exception(function() use (& $line, $asserter, $notEqualVariable) { $line = __LINE__; $asserter->isEqualTo($notEqualVariable); })
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
						'fail' => sprintf($locale->_('%s is not equal to %s'), $asserter, $asserter->toString($notEqualVariable))
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

		$failMessage = uniqid();

		$this->assert
			->exception(function() use ($asserter, $notEqualVariable, $failMessage) { $asserter->isEqualTo($notEqualVariable, $failMessage); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}

	public function testIsNotEqualTo()
	{
		$score = new atoum\score();
		$locale = new atoum\locale();

		$asserter = new asserters\variable($score, $locale);

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->isNotEqualTo(rand(- PHP_INT_MAX, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Variable is undefined')
		;

		$variable = uniqid();

		$asserter->setWith($variable);

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

		$failMessage = uniqid();

		$this->assert
			->exception(function() use ($asserter, $variable, $failMessage) { $asserter->isNotEqualTo($variable, $failMessage); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}

	public function testIsIdenticalTo()
	{
		$score = new atoum\score();
		$locale = new atoum\locale();

		$asserter = new asserters\variable($score, $locale);

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->isEqualTo(rand(- PHP_INT_MAX, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Variable is undefined')
		;

		$variable = rand(- PHP_INT_MAX, PHP_INT_MAX);

		$asserter->setWith($variable);

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->isIdenticalTo($variable))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$notIdenticalVariable = (string) $variable;

		$this->assert
			->exception(function() use ($asserter, $notIdenticalVariable) { $asserter->isIdenticalTo($notIdenticalVariable); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not identical to %s'), $asserter, $asserter->toString($notIdenticalVariable)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$failMessage = uniqid();

		$this->assert
			->exception(function() use ($asserter, $notIdenticalVariable, $failMessage) { $asserter->isIdenticalTo($notIdenticalVariable, $failMessage); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}

	public function testIsNull()
	{
		$score = new atoum\score();
		$locale = new atoum\locale();

		$asserter = new asserters\variable($score, $locale);

		$this->assert
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
			->object($asserter->setWith(null)->isNull())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$variable = '';

		$asserter->setWith($variable);

		$this->assert
			->exception(function() use ($asserter) { $asserter->isNull(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not null'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$variable = uniqid();

		$asserter->setWith($variable);

		$this->assert
			->exception(function() use ($asserter) { $asserter->isNull(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not null'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;

		$variable = 0;

		$asserter->setWith($variable);

		$this->assert
			->exception(function() use ($asserter) { $asserter->isNull(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not null'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(3)
		;

		$variable = false;

		$asserter->setWith($variable);

		$this->assert
			->exception(function() use ($asserter) { $asserter->isNull(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not null'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(4)
		;
	}
}

?>
