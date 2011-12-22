<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

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
						'dataSetKey' => null,
						'dataSetProvider' => null,
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
						'dataSetKey' => null,
						'dataSetProvider' => null,
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
		$asserter = new asserters\exception(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
						$asserter->hasCode(rand(- PHP_INT_MAX, PHP_INT_MAX));
					}
				)
					->isInstanceOf('logicException')
					->hasMessage('Exception is undefined')
		;

		$asserter->setWith(new atoum\exceptions\runtime(uniqid(), $code = rand(2, PHP_INT_MAX)));

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter, & $otherCode) { $line = __LINE__; $asserter->hasCode($otherCode = 1); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('code is %d instead of %d'), $code, $otherCode))
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'dataSetKey' => null,
						'dataSetProvider' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::hasCode()',
						'fail' => sprintf($test->getLocale()->_('code is %d instead of %d'), $code, $otherCode)
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->object($asserter->hasCode($code))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;
	}

	public function testHasMessage()
	{
		$asserter = new asserters\exception(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
						$asserter->hasMessage(uniqid());
					}
				)
					->isInstanceOf('logicException')
					->hasMessage('Exception is undefined')
		;

		$asserter->setWith(new atoum\exceptions\runtime($message = uniqid()));

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter, & $otherMessage) { $line = __LINE__; $asserter->hasMessage($otherMessage = uniqid()); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('message \'%s\' is not identical to \'%s\''), $message, $otherMessage))
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'dataSetKey' => null,
						'dataSetProvider' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::hasMessage()',
						'fail' => sprintf($test->getLocale()->_('message \'%s\' is not identical to \'%s\''), $message, $otherMessage)
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->object($asserter->hasMessage($message))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;
	}

	public function testHasNestedException()
	{
		$asserter = new asserters\exception(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
						$asserter->hasNestedException();
					}
				)
					->isInstanceOf('logicException')
					->hasMessage('Exception is undefined')
		;

		$asserter->setWith(new atoum\exceptions\runtime('', 0));

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->hasNestedException(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage($test->getLocale()->_('exception does not contain any nested exception'))
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'dataSetKey' => null,
						'dataSetProvider' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::hasNestedException()',
						'fail' => $test->getLocale()->_('exception does not contain any nested exception')
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->hasNestedException(new \exception()); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage($test->getLocale()->_('exception does not contain this nested exception'))
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'dataSetKey' => null,
						'dataSetProvider' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::hasNestedException()',
						'fail' => $test->getLocale()->_('exception does not contain any nested exception')
					),
					array(
						'case' => null,
						'dataSetKey' => null,
						'dataSetProvider' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::hasNestedException()',
						'fail' => $test->getLocale()->_('exception does not contain this nested exception')
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(2)
		;

		$asserter->setWith(new atoum\exceptions\runtime('', 0, $nestedException = new \exception()));

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->hasNestedException())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
			->object($asserter->hasNestedException($nestedException))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->hasNestedException(new \exception()); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage($test->getLocale()->_('exception does not contain this nested exception'))
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'dataSetKey' => null,
						'dataSetProvider' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::hasNestedException()',
						'fail' => $test->getLocale()->_('exception does not contain this nested exception')
					)
				)
			)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(1)
		;
	}
}

?>
