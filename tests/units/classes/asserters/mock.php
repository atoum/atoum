<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once(__DIR__ . '/../../runner.php');

class dummy
{
	public function foo($arg) {}
	public function bar($arg) {}
}

class mock extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\asserter')
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
			->variable($asserter->getCall())->isNull()
			->array($asserter->getBeforeMethodCalls())->isEmpty()
			->array($asserter->getAfterMethodCalls())->isEmpty()
		;
	}

	public function testReset()
	{
		$this->mockGenerator
			->generate('mageekguy\atoum\score')
			->generate('mageekguy\atoum\mock\controller')
		;

		$mockController = new \mock\mageekguy\atoum\mock\controller();

		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->variable($asserter->getMock())->isNull()
			->object($asserter->reset())->isIdenticalTo($asserter)
			->variable($asserter->getMock())->isNull()
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\score());
		$mock->setMockController($mockController);

		$this->assert
			->object($asserter->getMock())->isIdenticalTo($mock)
			->object($asserter->reset())->isIdenticalTo($asserter)
			->object($asserter->getMock())->isIdenticalTo($mock)
			->mock($mockController)
				->call('resetCalls')
		;

	}

	public function testSetWith()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->mockGenerator
			->generate(__CLASS__)
		;

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$this->assert
			->exception(function() use ($asserter, & $mock) {
						$asserter->setWith($mock = uniqid());
					}
				)
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not a mock'), $asserter->getTypeOf($mock)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isZero()
			->object($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\mock(null, null, $adapter)))->isIdenticalTo($asserter)
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
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate(__CLASS__)
		;

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\mock(null, null, $adapter));

		$mock->getMockController()->resetCalls();

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->wasCalled(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not called'), get_class($mock)))
			->integer($score->getPassNumber())->isZero()
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
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage)
			->integer($score->getPassNumber())->isZero()
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
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate(__CLASS__)
		;

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\mock(null, null, $adapter));

		$mock->getMockController()->resetCalls();

		$score->reset();

		$this->assert
			->object($asserter->wasNotCalled())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$mock->getMockController()->{__FUNCTION__} = function() {};
		$mock->{__FUNCTION__}();

		$this->assert
			->exception(function() use (& $line, $asserter, & $failMessage) { $line = __LINE__; $asserter->wasNotCalled($failMessage = uniqid()); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
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

	public function testBeforeMethodCall()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->beforeMethodCall(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$this->assert
			->object($asserter->beforeMethodCall('foo'))->isEqualTo($beforeMethodCall = new asserters\mock\call\mock($asserter, $mock, 'foo'))
			->array($asserter->getBeforeMethodCalls())->isEqualTo(array($beforeMethodCall))
			->object($asserter->beforeMethodCall('bar'))->isEqualTo($otherBeforeMethodCall = new asserters\mock\call\mock($asserter, $mock, 'bar'))
			->array($asserter->getBeforeMethodCalls())->isEqualTo(array($beforeMethodCall, $otherBeforeMethodCall))
		;
	}

	public function testWithAnyMethodCallsBefore()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->array($asserter->getBeforeMethodCalls())->isEmpty()
			->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
			->array($asserter->getBeforeMethodCalls())->isEmpty()
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith(new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$asserter->beforeMethodCall(uniqid());

		$this->assert
			->array($asserter->getBeforeMethodCalls())->isNotEmpty()
			->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
			->array($asserter->getBeforeMethodCalls())->isEmpty()
		;

		$asserter
			->beforeMethodCall(uniqid())
			->beforeMethodCall(uniqid())
		;

		$this->assert
			->array($asserter->getBeforeMethodCalls())->isNotEmpty()
			->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
			->array($asserter->getBeforeMethodCalls())->isEmpty()
		;
	}

	public function testAfterMethodCall()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->afterMethodCall(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$this->assert
			->object($asserter->afterMethodCall('foo'))->isEqualTo($afterMethodCall = new asserters\mock\call\mock($asserter, $mock, 'foo'))
			->array($asserter->getAfterMethodCalls())->isEqualTo(array($afterMethodCall))
			->object($asserter->afterMethodCall('bar'))->isEqualTo($otherAfterMethodCall = new asserters\mock\call\mock($asserter, $mock, 'bar'))
			->array($asserter->getAfterMethodCalls())->isEqualTo(array($afterMethodCall, $otherAfterMethodCall))
		;
	}

	public function testWithAnyMethodCallsAfter()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->array($asserter->getAfterMethodCalls())->isEmpty()
			->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter)
			->array($asserter->getAfterMethodCalls())->isEmpty()
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$asserter->afterMethodCall($function = uniqid());

		$this->assert
			->array($asserter->getAfterMethodCalls())->isNotEmpty()
			->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter)
			->array($asserter->getAfterMethodCalls())->isEmpty()
		;

		$asserter
			->afterMethodCall($function1 = uniqid())
			->afterMethodCall($function2 = uniqid())
		;

		$this->assert
			->array($asserter->getAfterMethodCalls())->isNotEmpty()
			->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter)
			->array($asserter->getAfterMethodCalls())->isEmpty()
		;
	}

	public function testBeforeFunctionCall()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->beforeFunctionCall(uniqid(), new test\adapter());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$adapter = new test\adapter();

		$this->assert
			->object($asserter->beforeFunctionCall('foo', $adapter))->isEqualTo($beforeFunctionCall = new asserters\mock\call\adapter($asserter, $adapter, 'foo'))
			->array($asserter->getBeforeFunctionCalls())->isEqualTo(array($beforeFunctionCall))
			->object($asserter->beforeFunctionCall('bar', $adapter))->isEqualTo($otherBeforeFunctionCall = new asserters\mock\call\adapter($asserter, $adapter, 'bar'))
			->array($asserter->getBeforeFunctionCalls())->isEqualTo(array($beforeFunctionCall, $otherBeforeFunctionCall))
		;
	}

	public function testWithAnyFunctionCallsBefore()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->array($asserter->getBeforeFunctionCalls())->isEmpty()
			->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter)
			->array($asserter->getBeforeFunctionCalls())->isEmpty()
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$adapter = new test\adapter();

		$asserter->beforeFunctionCall($function = uniqid(), $adapter);

		$this->assert
			->array($asserter->getBeforeFunctionCalls())->isNotEmpty()
			->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter)
			->array($asserter->getBeforeFunctionCalls())->isEmpty()
		;

		$asserter
			->beforeFunctionCall($function1 = uniqid(), $adapter)
			->beforeFunctionCall($function2 = uniqid(), $adapter)
		;

		$this->assert
			->array($asserter->getBeforeFunctionCalls())->isNotEmpty()
			->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter)
			->array($asserter->getBeforeFunctionCalls())->isEmpty()
		;
	}

	public function testAfterFunctionCall()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->afterFunctionCall(uniqid(), new test\adapter());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$adapter = new test\adapter();

		$this->assert
			->object($asserter->afterFunctionCall('foo', $adapter))->isEqualTo($afterFunctionCall = new asserters\mock\call\adapter($asserter, $adapter, 'foo'))
			->array($asserter->getAfterFunctionCalls())->isEqualTo(array($afterFunctionCall))
			->object($asserter->afterFunctionCall('bar', $adapter))->isEqualTo($otherAfterFunctionCall = new asserters\mock\call\adapter($asserter, $adapter, 'bar'))
			->array($asserter->getAfterFunctionCalls())->isEqualTo(array($afterFunctionCall, $otherAfterFunctionCall))
		;
	}

	public function testWithAnyFunctionCallsAfter()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->array($asserter->getAfterFunctionCalls())->isEmpty()
			->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter)
			->array($asserter->getAfterFunctionCalls())->isEmpty()
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$adapter = new test\adapter();

		$asserter->afterFunctionCall($function = uniqid(), $adapter);

		$this->assert
			->array($asserter->getAfterFunctionCalls())->isNotEmpty()
			->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter)
			->array($asserter->getAfterFunctionCalls())->isEmpty()
		;

		$asserter
			->afterFunctionCall($function1 = uniqid(), $adapter)
			->afterFunctionCall($function2 = uniqid(), $adapter)
		;

		$this->assert
			->array($asserter->getAfterFunctionCalls())->isNotEmpty()
			->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter)
			->array($asserter->getAfterFunctionCalls())->isEmpty()
		;
	}

	public function testCall()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->call(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$this->assert
			->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter)
			->object($asserter->getCall())->isEqualTo(new php\call($function, null, $mock))
		;

		$asserter->withArguments();

		$this->assert
			->object($asserter->getCall())->isEqualTo(new php\call($function, array(), $mock))
			->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter)
			->object($asserter->getCall())->isEqualTo(new php\call($function, null, $mock))
		;
	}

	public function testWithArguments()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->withArguments(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->withArguments(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
		;

		$asserter->call($function = uniqid());

		$this->assert
			->object($asserter->withArguments())->isIdenticalTo($asserter)
			->object($asserter->getCall())->isEqualTo(new php\call($function, array(), $mock))
			->object($asserter->withArguments($arg1 = uniqid()))->isIdenticalTo($asserter)
			->object($asserter->getCall())->isEqualTo(new php\call($function, array($arg1), $mock))
			->object($asserter->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isIdenticalTo($asserter)
			->object($asserter->getCall())->isEqualTo(new php\call($function, array($arg1, $arg2), $mock))
		;
	}

	public function testWithAnyArguments()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->withArguments(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->withArguments(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
		;

		$asserter->call($function = uniqid());

		$this->assert
			->object($asserter->getCall())->isEqualTo(new php\call($function, null, $mock))
			->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
			->object($asserter->getCall())->isEqualTo(new php\call($function, null, $mock))
		;

		$asserter->withArguments($arg = uniqid());

		$this->assert
			->object($asserter->getCall())->isEqualTo(new php\call($function, array($arg), $mock))
			->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
			->object($asserter->getCall())->isEqualTo(new php\call($function, null, $mock))
		;
	}

	public function testOnce()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->once();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->once();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
		;

		$asserter->call('foo');

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall()))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall())
					)
				)
			)
		;

		$mock->foo($usedArg = uniqid());

		$this->assert
			->object($asserter->once())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$call = new php\call('foo', null, $mock);

		$mock->foo($otherUsedArg = uniqid());

		$this->assert
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->once(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 2 times instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($otherUsedArg)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall())
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 2 times instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($otherUsedArg))
					)
				)
			)
		;

		$mock->getMockController()->resetCalls();

		$score->reset();

		$asserter->withArguments($usedArg = uniqid());

		$mock->foo($usedArg);

		$this->assert
			->object($asserter->once())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->withArguments($arg = uniqid());

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
					)
				)
			)
		;

		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));
		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$score->reset();

		$asserter->beforeMethodCall('bar')->call('foo');

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall()))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall())
					)
				)
			)
		;

		$mock->foo(uniqid());

		$this->assert
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->once(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is not called'), current($asserter->getBeforeMethodCalls())))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(2)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall())
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is not called'), current($asserter->getBeforeMethodCalls()))
					)
				)
			)
		;

		$mock->getMockController()->resetCalls();

		$mock->bar(uniqid());
		$mock->foo(uniqid());

		$this->assert
			->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->once(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is not called before method %s'), $asserter->getCall(), current($asserter->getBeforeMethodCalls())))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(3)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall())
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is not called'), current($asserter->getBeforeMethodCalls()))
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $anotherLine,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is not called before method %s'), $asserter->getCall(), current($asserter->getBeforeMethodCalls()))
					)
				)
			)
		;

		$mock->getMockController()->resetCalls();

		$mock->foo(uniqid());
		$mock->bar(uniqid());

		$this->assert
			->object($asserter->once())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(3)
		;

		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));
		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$score->reset();

		$asserter->afterMethodCall('bar')->call('foo');

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall()))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall())
					)
				)
			)
		;

		$mock->foo(uniqid());

		$this->assert
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->once(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is not called'), current($asserter->getAfterMethodCalls())))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(2)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall())
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is not called'), current($asserter->getAfterMethodCalls()))
					)
				)
			)
		;

		$mock->getMockController()->resetCalls();

		$mock->foo(uniqid());
		$mock->bar(uniqid());

		$this->assert
			->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->once(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is not called after method %s'), $asserter->getCall(), current($asserter->getAfterMethodCalls())))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(3)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall())
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is not called'), current($asserter->getAfterMethodCalls()))
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $anotherLine,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is not called after method %s'), $asserter->getCall(), current($asserter->getAfterMethodCalls()))
					)
				)
			)
		;

		$mock->getMockController()->resetCalls();

		$mock->bar(uniqid());
		$mock->foo(uniqid());

		$this->assert
			->object($asserter->once())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(3)
		;

		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));
		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$score->reset();

		$asserter->beforeMethodCall('bar')->withArguments($arg = uniqid())->call('foo');

		$mock->foo(uniqid());

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is not called'), current($asserter->getBeforeMethodCalls())))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('method %s is not called'), current($asserter->getBeforeMethodCalls()))
					)
				)
			)
		;


	}

	public function testAtLeastOnce()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->atLeastOnce();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->atLeastOnce();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
		;

		$asserter->call('foo');

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->atLeastOnce(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 0 time'), $asserter->getCall()))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::atLeastOnce()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time'), $asserter->getCall())
					)
				)
			)
		;

		$mock->foo(uniqid());

		$this->assert
			->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$mock->foo(uniqid());

		$this->assert
			->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$mock->getMockController()->resetCalls();

		$score->reset();

		$asserter->withArguments($usedArg = uniqid());

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->atLeastOnce(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 0 time'), $asserter->getCall()))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::atLeastOnce()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time'), $asserter->getCall())
					)
				)
			)
		;

		$call = new php\call('foo', null, $mock);

		$mock->foo($usedArg);

		$this->assert
			->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$previousCall = $asserter->getCall();

		$asserter->withArguments($otherArg = uniqid());

		$this->assert
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->atLeastOnce(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 0 time'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::atLeastOnce()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time'), $previousCall)
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::atLeastOnce()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
					)
				)
			)
		;
	}

	public function testExactly()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->exactly(2);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->exactly(2);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
		;

		$asserter->call('foo');

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'),$asserter->getCall())
					)
				)
			)
		;

		$call = new php\call('foo', null, $mock);

		$mock->foo($usedArg = uniqid());

		$this->assert
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(2)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall())
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
					)
				)
			)
		;

		$mock->foo($otherUsedArg = uniqid());

		$this->assert
			->object($asserter->exactly(2))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;

		$mock->foo($anOtherUsedArg = uniqid());

		$this->assert
			->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($otherUsedArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($anOtherUsedArg)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(3)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall())
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $anotherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($otherUsedArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($anOtherUsedArg))
					)
				)
			)
		;

		$mock->getMockController()->resetCalls();

		$score->reset();

		$asserter->withArguments($arg = uniqid());

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall())
					)
				)
			)
		;

		$mock->foo($usedArg = uniqid());

		$this->assert
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(2)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall())
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
					)
				)
			)
		;

		$mock->foo($arg);

		$this->assert
			->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(3)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall())
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $anotherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg))
					)
				)
			)
		;

		$mock->foo($arg);

		$this->assert
			->object($asserter->exactly(2))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(3)
		;

		$mock->foo($arg);

		$this->assert
			->exception(function() use (& $anAnotherLine, $asserter) { $anAnotherLine = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)) . PHP_EOL . '[3] ' . $call->setArguments(array($arg)) . PHP_EOL . '[4] ' . $call->setArguments(array($arg)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(4)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall())
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $anotherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg))
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $anAnotherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)) . PHP_EOL . '[3] ' . $call->setArguments(array($arg)) . PHP_EOL . '[4] ' . $call->setArguments(array($arg))
					)
				)
			)
		;
	}

	public function testNever()
	{
		$asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->never();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Mock is undefined')
		;

		$this->mockGenerator
			->generate('mageekguy\atoum\tests\units\asserters\dummy')
		;

		$asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->never();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called method is undefined')
		;

		$asserter->call('foo');

		$score->reset();

		$this->assert
			->object($asserter->never())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$call = new php\call('foo', null, $mock);

		$mock->foo($usedArg = uniqid());

		$this->assert
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->never(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::never()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
					)
				)
			)
		;

		$mock->getMockController()->resetCalls();

		$score->reset();

		$asserter->withArguments($arg = uniqid());

		$this->assert
			->object($asserter->never())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$mock->foo($arg);

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->never(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::never()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg))
					)
				)
			)
		;

		$mock->foo($arg);

		$this->assert
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->never(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 2 times instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::never()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg))
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::never()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 2 times instead of 0'), $asserter->getCall())  . PHP_EOL . '[1] ' . $call->setArguments(array($arg)). PHP_EOL . '[2] ' . $call->setArguments(array($arg))
					)
				)
			)
		;

		$asserter->withArguments(uniqid());

		$this->assert
			->object($asserter->never())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}
}

?>
