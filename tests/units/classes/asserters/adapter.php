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

class adapter extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\asserter')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\adapter($generator = new asserter\generator($this));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($this->getScore())
			->object($asserter->getLocale())->isIdenticalTo($this->getLOcale())
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getCall())->isNull()
			->variable($asserter->getAdapter())->isNull()
			->array($asserter->getBeforeMethodCalls())->isEmpty()
			->array($asserter->getBeforeFunctionCalls())->isEmpty()
			->array($asserter->getAfterMethodCalls())->isEmpty()
			->array($asserter->getAfterFunctionCalls())->isEmpty()
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = uniqid()); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not a test adapter'), $asserter->getTypeOf($value)))
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$this->assert
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($test->getLocale()->_('%s is not a test adapter'), $asserter->getTypeOf($value))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->string($asserter->getAdapter())->isEqualTo($value)
		;

		$this->assert
			->object($asserter->setWith($adapter = new test\adapter()))->isIdenticalTo($asserter);
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->object($asserter->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testReset()
	{
		$this->mockGenerator
			->generate('mageekguy\atoum\test\adapter')
		;

		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->variable($asserter->getAdapter())->isNull()
			->object($asserter->reset())->isIdenticalTo($asserter)
			->variable($asserter->getAdapter())->isNull()
		;

		$asserter->setWith($adapter = new \mock\mageekguy\atoum\test\adapter());

		$this->assert
			->object($asserter->getAdapter())->isIdenticalTo($adapter)
			->object($asserter->reset())->isIdenticalTo($asserter)
			->object($asserter->getAdapter())->isIdenticalTo($adapter)
			->mock($adapter)
				->call('resetCalls')->once()
		;
	}

	public function testCall()
	{
		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->call(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
		;

		$asserter->setWith($adapter = new test\adapter());

		$this->assert
			->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter)
			->object($asserter->getCall())->isEqualTo(new php\call($function))
		;

		$asserter->withArguments();

		$this->assert
			->object($asserter->getCall())->isEqualTo(new php\call($function, array()))
			->object($asserter->call($function = uniqid()))->isIdenticalTo($asserter)
			->object($asserter->getCall())->isEqualTo(new php\call($function))
		;
	}

	public function testWithArguments()
	{
		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->withArguments(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
		;

		$asserter->setWith($adapter = new test\adapter());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->withArguments(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
		;

		$asserter->call($function = uniqid());

		$this->assert
			->object($asserter->withArguments())->isIdenticalTo($asserter)
			->object($asserter->getCall())->isEqualTo(new php\call($function, array()))
			->object($asserter->withArguments($arg1 = uniqid()))->isIdenticalTo($asserter)
			->object($asserter->getCall())->isEqualTo(new php\call($function, array($arg1)))
			->object($asserter->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isIdenticalTo($asserter)
			->object($asserter->getCall())->isEqualTo(new php\call($function, array($arg1, $arg2)))
		;
	}

	public function testWithAnyArguments()
	{
		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->withArguments(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
		;

		$asserter->setWith($adapter = new test\adapter());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->withArguments(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
		;

		$asserter->call($function = uniqid());

		$this->assert
			->object($asserter->getCall())->isEqualTo(new php\call($function))
			->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
			->object($asserter->getCall())->isEqualTo(new php\call($function))
		;

		$asserter->withArguments($arg = uniqid());

		$this->assert
			->object($asserter->getCall())->isEqualTo(new php\call($function, array($arg)))
			->object($asserter->withAnyArguments())->isIdenticalTo($asserter)
			->object($asserter->getCall())->isEqualTo(new php\call($function))
		;
	}

	public function testBeforeMethodCall()
	{
		$this->mockGenerator
			->generate('dummy')
		;

		$mock = new \mock\dummy();

		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));
		$this->assert
			->exception(function() use ($asserter, $mock) {
						$asserter->beforeMethodCall(uniqid(), $mock);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
		;

		$asserter->setWith($adapter = new test\adapter());

		$this->assert
			->object($asserter->beforeMethodCall('foo', $mock))->isEqualTo($beforeMethodCall = new asserters\adapter\call\mock($asserter, $mock, 'foo'))
			->array($asserter->getBeforeMethodCalls())->isEqualTo(array($beforeMethodCall))
			->object($asserter->beforeMethodCall('bar', $mock))->isEqualTo($otherBeforeMethodCall = new asserters\adapter\call\mock($asserter, $mock, 'bar'))
			->array($asserter->getBeforeMethodCalls())->isEqualTo(array($beforeMethodCall, $otherBeforeMethodCall))
		;
	}

	public function testWithAnyMethodCallsBefore()
	{
		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->array($asserter->getBeforeMethodCalls())->isEmpty()
			->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
			->array($asserter->getBeforeMethodCalls())->isEmpty()
		;

		$this->mockGenerator
			->generate('dummy')
		;

		$asserter->setWith($adapter = new test\adapter());

		$asserter->beforeMethodCall(uniqid(), new \mock\dummy());

		$this->assert
			->array($asserter->getBeforeMethodCalls())->isNotEmpty()
			->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
			->array($asserter->getBeforeMethodCalls())->isEmpty()
		;

		$asserter
			->beforeMethodCall($method1 = uniqid(), new \mock\dummy())
			->beforeMethodCall($method2 = uniqid(), new \mock\dummy())
		;

		$this->assert
			->array($asserter->getBeforeMethodCalls())->isNotEmpty()
			->object($asserter->withAnyMethodCallsBefore())->isIdenticalTo($asserter)
			->array($asserter->getBeforeMethodCalls())->isEmpty()
		;
	}

	public function testAfterMethodCall()
	{
		$this->mockGenerator
			->generate('dummy')
		;

		$mock = new \mock\dummy();

		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));
		$this->assert
			->exception(function() use ($asserter, $mock) {
						$asserter->afterMethodCall(uniqid(), $mock);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
		;

		$asserter->setWith($adapter = new test\adapter());

		$this->assert
			->object($asserter->afterMethodCall('foo', $mock))->isEqualTo($afterMethodCall = new asserters\adapter\call\mock($asserter, $mock, 'foo'))
			->array($asserter->getAfterMethodCalls())->isEqualTo(array($afterMethodCall))
			->object($asserter->afterMethodCall('bar', $mock))->isEqualTo($otherAfterMethodCall = new asserters\adapter\call\mock($asserter, $mock, 'bar'))
			->array($asserter->getAfterMethodCalls())->isEqualTo(array($afterMethodCall, $otherAfterMethodCall))
		;
	}

	public function testWithAnyMethodCallsAfter()
	{
		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->array($asserter->getAfterMethodCalls())->isEmpty()
			->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter)
			->array($asserter->getAfterMethodCalls())->isEmpty()
		;

		$this->mockGenerator
			->generate('dummy')
		;

		$asserter->setWith($adapter = new test\adapter());

		$asserter->afterMethodCall(uniqid(), new \mock\dummy());

		$this->assert
			->array($asserter->getAfterMethodCalls())->isNotEmpty()
			->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter)
			->array($asserter->getAfterMethodCalls())->isEmpty()
		;

		$asserter
			->afterMethodCall($method1 = uniqid(), new \mock\dummy())
			->afterMethodCall($method2 = uniqid(), new \mock\dummy())
		;

		$this->assert
			->array($asserter->getAfterMethodCalls())->isNotEmpty()
			->object($asserter->withAnyMethodCallsAfter())->isIdenticalTo($asserter)
			->array($asserter->getAfterMethodCalls())->isEmpty()
		;
	}

	public function testBeforeFunctionCall()
	{
		$this->mockGenerator
			->generate('dummy')
		;

		$mock = new \mock\dummy();

		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->beforeFunctionCall(uniqid(), new test\adapter());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
		;

		$asserter->setWith($adapter = new test\adapter());

		$this->assert
			->object($asserter->beforeFunctionCall('foo'))->isEqualTo($beforeFunctionCall = new asserters\adapter\call\adapter($asserter, $adapter, 'foo'))
			->array($asserter->getBeforeFunctionCalls())->isEqualTo(array($beforeFunctionCall))
			->object($asserter->beforeFunctionCall('bar'))->isEqualTo($otherBeforeFunctionCall = new asserters\adapter\call\adapter($asserter, $adapter, 'bar'))
			->array($asserter->getBeforeFunctionCalls())->isEqualTo(array($beforeFunctionCall, $otherBeforeFunctionCall))
		;
	}

	public function testWithAnyFunctionCallsBefore()
	{
		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->array($asserter->getBeforeFunctionCalls())->isEmpty()
			->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter)
			->array($asserter->getBeforeFunctionCalls())->isEmpty()
		;

		$asserter->setWith($adapter = new test\adapter());

		$asserter->beforeFunctionCall(uniqid());

		$this->assert
			->array($asserter->getBeforeFunctionCalls())->isNotEmpty()
			->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter)
			->array($asserter->getBeforeFunctionCalls())->isEmpty()
		;

		$asserter
			->beforeFunctionCall($method1 = uniqid())
			->beforeFunctionCall($method2 = uniqid())
		;

		$this->assert
			->array($asserter->getBeforeFunctionCalls())->isNotEmpty()
			->object($asserter->withAnyFunctionCallsBefore())->isIdenticalTo($asserter)
			->array($asserter->getBeforeFunctionCalls())->isEmpty()
		;
	}

	public function testAfterFunctionCall()
	{
		$this->mockGenerator
			->generate('dummy')
		;

		$mock = new \mock\dummy();

		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->afterFunctionCall(uniqid(), new test\adapter());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
		;

		$asserter->setWith($adapter = new test\adapter());

		$this->assert
			->object($asserter->afterFunctionCall('foo'))->isEqualTo($afterFunctionCall = new asserters\adapter\call\adapter($asserter, $adapter, 'foo'))
			->array($asserter->getAfterFunctionCalls())->isEqualTo(array($afterFunctionCall))
			->object($asserter->afterFunctionCall('bar'))->isEqualTo($otherAfterFunctionCall = new asserters\adapter\call\adapter($asserter, $adapter, 'bar'))
			->array($asserter->getAfterFunctionCalls())->isEqualTo(array($afterFunctionCall, $otherAfterFunctionCall))
		;
	}

	public function testWithAnyFunctionCallsAfter()
	{
		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->array($asserter->getAfterFunctionCalls())->isEmpty()
			->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter)
			->array($asserter->getAfterFunctionCalls())->isEmpty()
		;

		$asserter->setWith($adapter = new test\adapter());

		$asserter->afterFunctionCall(uniqid());

		$this->assert
			->array($asserter->getAfterFunctionCalls())->isNotEmpty()
			->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter)
			->array($asserter->getAfterFunctionCalls())->isEmpty()
		;

		$asserter
			->afterFunctionCall($method1 = uniqid())
			->afterFunctionCall($method2 = uniqid())
		;

		$this->assert
			->array($asserter->getAfterFunctionCalls())->isNotEmpty()
			->object($asserter->withAnyFunctionCallsAfter())->isIdenticalTo($asserter)
			->array($asserter->getAfterFunctionCalls())->isEmpty()
		;
	}

	public function testOnce()
	{
		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->once();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
		;

		$asserter->setWith($adapter = new test\adapter());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->once();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
		;

		$asserter->call('md5');

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s is called 0 time instead of 1'), $asserter->getCall()))
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
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time instead of 1'), $asserter->getCall())
					)
				)
			)
		;

		$call = new php\call('md5');

		$adapter->md5($firstArgument = uniqid());

		$this->assert
			->object($asserter->once())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$adapter->md5($secondArgument = uniqid());

		$this->assert
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->once(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s is called 2 times instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArgument)))
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
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time instead of 1'), $asserter->getCall())
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::once()',
						'fail' => sprintf($test->getLocale()->_('function %s is called 2 times instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($firstArgument)) . PHP_EOL . '[2] ' . $call->setArguments(array($secondArgument))
					)
				)
			)
		;

		$adapter->resetCalls();

		$score->reset();

		$asserter->withArguments($arg = uniqid());

		$adapter->md5($arg);

		$this->assert
			->object($asserter->once())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->withArguments(uniqid());

		$this->assert
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->once(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s is called 0 time instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
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
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time instead of 1'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call
					)
				)
			)
		;
	}

	public function testAtLeastOnce()
	{
		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->atLeastOnce();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
		;

		$asserter->setWith($adapter = new test\adapter());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->atLeastOnce();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
		;

		$asserter->call('md5');

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->atLeastOnce(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s is called 0 time'), $asserter->getCall()))
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
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time'), $asserter->getCall())
					)
				)
			)
		;

		$adapter->md5(uniqid());

		$this->assert
			->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$adapter->md5(uniqid());

		$this->assert
			->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$adapter->resetCalls();

		$score->reset();

		$asserter->withArguments($arg = uniqid());

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->atLeastOnce(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s is called 0 time'), $asserter->getCall()))
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
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time'), $asserter->getCall())
					)
				)
			)
		;

		$call = new php\call('md5');

		$adapter->md5($arg);

		$this->assert
			->object($asserter->atLeastOnce())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$previousCall = $asserter->getCall();

		$asserter->withArguments(uniqid());

		$this->assert
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->atLeastOnce(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s is called 0 time'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
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
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time'), $previousCall)
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::atLeastOnce()',
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call
					)
				)
			)
		;
	}

	public function testExactly()
	{
		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->exactly(2);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
		;

		$asserter->setWith($adapter = new test\adapter());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->exactly(2);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
		;


		$asserter->call('md5');

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall()))
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
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall())
					)
				)
			)
		;

		$call = new php\call('md5');

		$adapter->md5($arg = uniqid());

		$this->assert
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
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
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall())
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('function %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call
					)
				)
			)
		;

		$adapter->md5($otherArg = uniqid());

		$this->assert
			->object($asserter->exactly(2))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;

		$adapter->md5($anOtherArg = uniqid());

		$this->assert
			->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)) . PHP_EOL . '[2] ' . $call->setArguments(array($otherArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($anOtherArg)))
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
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall())
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('function %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg))
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $anotherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('function %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)) . PHP_EOL . '[2] ' . $call->setArguments(array($otherArg)) . PHP_EOL . '[3] ' . $call->setArguments(array($anOtherArg))
					)
				)
			)
		;

		$adapter->resetCalls();

		$score->reset();

		$asserter->withArguments($arg = uniqid());

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall()))
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
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall())
					)
				)
			)
		;

		$adapter->md5($usedArg = uniqid());

		$this->assert
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
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
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall())
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
					)
				)
			)
		;

		$adapter->md5($arg);

		$this->assert
			->exception(function() use (& $anotherLine, $asserter) { $anotherLine = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))
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
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall())
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $anotherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('function %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg))
					)
				)
			)
		;

		$adapter->md5($arg);

		$this->assert
			->object($asserter->exactly(2))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(3)
		;

		$adapter->md5($arg);

		$this->assert
			->exception(function() use (& $anAnotherLine, $asserter) { $anAnotherLine = __LINE__; $asserter->exactly(2); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)) . PHP_EOL . '[3] ' . $call->setArguments(array($arg))  . PHP_EOL . '[4] ' . $call->setArguments(array($arg)))
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
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall())
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('function %s is called 0 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $anotherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('function %s is called 1 time instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg))
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $anAnotherLine,
						'asserter' => get_class($asserter) . '::exactly()',
						'fail' => sprintf($test->getLocale()->_('function %s is called 3 times instead of 2'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)) . PHP_EOL . '[3] ' . $call->setArguments(array($arg))  . PHP_EOL . '[4] ' . $call->setArguments(array($arg))
					)
				)
			)
		;
	}

	public function testNever()
	{
		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->never();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
		;

		$asserter->setWith($adapter = new test\adapter());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->never();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Called function is undefined')
		;

		$call = new php\call('md5');

		$asserter->call('md5');

		$score->reset();

		$this->assert
			->object($asserter->never())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$adapter->md5($usedArg = uniqid());

		$this->assert
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->never(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg)))
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
						'fail' => sprintf($test->getLocale()->_('function %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($usedArg))
					)
				)
			)
		;

		$adapter->resetCalls();

		$score->reset();

		$asserter->withArguments($arg = uniqid());

		$this->assert
			->object($asserter->never())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$adapter->md5($arg);

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->never(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)))
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
						'fail' => sprintf($test->getLocale()->_('function %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg))
					)
				)
			)
		;

		$adapter->md5($arg);

		$this->assert
			->exception(function() use (& $otherLine, $asserter) { $otherLine = __LINE__; $asserter->never(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s is called 2 times instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg)))
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
						'fail' => sprintf($test->getLocale()->_('function %s is called 1 time instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg))
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::never()',
						'fail' => sprintf($test->getLocale()->_('function %s is called 2 times instead of 0'), $asserter->getCall()) . PHP_EOL . '[1] ' . $call->setArguments(array($arg)) . PHP_EOL . '[2] ' . $call->setArguments(array($arg))
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
