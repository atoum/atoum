<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	\mageekguy\atoum,
	\mageekguy\atoum\asserter,
	\mageekguy\atoum\asserters
;

require_once(__DIR__ . '/../../runner.php');

class mock extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('\mageekguy\atoum\asserter')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\mock($generator = new asserter\generator($this));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($this->getScore())
			->object($asserter->getLocale())->isIdenticalTo($this->getLocale())
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getMock())->isNull()
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$mockGenerator = new atoum\mock\generator();
		$mockGenerator->generate(__CLASS__);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$this->assert
			->exception(function() use ($asserter, & $mock) {
						$asserter->setWith($mock = uniqid());
					}
				)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not a mock'), $asserter->toString($mock)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isZero()
			->object($asserter->setWith($mock = new atoum\mock\mageekguy\atoum\tests\units\asserters\mock(null, null, $adapter)))->isIdenticalTo($asserter)
			->object($asserter->getMock())->isIdenticalTo($mock)
		;
	}

	public function testWasCalled()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->wasCalled();
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$mockGenerator = new atoum\mock\generator();
		$mockGenerator->generate(__CLASS__);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$asserter->setWith($mock = new atoum\mock\mageekguy\atoum\tests\units\asserters\mock(null, null, $adapter));

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->wasCalled(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not called'), get_class($mock)))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::wasCalled()',
						'fail' => sprintf($test->getLocale()->_('%s is not called'), get_class($mock))
					)
				)
			)
		;

		$score->reset();

		$this->assert
			->exception(function() use (& $line, $asserter, & $failMessage) { $line = __LINE__; $asserter->wasCalled($failMessage = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage)
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::wasCalled()',
						'fail' => $failMessage
					)
				)
			)
		;

		$mock->getMockController()->{__FUNCTION__} = function() {};
		$mock->{__FUNCTION__}();

		$this->assert
			->object($asserter->wasCalled())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;
	}

	public function testWasNotCalled()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->wasNotCalled();
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$mockGenerator = new atoum\mock\generator();
		$mockGenerator->generate(__CLASS__);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$asserter->setWith($mock = new atoum\mock\mageekguy\atoum\tests\units\asserters\mock(null, null, $adapter));

		$score->reset();

		$this->assert
			->object($asserter->wasNotCalled())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(0)
		;

		$mock->getMockController()->{__FUNCTION__} = function() {};
		$mock->{__FUNCTION__}();

		$this->assert
			->exception(function() use (& $line, $asserter, & $failMessage) { $line = __LINE__; $asserter->wasNotCalled($failMessage = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::wasNotCalled()',
						'fail' => $failMessage
					)
				)
			)
		;
	}

	public function testCall($argForTest = null)
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->call(uniqid());
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$mockGenerator = new atoum\mock\generator();
		$mockGenerator->generate(__CLASS__);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$mock = new atoum\mock\mageekguy\atoum\tests\units\asserters\mock(null, null, $adapter);
		$mock->getMockController()->{__FUNCTION__} = function() {};

		$asserter->setWith($mock);

		$score->reset();

		$method = __FUNCTION__;

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter, $method) { $line = __LINE__; $asserter->call($method); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is not called'), get_class($mock), $method))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::call()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is not called'), get_class($mock), $method)
					)
				)
			)
		;

		$mock->{__FUNCTION__}();

		$this->assert
			->object($asserter->call($method))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter, $method) { $line = __LINE__; $asserter->call($method, array(uniqid())); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is not called with this argument'), get_class($mock), $method))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::call()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is not called with this argument'), get_class($mock), $method)
					)
				)
			)
			->exception(function() use (& $otherLine, $asserter, $method) { $otherLine = __LINE__; $asserter->call($method, array(uniqid(), uniqid())); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is not called with these arguments'), get_class($mock),$method))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(2)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::call()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is not called with this argument'), get_class($mock), $method)
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::call()',
						'fail' => sprintf($test->getLocale()->_('method %s::%s() is not called with these arguments'), get_class($mock), $method)
					)
				)
			)
		;

		$mock->{__FUNCTION__}($arg = uniqid());

		$this->assert
			->object($asserter->call($method, array($arg)))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}

	public function testNotCall($argForTest = null)
	{
		$mockGenerator = new atoum\mock\generator();
		$mockGenerator->generate(__CLASS__);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$mock = new atoum\mock\mageekguy\atoum\tests\units\asserters\mock(null, null, $adapter);

		$method = __FUNCTION__;

		$mock->getMockController()->{$method} = function() {};

		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));
		$asserter->setWith($mock);

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->notCall($method))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->notCall($method, array()))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->notCall($method, array(uniqid())))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$mock->{$method}();

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use ($asserter, $method) {
					$asserter->notCall($method);
				}
			)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called'), get_class($mock), $method))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->notCall($method, array()))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->notCall($method, array(uniqid())))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$mock->{$method}($arg = uniqid());

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use ($asserter, $method) {
					$asserter->notCall($method);
				}
			)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called'), get_class($mock), $method))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->notCall($method, array()))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->notCall($method, array(uniqid())))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use ($asserter, $method, $arg) {
					$asserter->notCall($method, array($arg));
				}
			)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s::%s() is called with this argument'), get_class($mock), $method))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
		;
	}
}

?>
