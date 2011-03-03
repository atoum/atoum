<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserter;
use \mageekguy\atoum\asserters;
use \mageekguy\atoum\tools\diffs;

require_once(__DIR__ . '/../../runner.php');

class float extends atoum\test
{
	public function test__construct()
	{
		$asserter = new asserters\float($score = new atoum\score(), $locale = new atoum\locale(), $generator = new asserter\generator($this));

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

		$asserter = new asserters\float($score = new atoum\score(), $locale = new atoum\locale(), new asserter\generator($this));

		$this->assert
			->exception(function() use (& $line, $asserter, & $variable) { $line = __LINE__; $asserter->setWith($variable = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not a float'), $asserter->toString($variable)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($locale->_('%s is not a float'), $asserter->toString($variable))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->string($asserter->getVariable())->isEqualTo($variable)
		;

		$this->assert
			->object($asserter->setWith($variable = (float) rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($asserter)
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->float($asserter->getVariable())->isEqualTo($variable)
		;
	}

	public function testIsEqualTo()
	{
		$currentMethod = substr(__METHOD__, strrpos(__METHOD__, ':') + 1);

		$asserter = new asserters\float($score = new atoum\score(), $locale = new atoum\locale(), new asserter\generator($this));

		$asserter->setWith($variable = (float) rand(1, PHP_INT_MAX));

		$score->reset();

		$this->assert
			->object($asserter->isEqualTo($variable))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$diff = new diffs\variable();

		$diff->setReference(- $variable)->setData($variable);

		$this->assert
			->exception(function() use ($asserter, $variable, & $line) {
						$line = __LINE__; $asserter->isEqualTo(- $variable);
					}
			)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not equal to %s'), $asserter, $asserter->toString(- $variable)) . PHP_EOL . $diff)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'class' => __CLASS__,
							'method' => $currentMethod,
							'file' => __FILE__,
							'line' => $line,
							'asserter' => get_class($asserter) . '::isEqualTo()',
							'fail' => sprintf($locale->_('%s is not equal to %s'), $asserter, $asserter->toString(- $variable) . PHP_EOL . $diff)
						)
					)
				)
			;
	}
}

?>
