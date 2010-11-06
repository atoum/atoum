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
		$asserter = new asserters\string($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->object($asserter)->isInstanceOf('\mageekguy\atoum\asserters\variable')
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($locale)
			->variable($asserter->getVariable())->isNull()
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$currentMethod = substr(__METHOD__, strrpos(__METHOD__, ':') + 1);

		$asserter = new asserters\string($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->exception(function() use (& $line, $asserter, & $variable) { $line = __LINE__; $asserter->setWith($variable = rand(- PHP_INT_MAX, PHP_INT_MAX)); })
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

		$this->assert
			->object($asserter->setWith($variable = uniqid()))->isIdenticalTo($asserter)
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->string($asserter->getVariable())->isEqualTo($variable)
			->variable($asserter->getCharlist())->isNull()
		;

		$score->reset();

		$this->assert
			->object($asserter->setWith($variable = uniqid(), $charlist = "\010"))->isIdenticalTo($asserter)
		;

		$this->assert
			->integer($score->getFailNumber())->isZero()
			->integer($score->getPassNumber())->isEqualTo(1)
			->string($asserter->getVariable())->isEqualTo($variable)
			->string($asserter->getCharlist())->isEqualTo($charlist)
		;
	}

	public function testToString()
	{
		$asserter = new asserters\string(new atoum\score(), new atoum\locale());

		$asserter->setWith($variable = uniqid());

		$this->assert
			->string((string) $asserter)->isEqualTo('string(' . strlen($variable) . ') \'' . $variable . '\'')
		;

		$asserter->setWith($variable = "\010" . uniqid() . "\010", $charlist = "\010");

		$this->assert
			->string((string) $asserter)->isEqualTo('string(' . strlen($variable) . ') \'' . addcslashes($variable, "\010") . '\'')
		;
	}
}

?>
