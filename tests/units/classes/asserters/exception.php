<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserter;
use \mageekguy\atoum\asserters;

require_once(__DIR__ . '/../../runner.php');

class exception extends atoum\test
{
	public function test__construct()
	{
		$asserter = new asserters\exception($score = new atoum\score(), $locale = new atoum\locale(), $generator = new asserter\generator($this));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($locale)
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getVariable())->isNull()
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$currentMethod = substr(__METHOD__, strrpos(__METHOD__, ':') + 1);

		$asserter = new asserters\exception($score = new atoum\score(), $locale = new atoum\locale(), new asserter\generator($this));

		$this->assert
			->exception(function() use (& $line, $asserter, & $variable) { $line = __LINE__; $asserter->setWith($variable = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not an exception'), $asserter->toString($variable)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($locale->_('%s is not an exception'), $asserter->toString($variable))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->string($asserter->getVariable())->isEqualTo($variable)
		;

		$this->assert
			->object($asserter->setWith($variable = new \exception()))->isIdenticalTo($asserter); $line = __LINE__
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->exception($asserter->getVariable())->isIdenticalTo($variable)
		;
	}

	public function testHasCode()
	{
		$this->assert
			->exception(function() { throw new atoum\exceptions\runtime('An exception message to test!',33); })
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('An exception message to test!')
				->hasCode(33);
	}
}

?>
