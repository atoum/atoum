<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

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
		$this->assert
			->if($asserter = new asserters\mock($generator = new asserter\generator($this)))
			->then
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
		$this
			->mock('mageekguy\atoum\score')
			->mock('mageekguy\atoum\mock\controller')
			->assert
				->if($mockController = new \mock\mageekguy\atoum\mock\controller())
				->and($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->then
					->variable($asserter->getMock())->isNull()
					->object($asserter->reset())->isIdenticalTo($asserter)
					->variable($asserter->getMock())->isNull()
				->if($asserter->setWith($mock = new \mock\mageekguy\atoum\score()))
				->and($mock->setMockController($mockController))
				->then
					->object($asserter->getMock())->isIdenticalTo($mock)
					->object($asserter->reset())->isIdenticalTo($asserter)
					->object($asserter->getMock())->isIdenticalTo($mock)
					->mock($mockController)
						->call('resetCalls')
		;
	}

	public function testSetWith()
	{
		$this
			->mock(__CLASS__)
			->assert
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->and($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = true)
				->then
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
		$this
			->mock(__CLASS__)
			->assert
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->then
					->exception(function() use ($asserter) {
								$asserter->wasCalled();
							}
						)
							->isInstanceOf('mageekguy\atoum\exceptions\logic')
							->hasMessage('Mock is undefined')
				->if($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = true)
				->and($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\mock(null, null, $adapter)))
				->and($mock->getMockController()->resetCalls())
				->and($score->reset())
				->then
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
								'dataSetKey' => null,
								'dataSetProvider' => null,
								'class' => __CLASS__,
								'method' => $test->getCurrentMethod(),
								'file' => __FILE__,
								'line' => $line,
								'asserter' => get_class($asserter) . '::wasCalled()',
								'fail' => sprintf($test->getLocale()->_('%s is not called'), get_class($mock))
							)
						)
					)
				->if($score->reset())
				->then
					->exception(function() use (& $line, $asserter, & $failMessage) { $line = __LINE__; $asserter->wasCalled($failMessage = uniqid()); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage($failMessage)
					->integer($score->getPassNumber())->isZero()
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
								'asserter' => get_class($asserter) . '::wasCalled()',
								'fail' => $failMessage
							)
						)
					)
				->if($mock->getMockController()->{$method = __FUNCTION__} = function() {})
				->then
					->when(function() use ($mock, $method) { $mock->{$method}(); })
						->object($asserter->wasCalled())->isIdenticalTo($asserter)
						->integer($score->getPassNumber())->isEqualTo(1)
						->integer($score->getFailNumber())->isEqualTo(1)
		;
	}

	public function testWasNotCalled()
	{
		$this
			->mock(__CLASS__)
			->assert
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->then
					->exception(function() use ($asserter) {
								$asserter->wasNotCalled();
							}
						)
							->isInstanceOf('mageekguy\atoum\exceptions\logic')
							->hasMessage('Mock is undefined')
				->if($adapter = new atoum\test\adapter())
				->and($adapter->class_exists = true)
				->and($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\mock(null, null, $adapter)))
				->and($mock->getMockController()->resetCalls())
				->and($score->reset())
				->then
					->object($asserter->wasNotCalled())->isIdenticalTo($asserter)
					->integer($score->getPassNumber())->isEqualTo(1)
					->integer($score->getFailNumber())->isZero()
				->if($mock->getMockController()->{$method = __FUNCTION__} = function() {})
				->then
					->when(function() use ($mock, $method) { $mock->{$method}(); })
						->exception(function() use (& $line, $asserter, & $failMessage) { $line = __LINE__; $asserter->wasNotCalled($failMessage = uniqid()); })
							->isInstanceOf('mageekguy\atoum\asserter\exception')
							->hasMessage($failMessage)
						->integer($score->getPassNumber())->isEqualTo(1)
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
									'asserter' => get_class($asserter) . '::wasNotCalled()',
									'fail' => $failMessage
								)
							)
						)
		;
	}

	public function testBeforeMethodCall()
	{
		$this
			->mock('mageekguy\atoum\tests\units\asserters\dummy')
			->assert
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->then
					->exception(function() use ($asserter) {
								$asserter->beforeMethodCall(uniqid());
							}
						)
							->isInstanceOf('mageekguy\atoum\exceptions\logic')
							->hasMessage('Mock is undefined')
				->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
				->then
					->object($asserter->beforeMethodCall('foo'))->isEqualTo($beforeMethodCall = new asserters\mock\call\mock($asserter, $mock, 'foo'))
					->array($asserter->getBeforeMethodCalls())->isEqualTo(array($beforeMethodCall))
					->object($asserter->beforeMethodCall('bar'))->isEqualTo($otherBeforeMethodCall = new asserters\mock\call\mock($asserter, $mock, 'bar'))
					->array($asserter->getBeforeMethodCalls())->isEqualTo(array($beforeMethodCall, $otherBeforeMethodCall))
		;
	}

	public function testWithAnyMethodCallsBefore()
	{
		$this
			->mock('mageekguy\atoum\tests\units\asserters\dummy')
			->assert
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->then
					->array($asserter->getBeforeMethodCalls())->isEmpty()
					->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
					->array($asserter->getBeforeMethodCalls())->isEmpty()
				->if($asserter->setWith(new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
				->and($asserter->beforeMethodCall(uniqid()))
				->then
					->array($asserter->getBeforeMethodCalls())->isNotEmpty()
					->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
					->array($asserter->getBeforeMethodCalls())->isEmpty()
				->if($asserter
					->beforeMethodCall(uniqid())
					->beforeMethodCall(uniqid())
				)
				->then
					->array($asserter->getBeforeMethodCalls())->isNotEmpty()
					->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
					->array($asserter->getBeforeMethodCalls())->isEmpty()
		;
	}

	public function testAfterMethodCall()
	{
		$this
			->mock('mageekguy\atoum\tests\units\asserters\dummy')
			->assert
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->then
					->exception(function() use ($asserter) {
								$asserter->afterMethodCall(uniqid());
							}
						)
							->isInstanceOf('mageekguy\atoum\exceptions\logic')
							->hasMessage('Mock is undefined')
				->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
				->then
					->object($asserter->afterMethodCall('foo'))->isEqualTo($afterMethodCall = new asserters\mock\call\mock($asserter, $mock, 'foo'))
					->array($asserter->getAfterMethodCalls())->isEqualTo(array($afterMethodCall))
					->object($asserter->afterMethodCall('bar'))->isEqualTo($otherAfterMethodCall = new asserters\mock\call\mock($asserter, $mock, 'bar'))
					->array($asserter->getAfterMethodCalls())->isEqualTo(array($afterMethodCall, $otherAfterMethodCall))
		;
	}

	public function testWithAnyMethodCallsAfter()
	{
		$this
			->mock('mageekguy\atoum\tests\units\asserters\dummy')
			->assert
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->then
					->array($asserter->getAfterMethodCalls())->isEmpty()
					->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter)
					->array($asserter->getAfterMethodCalls())->isEmpty()
				->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
				->and($asserter->afterMethodCall($function = uniqid()))
				->then
					->array($asserter->getAfterMethodCalls())->isNotEmpty()
					->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter)
					->array($asserter->getAfterMethodCalls())->isEmpty()
				->if($asserter
					->afterMethodCall($function1 = uniqid())
					->afterMethodCall($function2 = uniqid())
				)
				->then
					->array($asserter->getAfterMethodCalls())->isNotEmpty()
					->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter)
					->array($asserter->getAfterMethodCalls())->isEmpty()
		;
	}

	public function testBeforeFunctionCall()
	{
		$this
			->mock('mageekguy\atoum\tests\units\asserters\dummy')
			->assert
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->then
					->exception(function() use ($asserter) {
								$asserter->beforeFunctionCall(uniqid(), new test\adapter());
							}
						)
							->isInstanceOf('mageekguy\atoum\exceptions\logic')
							->hasMessage('Mock is undefined')
				->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
				->and($adapter = new test\adapter())
				->then
					->object($asserter->beforeFunctionCall('foo', $adapter))->isEqualTo($beforeFunctionCall = new asserters\mock\call\adapter($asserter, $adapter, 'foo'))
					->array($asserter->getBeforeFunctionCalls())->isEqualTo(array($beforeFunctionCall))
					->object($asserter->beforeFunctionCall('bar', $adapter))->isEqualTo($otherBeforeFunctionCall = new asserters\mock\call\adapter($asserter, $adapter, 'bar'))
					->array($asserter->getBeforeFunctionCalls())->isEqualTo(array($beforeFunctionCall, $otherBeforeFunctionCall))
		;
	}

	public function testWithAnyFunctionCallsBefore()
	{
		$this
			->mock('mageekguy\atoum\tests\units\asserters\dummy')
			->assert
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->then
					->array($asserter->getBeforeFunctionCalls())->isEmpty()
					->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter)
					->array($asserter->getBeforeFunctionCalls())->isEmpty()
				->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
				->and($adapter = new test\adapter())
				->and($asserter->beforeFunctionCall($function = uniqid(), $adapter))
				->then
					->array($asserter->getBeforeFunctionCalls())->isNotEmpty()
					->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter)
					->array($asserter->getBeforeFunctionCalls())->isEmpty()
				->if($asserter
					->beforeFunctionCall($function1 = uniqid(), $adapter)
					->beforeFunctionCall($function2 = uniqid(), $adapter)
				)
				->then
					->array($asserter->getBeforeFunctionCalls())->isNotEmpty()
					->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter)
					->array($asserter->getBeforeFunctionCalls())->isEmpty()
		;
	}

	public function testAfterFunctionCall()
	{
		$this
			->mock('mageekguy\atoum\tests\units\asserters\dummy')
			->assert
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->then
					->exception(function() use ($asserter) {
								$asserter->afterFunctionCall(uniqid(), new test\adapter());
							}
						)
							->isInstanceOf('mageekguy\atoum\exceptions\logic')
							->hasMessage('Mock is undefined')
				->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
				->and($adapter = new test\adapter())
				->then
					->object($asserter->afterFunctionCall('foo', $adapter))->isEqualTo($afterFunctionCall = new asserters\mock\call\adapter($asserter, $adapter, 'foo'))
					->array($asserter->getAfterFunctionCalls())->isEqualTo(array($afterFunctionCall))
					->object($asserter->afterFunctionCall('bar', $adapter))->isEqualTo($otherAfterFunctionCall = new asserters\mock\call\adapter($asserter, $adapter, 'bar'))
					->array($asserter->getAfterFunctionCalls())->isEqualTo(array($afterFunctionCall, $otherAfterFunctionCall))
		;
	}

	public function testWithAnyFunctionCallsAfter()
	{
		$this
			->mock('mageekguy\atoum\tests\units\asserters\dummy')
			->assert
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->then
					->array($asserter->getAfterFunctionCalls())->isEmpty()
					->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter)
					->array($asserter->getAfterFunctionCalls())->isEmpty()
				->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
				->and($adapter = new test\adapter())
				->and($asserter->afterFunctionCall($function = uniqid(), $adapter))
				->then
					->array($asserter->getAfterFunctionCalls())->isNotEmpty()
					->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter)
					->array($asserter->getAfterFunctionCalls())->isEmpty()
				->if($asserter
					->afterFunctionCall($function1 = uniqid(), $adapter)
					->afterFunctionCall($function2 = uniqid(), $adapter)
				)
				->then
					->array($asserter->getAfterFunctionCalls())->isNotEmpty()
					->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter)
					->array($asserter->getAfterFunctionCalls())->isEmpty()
		;
	}

	public function testCall()
	{
		$this
			->mock('mageekguy\atoum\tests\units\asserters\dummy')
			->assert
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->then
					->exception(function() use ($asserter) {
							$asserter->call(uniqid());
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage('Mock is undefined')
				->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
				->then
					->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getCall())->isEqualTo(new php\call($function, null, $mock))
				->if($asserter->withArguments())
				->then
					->object($asserter->getCall())->isEqualTo(new php\call($function, array(), $mock))
					->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter)
					->object($asserter->getCall())->isEqualTo(new php\call($function, null, $mock))
		;
	}

	public function testWithArguments()
	{
		$this
			->mock('mageekguy\atoum\tests\units\asserters\dummy')
			->assert
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->then
					->exception(function() use ($asserter) {
								$asserter->withArguments(uniqid());
							}
						)
							->isInstanceOf('mageekguy\atoum\exceptions\logic')
							->hasMessage('Mock is undefined')
				->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
				->then
					->exception(function() use ($asserter) {
								$asserter->withArguments(uniqid());
							}
						)
							->isInstanceOf('mageekguy\atoum\exceptions\logic')
							->hasMessage('Called method is undefined')
				->if($asserter->call($function = uniqid()))
				->then
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
		$this
			->mock('mageekguy\atoum\tests\units\asserters\dummy')
			->assert
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->then
					->exception(function() use ($asserter) {
								$asserter->withArguments(uniqid());
							}
						)
							->isInstanceOf('mageekguy\atoum\exceptions\logic')
							->hasMessage('Mock is undefined')
				->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
				->then
					->exception(function() use ($asserter) {
								$asserter->withArguments(uniqid());
							}
						)
							->isInstanceOf('mageekguy\atoum\exceptions\logic')
							->hasMessage('Called method is undefined')
				->if($asserter->call($function = uniqid()))
				->then
					->object($asserter->getCall())->isEqualTo(new php\call($function, null, $mock))
					->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
					->object($asserter->getCall())->isEqualTo(new php\call($function, null, $mock))
				->if($asserter->withArguments($arg = uniqid()))
				->then
					->object($asserter->getCall())->isEqualTo(new php\call($function, array($arg), $mock))
					->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
					->object($asserter->getCall())->isEqualTo(new php\call($function, null, $mock))
		;
	}

	public function testOnce()
	{

		$this
			->mock('mageekguy\atoum\tests\units\asserters\dummy')
			->assert
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->then
					->exception(function() use ($asserter) {
								$asserter->once();
							}
						)
							->isInstanceOf('mageekguy\atoum\exceptions\logic')
							->hasMessage('Mock is undefined')
				->if($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
				->then
					->exception(function() use ($asserter) {
								$asserter->once();
							}
						)
							->isInstanceOf('mageekguy\atoum\exceptions\logic')
							->hasMessage('Called method is undefined')
				->if($asserter->call('foo'))
				->and($score->reset())
				->then
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
								'dataSetKey' => null,
								'dataSetProvider' => null,
								'class' => __CLASS__,
								'method' => $test->getCurrentMethod(),
								'file' => __FILE__,
								'line' => $line,
								'asserter' => get_class($asserter) . '::once()',
								'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall())
							)
						)
					)
				->if($call = new php\call('foo', null, $mock))
				->then
					->when(function() use ($mock, & $usedArg) { $mock->foo($usedArg = uniqid()); })
						->object($asserter->once())->isIdenticalTo($asserter)
						->integer($score->getPassNumber())->isEqualTo(1)
						->integer($score->getFailNumber())->isEqualTo(1)
					->when(function() use ($mock, & $otherUsedArg) { $mock->foo($otherUsedArg = uniqid()); })
						->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->once(); })
							->isInstanceOf('mageekguy\atoum\asserter\exception')
							->hasMessage(sprintf($test->getLocale()->_('method %s is called 2 times instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($otherUsedArg)))
						->integer($score->getPassNumber())->isEqualTo(1)
						->integer($score->getFailNumber())->isEqualTo(2)
						->array($score->getFailAssertions())->isEqualTo(array(
								array(
									'case' => null,
									'dataSetKey' => null,
									'dataSetProvider' => null,
									'class' => __CLASS__,
									'method' => $test->getCurrentMethod(),
									'file' => __FILE__,
									'line' => $line,
									'asserter' => get_class($asserter) . '::once()',
									'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall())
								),
								array(
									'case' => null,
									'dataSetKey' => null,
									'dataSetProvider' => null,
									'class' => __CLASS__,
									'method' => $test->getCurrentMethod(),
									'file' => __FILE__,
									'line' => $otherLine,
									'asserter' => get_class($asserter) . '::once()',
									'fail' => sprintf($test->getLocale()->_('method %s is called 2 times instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($otherUsedArg))
								)
							)
						)
				->if($mock->getMockController()->resetCalls())
				->and($score->reset())
				->and($asserter->withArguments($usedArg = uniqid()))
				->then
					->when(function() use ($mock, $usedArg) { $mock->foo($usedArg); })
						->object($asserter->once())->isIdenticalTo($asserter)
						->integer($score->getPassNumber())->isEqualTo(1)
						->integer($score->getFailNumber())->isZero()
					->when(function() use ($asserter, & $arg) { $asserter->withArguments($arg = uniqid()); })
						->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
							->isInstanceOf('mageekguy\atoum\asserter\exception')
							->hasMessage(sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
						->integer($score->getPassNumber())->isEqualTo(1)
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
									'asserter' => get_class($asserter) . '::once()',
									'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
								)
							)
						)
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->and($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
				->and($score->reset())
				->and($asserter->beforeMethodCall('bar')->call('foo'))
				->then
					->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall()))
					->integer($score->getPassNumber())->isEqualTo(0)
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
								'asserter' => get_class($asserter) . '::once()',
								'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall())
							)
						)
					)
					->when(function() use ($mock) { $mock->foo(uniqid()); })
						->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->once(); })
							->isInstanceOf('mageekguy\atoum\asserter\exception')
							->hasMessage(sprintf($test->getLocale()->_('method %s is not called'), current($asserter->getBeforeMethodCalls())))
						->integer($score->getPassNumber())->isEqualTo(0)
						->integer($score->getFailNumber())->isEqualTo(2)
						->array($score->getFailAssertions())->isEqualTo(array(
								array(
									'case' => null,
									'dataSetKey' => null,
									'dataSetProvider' => null,
									'class' => __CLASS__,
									'method' => $test->getCurrentMethod(),
									'file' => __FILE__,
									'line' => $line,
									'asserter' => get_class($asserter) . '::once()',
									'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall())
								),
								array(
									'case' => null,
									'dataSetKey' => null,
									'dataSetProvider' => null,
									'class' => __CLASS__,
									'method' => $test->getCurrentMethod(),
									'file' => __FILE__,
									'line' => $otherLine,
									'asserter' => get_class($asserter) . '::once()',
									'fail' => sprintf($test->getLocale()->_('method %s is not called'), current($asserter->getBeforeMethodCalls()))
								)
							)
						)
				->if($mock->getMockController()->resetCalls())
				->then
					->when(function() use ($mock) { $mock->bar(uniqid()); $mock->foo(uniqid()); })
						->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->once(); })
							->isInstanceOf('mageekguy\atoum\asserter\exception')
							->hasMessage(sprintf($test->getLocale()->_('method %s is not called before method %s'), $asserter->getCall(), current($asserter->getBeforeMethodCalls())))
						->integer($score->getPassNumber())->isEqualTo(0)
						->integer($score->getFailNumber())->isEqualTo(3)
						->array($score->getFailAssertions())->isEqualTo(array(
								array(
									'case' => null,
									'dataSetKey' => null,
									'dataSetProvider' => null,
									'class' => __CLASS__,
									'method' => $test->getCurrentMethod(),
									'file' => __FILE__,
									'line' => $line,
									'asserter' => get_class($asserter) . '::once()',
									'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall())
								),
								array(
									'case' => null,
									'dataSetKey' => null,
									'dataSetProvider' => null,
									'class' => __CLASS__,
									'method' => $test->getCurrentMethod(),
									'file' => __FILE__,
									'line' => $otherLine,
									'asserter' => get_class($asserter) . '::once()',
									'fail' => sprintf($test->getLocale()->_('method %s is not called'), current($asserter->getBeforeMethodCalls()))
								),
								array(
									'case' => null,
									'dataSetKey' => null,
									'dataSetProvider' => null,
									'class' => __CLASS__,
									'method' => $test->getCurrentMethod(),
									'file' => __FILE__,
									'line' => $anotherLine,
									'asserter' => get_class($asserter) . '::once()',
									'fail' => sprintf($test->getLocale()->_('method %s is not called before method %s'), $asserter->getCall(), current($asserter->getBeforeMethodCalls()))
								)
							)
						)
				->if($mock->getMockController()->resetCalls())
				->then
					->when(function() use ($mock) { $mock->foo(uniqid()); $mock->bar(uniqid()); })
						->object($asserter->once())->isIdenticalTo($asserter)
						->integer($score->getPassNumber())->isEqualTo(2)
						->integer($score->getFailNumber())->isEqualTo(3)
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->and($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
				->and($score->reset())
				->and($asserter->afterMethodCall('bar')->call('foo'))
				->then
					->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall()))
					->integer($score->getPassNumber())->isEqualTo(0)
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
								'asserter' => get_class($asserter) . '::once()',
								'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall())
							)
						)
					)
					->when(function() use ($mock) { $mock->foo(uniqid()); })
						->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->once(); })
							->isInstanceOf('mageekguy\atoum\asserter\exception')
							->hasMessage(sprintf($test->getLocale()->_('method %s is not called'), current($asserter->getAfterMethodCalls())))
						->integer($score->getPassNumber())->isEqualTo(0)
						->integer($score->getFailNumber())->isEqualTo(2)
						->array($score->getFailAssertions())->isEqualTo(array(
								array(
									'case' => null,
									'dataSetKey' => null,
									'dataSetProvider' => null,
									'class' => __CLASS__,
									'method' => $test->getCurrentMethod(),
									'file' => __FILE__,
									'line' => $line,
									'asserter' => get_class($asserter) . '::once()',
									'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall())
								),
								array(
									'case' => null,
									'dataSetKey' => null,
									'dataSetProvider' => null,
									'class' => __CLASS__,
									'method' => $test->getCurrentMethod(),
									'file' => __FILE__,
									'line' => $otherLine,
									'asserter' => get_class($asserter) . '::once()',
									'fail' => sprintf($test->getLocale()->_('method %s is not called'), current($asserter->getAfterMethodCalls()))
								)
							)
						)
				->if($mock->getMockController()->resetCalls())
				->then
					->when(function() use ($mock) { $mock->foo(uniqid()); $mock->bar(uniqid()); })
						->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->once(); })
							->isInstanceOf('mageekguy\atoum\asserter\exception')
							->hasMessage(sprintf($test->getLocale()->_('method %s is not called after method %s'), $asserter->getCall(), current($asserter->getAfterMethodCalls())))
						->integer($score->getPassNumber())->isEqualTo(0)
						->integer($score->getFailNumber())->isEqualTo(3)
						->array($score->getFailAssertions())->isEqualTo(array(
								array(
									'case' => null,
									'dataSetKey' => null,
									'dataSetProvider' => null,
									'class' => __CLASS__,
									'method' => $test->getCurrentMethod(),
									'file' => __FILE__,
									'line' => $line,
									'asserter' => get_class($asserter) . '::once()',
									'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 1'), $asserter->getCall())
								),
								array(
									'case' => null,
									'dataSetKey' => null,
									'dataSetProvider' => null,
									'class' => __CLASS__,
									'method' => $test->getCurrentMethod(),
									'file' => __FILE__,
									'line' => $otherLine,
									'asserter' => get_class($asserter) . '::once()',
									'fail' => sprintf($test->getLocale()->_('method %s is not called'), current($asserter->getAfterMethodCalls()))
								),
								array(
									'case' => null,
									'dataSetKey' => null,
									'dataSetProvider' => null,
									'class' => __CLASS__,
									'method' => $test->getCurrentMethod(),
									'file' => __FILE__,
									'line' => $anotherLine,
									'asserter' => get_class($asserter) . '::once()',
									'fail' => sprintf($test->getLocale()->_('method %s is not called after method %s'), $asserter->getCall(), current($asserter->getAfterMethodCalls()))
								)
							)
						)
				->if($mock->getMockController()->resetCalls())
				->then
					->when(function() use ($mock) { $mock->bar(uniqid()); $mock->foo(uniqid()); })
						->object($asserter->once())->isIdenticalTo($asserter)
						->integer($score->getPassNumber())->isEqualTo(2)
						->integer($score->getFailNumber())->isEqualTo(3)
				->if($asserter = new asserters\mock(new asserter\generator($test = new self($score = new atoum\score()))))
				->and($asserter->setWith($mock = new \mock\mageekguy\atoum\tests\units\asserters\dummy()))
				->and($score->reset())
				->and($asserter->beforeMethodCall('bar')->withArguments($arg = uniqid())->call('foo'))
				->then
					->when(function() use ($mock) { $mock->foo(uniqid()); })
						->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
							->isInstanceOf('mageekguy\atoum\asserter\exception')
							->hasMessage(sprintf($test->getLocale()->_('method %s is not called'), current($asserter->getBeforeMethodCalls())))
						->integer($score->getPassNumber())->isEqualTo(0)
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
						'dataSetKey' => null,
						'dataSetProvider' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::atLeastOnce()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time'), $asserter->getCall())
					)
				)
			)
			->when(function() use ($mock) { $mock->foo(uniqid()); })
			->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->when(function() use ($mock) { $mock->foo(uniqid()); })
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
						'dataSetKey' => null,
						'dataSetProvider' => null,
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

		$this->assert
			->when(function() use ($mock, $usedArg) { $mock->foo($usedArg); })
			->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->when(function() use ($asserter, & $otherArg, & $previousCall) { $previousCall = $asserter->getCall(); $asserter->withArguments($otherArg = uniqid()); })
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->atLeastOnce(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('method %s is called 0 time'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'dataSetKey' => null,
						'dataSetProvider' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::atLeastOnce()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time'), $previousCall)
					),
					array(
						'case' => null,
						'dataSetKey' => null,
						'dataSetProvider' => null,
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
						'dataSetKey' => null,
						'dataSetProvider' => null,
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

		$this->assert
			->when(function() use ($mock, & $usedArg) { $mock->foo($usedArg = uniqid()); })
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($test->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
				->integer($score->getPassNumber())->isZero()
				->integer($score->getFailNumber())->isEqualTo(2)
				->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $line,
							'asserter' => get_class($asserter) . '::exactly()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall())
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $otherLine,
							'asserter' => get_class($asserter) . '::exactly()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
						)
					)
				)
			->when(function() use ($mock, & $otherUsedArg) { $mock->foo($otherUsedArg = uniqid()); })
				->object($asserter->exactly(2))->isIdenticalTo($asserter)
				->integer($score->getPassNumber())->isEqualTo(1)
				->integer($score->getFailNumber())->isEqualTo(2)
			->when(function() use ($mock, & $anOtherUsedArg) { $mock->foo($anOtherUsedArg = uniqid()); })
				->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($test->getLocale()->_('method %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($otherUsedArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($anOtherUsedArg)))
				->integer($score->getPassNumber())->isEqualTo(1)
				->integer($score->getFailNumber())->isEqualTo(3)
				->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $line,
							'asserter' => get_class($asserter) . '::exactly()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall())
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $otherLine,
							'asserter' => get_class($asserter) . '::exactly()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
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
						'dataSetKey' => null,
						'dataSetProvider' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall())
					)
				)
			)
			->when(function() use ($mock, & $usedArg) { $mock->foo($usedArg = uniqid()); })
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
				->integer($score->getPassNumber())->isZero()
				->integer($score->getFailNumber())->isEqualTo(2)
				->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $line,
							'asserter' => get_class($asserter) . '::exactly()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall())
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $otherLine,
							'asserter' => get_class($asserter) . '::exactly()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
						)
					)
				)
			->when(function() use ($mock, $arg) { $mock->foo($arg); })
				->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($test->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))
				->integer($score->getPassNumber())->isZero()
				->integer($score->getFailNumber())->isEqualTo(3)
				->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $line,
							'asserter' => get_class($asserter) . '::exactly()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall())
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $otherLine,
							'asserter' => get_class($asserter) . '::exactly()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $anotherLine,
							'asserter' => get_class($asserter) . '::exactly()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg))
						)
					)
				)
			->when(function() use ($mock, $arg) { $mock->foo($arg); })
				->object($asserter->exactly(2))->isIdenticalTo($asserter)
				->integer($score->getPassNumber())->isEqualTo(1)
				->integer($score->getFailNumber())->isEqualTo(3)
			->when(function() use ($mock, $arg) { $mock->foo($arg); })
				->exception(function() use (& $anAnotherLine, $asserter) { $anAnotherLine = __LINE__; $asserter->exactly(2); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($test->getLocale()->_('method %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)) . PHP_EOL . '[3] ' . $call->setArguments(array($arg)) . PHP_EOL . '[4] ' . $call->setArguments(array($arg)))
				->integer($score->getPassNumber())->isEqualTo(1)
				->integer($score->getFailNumber())->isEqualTo(4)
				->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $line,
							'asserter' => get_class($asserter) . '::exactly()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall())
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $otherLine,
							'asserter' => get_class($asserter) . '::exactly()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $anotherLine,
							'asserter' => get_class($asserter) . '::exactly()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg))
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
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

		$this->assert
			->when(function() use ($mock, & $usedArg) { $mock->foo($usedArg = uniqid()); })
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
							'dataSetKey' => null,
							'dataSetProvider' => null,
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

		$this->assert
			->when(function() use ($mock, $arg) { $mock->foo($arg); })
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($test->getLocale()->_('method %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
				->integer($score->getPassNumber())->isEqualTo(1)
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
							'asserter' => get_class($asserter) . '::never()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg))
						)
					)
				)
			->when(function() use ($mock, $arg) { $mock->foo($arg); })
				->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->never(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($test->getLocale()->_('method %s is called 2 times instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))
				->integer($score->getPassNumber())->isEqualTo(1)
				->integer($score->getFailNumber())->isEqualTo(2)
				->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $line,
							'asserter' => get_class($asserter) . '::never()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg))
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $otherLine,
							'asserter' => get_class($asserter) . '::never()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 2 times instead of 0'), $asserter->getCall())  . PHP_EOL . '[1] ' . $call->setArguments(array($arg)). PHP_EOL . '[2] ' . $call->setArguments(array($arg))
						)
					)
				)
			->when(function() use ($mock, $arg) { $mock->foo($arg); })
				->exception(function() use (& $anOtherLine, $asserter, & $message) { $anOtherLine = __LINE__; $asserter->never($message = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($message)
				->integer($score->getPassNumber())->isEqualTo(1)
				->integer($score->getFailNumber())->isEqualTo(3)
				->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $line,
							'asserter' => get_class($asserter) . '::never()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg))
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $otherLine,
							'asserter' => get_class($asserter) . '::never()',
							'fail' => sprintf($test->getLocale()->_('method %s is called 2 times instead of 0'), $asserter->getCall())  . PHP_EOL . '[1] ' . $call->setArguments(array($arg)). PHP_EOL . '[2] ' . $call->setArguments(array($arg))
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $anOtherLine,
							'asserter' => get_class($asserter) . '::never()',
							'fail' => $message
						)
					)
				)
		;

		$asserter->withArguments(uniqid());

		$this->assert
			->object($asserter->never())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(3)
		;
	}
}

?>
