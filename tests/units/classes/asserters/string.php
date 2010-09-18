<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserters;

require_once(__DIR__ . '/../../runner.php');

/**
@isolation off
*/
class string extends atoum\test
{
	public function test__construct()
	{
		$score = new atoum\score();
		$locale = new atoum\locale();

		$asserter = new asserters\string($score, $locale);

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetWith()
	{
		$currentMethod = substr(__METHOD__, strrpos(__METHOD__, ':') + 1);

		$asserter = new asserters\string($score = new atoum\score(), $locale = new atoum\locale());

		$variable = rand(- PHP_INT_MAX, PHP_INT_MAX);

		$this->assert
			->exception(function() use (& $line, $asserter, $variable) { $line = __LINE__; $asserter->setWith($variable); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not a string'), $asserter->toString($variable)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($locale->_('%s is not a string'), $asserter->toString($variable))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->integer($asserter->getVariable())->isEqualTo($variable)
			->variable($asserter->getCharlist())->isNull()
		;

		$variable = uniqid();

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
			->string($asserter->getVariable())->isEqualTo($variable)
			->variable($asserter->getCharlist())->isNull()
		;

		$score->reset();

		$variable = uniqid();
		$charlist = "\010";

		$this->assert
			->object($asserter->setWith($variable, $charlist))->isIdenticalTo($asserter); $line = __LINE__
		;

		$this->assert
			->integer($score->getFailNumber())->isZero()
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
			->string($asserter->getVariable())->isEqualTo($variable)
			->string($asserter->getCharlist())->isEqualTo($charlist)
		;
	}

	public function testToString()
	{
		$asserter = new asserters\string(new atoum\score(), new atoum\locale());

		$variable = uniqid();

		$asserter->setWith($variable);

		$this->assert
			->string((string) $asserter)->isEqualTo('string(' . strlen($variable) . ') \'' . $variable . '\'')
		;

		$variable = "\010" . uniqid() . "\010";
		$charlist = "\010";

		$asserter->setWith($variable, $charlist);

		$this->assert
			->string((string) $asserter)->isEqualTo('string(' . strlen($variable) . ') \'' . addcslashes($variable, "\010") . '\'')
		;
	}
}

?>
