<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserters;

require_once(__DIR__ . '/../../runner.php');

class castToString extends atoum\test
{
	public function test__construct()
	{
		$score = new atoum\score();
		$locale = new atoum\locale();

		$asserter = new asserters\castToString($score, $locale);

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

		$asserter = new asserters\castToString($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->exception(function() use (& $line, $asserter, & $variable) { $line = __LINE__; $asserter->setWith($variable = rand(- PHP_INT_MAX, PHP_INT_MAX)); })
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
			->integer($asserter->getVariable())->isEqualTo($variable)
			->variable($asserter->getCharlist())->isNull()
		;

		$this->assert
			->object($asserter->setWith($object = new \exception()))->isIdenticalTo($asserter)
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->string($asserter->getVariable())->isEqualTo((string) $object)
			->variable($asserter->getCharlist())->isNull()
		;

		$score->reset();

		$this->assert
			->object($asserter->setWith($object = new \exception, $charlist = "\010"))->isIdenticalTo($asserter)
		;

		$this->assert
			->integer($score->getFailNumber())->isZero()
			->integer($score->getPassNumber())->isEqualTo(1)
			->string($asserter->getVariable())->isEqualTo((string) $object)
			->string($asserter->getCharlist())->isEqualTo($charlist)
		;
	}

	public function testToString()
	{
		$asserter = new asserters\castToString(new atoum\score(), new atoum\locale());

		$asserter->setWith($object = new \exception());

		$string = (string) $object;

		$this->assert
			->string((string) $asserter)->isEqualTo('string(' . strlen($string) . ') \'' . $string . '\'')
		;
	}
}

?>
