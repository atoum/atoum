<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	\mageekguy\atoum,
	\mageekguy\atoum\asserter,
	\mageekguy\atoum\asserters,
	\mageekguy\atoum\tools\diffs
;

require_once(__DIR__ . '/../../runner.php');

class float extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('\mageekguy\atoum\asserters\integer')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\float($generator = new asserter\generator($this));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($this->getScore())
			->object($asserter->getLocale())->isIdenticalTo($this->getLocale())
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getValue())->isNull()
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\float(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not a float'), $asserter->getTypeOf($value)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($test->getLocale()->_('%s is not a float'), $asserter->getTypeOf($value))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->string($asserter->getValue())->isEqualTo($value)
		;

		$this->assert
			->object($asserter->setWith($value = (float) rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($asserter)
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->float($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testIsEqualTo()
	{
		$asserter = new asserters\float(new asserter\generator($test = new self($score = new atoum\score())));

		$asserter->setWith($value = (float) rand(1, PHP_INT_MAX));

		$score->reset();

		$this->assert
			->object($asserter->isEqualTo($value))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$diff = new diffs\variable();

		$diff->setReference(- $value)->setData($value);

		$this->assert
			->exception(function() use ($asserter, $value, & $line) {
						$line = __LINE__; $asserter->isEqualTo(- $value);
					}
			)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->getTypeOf(- $value)) . PHP_EOL . $diff)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $line,
							'asserter' => get_class($asserter) . '::isEqualTo()',
							'fail' => sprintf($test->getLocale()->_('%s is not equal to %s'), $asserter, $asserter->getTypeOf(- $value) . PHP_EOL . $diff)
						)
					)
				)
			;
	}
}

?>
