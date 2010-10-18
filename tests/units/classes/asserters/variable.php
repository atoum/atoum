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
			->variable($asserter->variable)->isNull()
			->boolean(isset($asserter->variable))->isFalse()
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
				->hasMessage('Property \'' . $property . '\' is undefined in class \'' . get_class($asserter) . '\'')
		;

		$this->assert
			->variable($asserter->variable)->isNull()
		;

		$asserter->variable = ($variable = uniqid());

		$this->assert
			->string($asserter->variable)->isEqualTo($variable)
		;
	}

	public function test__set()
	{
		$asserter = new asserters\variable(new atoum\score(), new atoum\locale());

		$this->assert
			->exception(function() use ($asserter, & $property) {
					$asserter->{$property = uniqid()} = uniqid();
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Property \'' . $property . '\' is undefined in class \'' . get_class($asserter) . '\'')
		;

		$this->assert
			->boolean(isset($asserter->variable))->isFalse()
		;

		$asserter->variable = ($variable = uniqid());

		$this->assert
			->string($asserter->variable)->isIdenticalTo($variable)
			->boolean(isset($asserter->variable))->isTrue()
		;

		$asserter->variable = $this;

		$this->assert
			->object($asserter->variable)->isIdenticalTo($this)
			->boolean(isset($asserter->variable))->isTrue()
		;
	}

	public function test__unset()
	{
		$asserter = new asserters\variable(new atoum\score(), new atoum\locale());

		$this->assert
			->exception(function() use ($asserter, & $property) {
					unset($asserter->{$property = uniqid()});
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Property \'' . $property . '\' is undefined in class \'' . get_class($asserter) . '\'')
		;

		$asserter->variable = uniqid();

		$this->assert
			->variable($asserter->variable)->isNotNull()
			->boolean(isset($asserter->variable))->isTrue()
		;

		unset($asserter->variable);

		$this->assert
			->variable($asserter->variable)->isNull()
			->boolean(isset($asserter->variable))->isFalse()
		;
	}

	public function testIsEqualTo()
	{
		$asserter = new asserters\variable($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->boolean(isset($asserter->variable))->isFalse()
			->exception(function() use ($asserter) {
						$asserter->isEqualTo(rand(- PHP_INT_MAX, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Variable is undefined')
		;

		$asserter->variable = ($variable = uniqid());

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
						'fail' => sprintf($locale->_('%s is not equal to %s'), $asserter, $asserter->toString($notEqualVariable))
					)
				)
			)
		;

		$asserter->variable = 1;

		$this->assert
			->object($asserter->isEqualTo('1'))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$this->assert
			->exception(function() use ($asserter, $notEqualVariable, & $failMessage) { $asserter->isEqualTo($notEqualVariable, $failMessage = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}

	public function testIsNotEqualTo()
	{
		$asserter = new asserters\variable($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->boolean(isset($asserter->variable))->isFalse()
			->exception(function() use ($asserter) {
						$asserter->isNotEqualTo(rand(- PHP_INT_MAX, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Variable is undefined')
		;

		$asserter->variable = ($variable = uniqid());

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
			->boolean(isset($asserter->variable))->isFalse()
			->exception(function() use ($asserter) {
						$asserter->isEqualTo(rand(- PHP_INT_MAX, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Variable is undefined')
		;

		$asserter->variable = ($variable = rand(- PHP_INT_MAX, PHP_INT_MAX));

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
			->boolean(isset($asserter->variable))->isFalse()
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

		$asserter->variable = null;

		$this->assert
			->object($asserter->isNull())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->variable = '';

		$this->assert
			->exception(function() use ($asserter) { $asserter->isNull(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not null'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$asserter->variable = uniqid();

		$this->assert
			->exception(function() use ($asserter) { $asserter->isNull(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not null'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;

		$asserter->variable = 0;

		$this->assert
			->exception(function() use ($asserter) { $asserter->isNull(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not null'), $asserter))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(3)
		;

		$asserter->variable = false;

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
