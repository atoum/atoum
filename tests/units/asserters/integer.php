<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserters;

require_once(__DIR__ . '/../../../runners/autorunner.php');

/**
@isolation off
*/
class integer extends atoum\test
{
	public function test__construct()
	{
		$score = new atoum\score();
		$locale = new atoum\locale();

		$asserter = new asserters\integer($score, $locale);

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetWith()
	{
		$currentMethod = substr(__METHOD__, strrpos(__METHOD__, ':') + 1);

		$locale = new atoum\locale();
		$score = new atoum\score();

		$asserter = new asserters\integer($score, $locale);

		$exception = null;

		$variable = uniqid();

		try
		{
			$line = __LINE__; $asserter->setWith($variable);
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not an integer'), asserters\integer::toString($variable)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => $exception->getMessage()
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$exception = null;

		try
		{
			$line = __LINE__; $this->assert->object($asserter->setWith(rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($asserter);
		}
		catch (\exception $exception) {}

		$this->assert
			->variable($exception)->isNull()
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
		;
	}

	public function testIsEqualTo()
	{
		$currentMethod = substr(__METHOD__, strrpos(__METHOD__, ':') + 1);

		$locale = new atoum\locale();
		$score = new atoum\score();

		$asserter = new asserters\integer($score, new atoum\locale());

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
				->hasMessage(sprintf($locale->_('%s is not equal to %s'), $asserter, asserters\integer::toString(- $variable)))
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
