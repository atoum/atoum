<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserters;

require_once(__DIR__ . '/../../runner.php');

class object extends atoum\test
{
	public function test__construct()
	{
		$asserter = new asserters\object($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($locale)
			->variable($asserter->getVariable())->isNull()
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$currentMethod = substr(__METHOD__, strrpos(__METHOD__, ':') + 1);

		$asserter = new asserters\object($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->exception(function() use (& $line, $asserter, & $variable) { $line = __LINE__; $asserter->setWith($variable = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not an object'), $asserter->toString($variable)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($locale->_('%s is not an object'), $asserter->toString($variable))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->string($asserter->getVariable())->isEqualTo($variable)
		;

		$this->assert
			->object($asserter->setWith($variable = $this))->isIdenticalTo($asserter); $line = __LINE__
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->object($asserter->getVariable())->isIdenticalTo($variable)
		;
	}

	public function testHasSize()
	{
		$currentMethod = substr(__METHOD__, strrpos(__METHOD__, ':') + 1);

		$asserter = new asserters\object($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->hasSize(rand(0, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Object is undefined')
		;

		$asserter->setWith($this);

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->hasSize(0); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s has not size %d'), $asserter, 0))
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::hasSize()',
						'fail' => sprintf($locale->_('%s has not size %d'), $asserter, 0)
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$this->assert
			->object($asserter->hasSize(sizeof($this)))->isIdenticalTo($asserter); $line = __LINE__
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
		;
	}

	public function testIsEmpty()
	{
		$currentMethod = substr(__METHOD__, strrpos(__METHOD__, ':') + 1);

		$asserter = new asserters\object($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->hasSize(rand(0, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Object is undefined')
		;

		$asserter->setWith($this);

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->isEmpty(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s has size %d'), $asserter, sizeof($this)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::isEmpty()',
						'fail' => sprintf($locale->_('%s has size %d'), $asserter, sizeof($this))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$asserter->setWith(new \arrayIterator());

		$score->reset();

		$this->assert
			->object($asserter->isEmpty())->isIdenticalTo($asserter); $line = __LINE__
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(0)
			->integer($score->getPassNumber())->isEqualTo(1)
		;
	}
}

?>
