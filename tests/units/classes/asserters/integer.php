<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserters;

require_once(__DIR__ . '/../../runner.php');

/**
@isolation off
*/
class integer extends atoum\test
{
	public function test__construct()
	{
		$asserter = new asserters\integer($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetWith()
	{
		$currentMethod = substr(__METHOD__, strrpos(__METHOD__, ':') + 1);

		$asserter = new asserters\integer($score = new atoum\score(), $locale = new atoum\locale());

		$variable = uniqid();

		$this->assert
			->exception(function() use (& $line, $asserter, $variable) { $line = __LINE__; $asserter->setWith($variable); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not an integer'), $asserter->toString($variable)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($locale->_('%s is not an integer'), $asserter->toString($variable))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->string($asserter->getVariable())->isEqualTo($variable)
		;

		$variable = rand(- PHP_INT_MAX, PHP_INT_MAX);

		$this->assert
			->object($asserter->setWith($variable))->isIdenticalTo($asserter); $line = __LINE__
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->collection($score->getPassAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => null
					)
				)
			)
			->integer($asserter->getVariable())->isEqualTo($variable)
		;
	}

	public function testIsEqualTo()
	{
		$currentMethod = substr(__METHOD__, strrpos(__METHOD__, ':') + 1);

		$asserter = new asserters\integer($score = new atoum\score(), $locale = new atoum\locale());

		$variable = rand(1, PHP_INT_MAX);

		$setWithLine = __LINE__; $asserter->setWith($variable);

		$exception = null;

		try
		{
			$isEqualToLine1 = __LINE__; $this->assert->object($asserter->isEqualTo($variable))->isIdenticalTo($asserter);
		}
		catch (\exception $exception) {}

		$this->assert
			->variable($exception)->isNull()
			->integer($score->getPassNumber())->isEqualTo(2)
			->collection($score->getPassAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $setWithLine,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => null
					),
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $isEqualToLine1,
						'asserter' => get_class($asserter) . '::isEqualTo()',
						'fail' => null
					)
				)
			)
			->integer($score->getFailNumber())->isZero()
		;

		$exception = null;

		try
		{
			$isEqualToLine2 = __LINE__; $asserter->isEqualTo(- $variable);
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not equal to %s'), $asserter, $asserter->toString(- $variable)))
			->integer($score->getPassNumber())->isEqualTo(2)
			->collection($score->getPassAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $setWithLine,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => null
					),
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $isEqualToLine1,
						'asserter' => get_class($asserter) . '::isEqualTo()',
						'fail' => null
					)
				)
			)
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $isEqualToLine2,
						'asserter' => get_class($asserter) . '::isEqualTo()',
						'fail' => $exception->getMessage()
					)
				)
			)
		;
	}
}

?>
