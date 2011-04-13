<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	\mageekguy\atoum,
	\mageekguy\atoum\asserter,
	\mageekguy\atoum\asserters
;

require_once(__DIR__ . '/../../runner.php');

class castToString extends atoum\test
{
	public function test__construct()
	{
		$asserter = new asserters\castToString($generator = new asserter\generator($this));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($this->getScore())
			->object($asserter->getLocale())->isIdenticalTo($this->getLocale())
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getVariable())->isNull()
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\castToString(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use (& $line, $asserter, & $variable) { $line = __LINE__; $asserter->setWith($variable = rand(- PHP_INT_MAX, PHP_INT_MAX)); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not an object'), $asserter->toString($variable)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($test->getLocale()->_('%s is not an object'), $asserter->toString($variable))
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
			->object($asserter->setWith($object = new \exception, null, $charlist = "\010"))->isIdenticalTo($asserter)
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
		$asserter = new asserters\castToString(new asserter\generator($test = new self($score = new atoum\score())));

		$asserter->setWith($object = new \exception());

		$string = (string) $object;

		$this->assert
			->string((string) $asserter)->isEqualTo('string(' . strlen($string) . ') \'' . $string . '\'')
		;
	}
}

?>
