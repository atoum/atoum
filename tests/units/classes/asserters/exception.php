<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once(__DIR__ . '/../../runner.php');

class exception extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\asserter')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\exception($generator = new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($test->getLocale())
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getValue())->isNull()
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\exception(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = uniqid()); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($this->getLocale()->_('%s is not an exception'), $asserter->getTypeOf($value)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($this->getLocale()->_('%s is not an exception'), $asserter->getTypeOf($value))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->string($asserter->getValue())->isEqualTo($value)
		;

		$this->assert
			->object($asserter->setWith($value = new \exception()))->isIdenticalTo($asserter); $line = __LINE__
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->exception($asserter->getValue())->isIdenticalTo($value)
		;
	}

	public function testIsInstanceOf()
	{
		$asserter = new asserters\exception($generator = new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->hasSize(rand(0, PHP_INT_MAX));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Exception is undefined')
		;

		$asserter->setWith(new \exception());

		$this->assert
			->object($asserter->isInstanceOf('\Exception'))->isIdenticalTo($asserter)
			->object($asserter->isInstanceOf('Exception'))->isIdenticalTo($asserter)
			->object($asserter->isInstanceOf('\exception'))->isIdenticalTo($asserter)
			->object($asserter->isInstanceOf('exception'))->isIdenticalTo($asserter)
			->exception(function() use ($asserter) {
						$asserter->isInstanceOf(uniqid());
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Argument of mageekguy\atoum\asserters\exception::isInstanceOf() must be a \exception instance or an exception class name')
		;

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->isInstanceOf('mageekguy\atoum\exceptions\runtime'); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not an instance of mageekguy\atoum\exceptions\runtime'), $asserter))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::isInstanceOf()',
						'fail' => sprintf($test->getLocale()->_('%s is not an instance of mageekguy\atoum\exceptions\runtime'), $asserter)
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;
	}

	public function testHasCode()
	{
		$this->assert
			->exception(function() { throw new atoum\exceptions\runtime('An exception message to test!',33); })
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('An exception message to test!')
				->hasCode(33);
	}
}

?>
