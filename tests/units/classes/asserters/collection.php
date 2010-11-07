<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserters;

require_once(__DIR__ . '/../../runner.php');

/**
@isolation off
*/
class collection extends atoum\test
{
	public function test__construct()
	{
		$asserter = new asserters\collection($score = new atoum\score(), $locale = new atoum\locale());

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

		$asserter = new asserters\collection($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->exception(function() use (& $line, $asserter, & $variable) { $line = __LINE__; $asserter->setWith($variable = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not an array'), $asserter->toString($variable)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($locale->_('%s is not an array'), $asserter->toString($variable))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->string($asserter->getVariable())->isEqualTo($variable)
		;

		$this->assert
			->object($asserter->setWith($variable = array()))->isIdenticalTo($asserter); $line = __LINE__
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->collection($asserter->getVariable())->isEqualTo($variable)
		;
	}

	public function testContain()
	{
		$currentMethod = substr(__METHOD__, strrpos(__METHOD__, ':') + 1);

		$asserter = new asserters\collection($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
					$asserter->contain(uniqid());
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Variable is undefined')
		;

		$asserter->setWith(array(uniqid(), uniqid(), $variable = uniqid(), uniqid(), uniqid()));

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter, & $notInArray) { $line = __LINE__; $asserter->contain($notInArray = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s does not contain %s'), $asserter, $asserter->toString($notInArray)))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => substr(__METHOD__, strrpos(__METHOD__, ':') + 1),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::contain()',
						'fail' => $failMessage = sprintf($locale->_('%s does not contain %s'), $asserter, $asserter->toString($notInArray))
					)
				)
			)
		;

		$this->assert
			->object($asserter->contain($variable))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;
	}
}

?>
