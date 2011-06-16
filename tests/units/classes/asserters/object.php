<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	\mageekguy\atoum,
	\mageekguy\atoum\asserter,
	\mageekguy\atoum\asserters
;

require_once(__DIR__ . '/../../runner.php');

class object extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('\mageekguy\atoum\asserters\variable')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\object($generator = new asserter\generator($this));

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
		$asserter = new asserters\object(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not an object'), $asserter->toString($value)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($test->getLocale()->_('%s is not an object'), $asserter->toString($value))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->string($asserter->getValue())->isEqualTo($value)
		;

		$this->assert
			->object($asserter->setWith($value = $this))->isIdenticalTo($asserter);
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->object($asserter->getValue())->isIdenticalTo($value)
		;

		$this->assert
			->object($asserter->setWith($value = uniqid(), false))->isIdenticalTo($asserter)
			->string($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testHasSize()
	{
		$asserter = new asserters\object(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->hasSize(rand(0, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')
		;

		$asserter->setWith($this);

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->hasSize(0); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s has not size %d'), $asserter, 0))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::hasSize()',
						'fail' => sprintf($test->getLocale()->_('%s has not size %d'), $asserter, 0)
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$this->assert
			->object($asserter->hasSize(sizeof($this)))->isIdenticalTo($asserter); $line = __LINE__
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
		;
	}

	public function testIsEmpty()
	{
		$asserter = new asserters\object(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->hasSize(rand(0, PHP_INT_MAX));
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\logic')
					->hasMessage('Object is undefined')
		;

		$asserter->setWith($this);

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->isEmpty(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s has size %d'), $asserter, sizeof($this)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::isEmpty()',
						'fail' => sprintf($test->getLocale()->_('%s has size %d'), $asserter, sizeof($this))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$asserter->setWith(new \arrayIterator());

		$score->reset();

		$this->assert
			->object($asserter->isEmpty())->isIdenticalTo($asserter); $line = __LINE__
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(0)
			->integer($score->getPassNumber())->isEqualTo(1)
		;
	}
}

?>
