<?php

namespace mageekguy\atoum\tests\units\mock;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\test\adapter,
	mageekguy\atoum\test\adapter\invoker,
	mageekguy\atoum\mock\controller as testedClass
;

require_once __DIR__ . '/../../runner.php';

class with__callAndOtherMethods
{
	public $public = null;

	public function __construct() {}
	public function __call($method, $arguments) {}
	public function doesSomething() { return 'something done'; }
	public function doesSomethingElse() {}
}

class bar {}

class controller extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\test\adapter');
	}

	public function test__construct()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->sizeOf($mockController->getCalls())->isZero()
				->array($mockController->getInvokers())->isEmpty()
				->variable($mockController->getMockClass())->isNull()
				->array($mockController->getMethods())->isEmpty()
				->object($mockController->getIterator())->isEqualTo(new mock\controller\iterator($mockController))
				->boolean($mockController->autoBindIsEnabled())->isTrue()
			->if(testedClass::disableAutoBindForNewMock())
			->and($mockController = new testedClass())
			->then
				->boolean($mockController->autoBindIsEnabled())->isFalse()
			->if(testedClass::enableAutoBindForNewMock())
			->and($mockControllerWithAutoBind = new testedClass())
			->and(testedClass::disableAutoBindForNewMock())
			->and($mockControllerWithoutAutoBind = new testedClass())
			->then
				->boolean($mockControllerWithAutoBind->autoBindIsEnabled())->isTrue()
				->boolean($mockControllerWithoutAutoBind->autoBindIsEnabled())->isFalse()
		;
	}

	public function test__set()
	{
		$this
			->if($mockController = new testedClass())
			->and($mockController->{$method = 'aMethod'} = $return = uniqid())
			->then
				->string($mockController->invoke($method))->isEqualTo($return)
				->string($mockController->invoke(strtoupper($method)))->isEqualTo($return)
			->if($mockController->{$otherMethod = 'anOtherMethod'} = $otherReturn = uniqid())
			->then
				->string($mockController->invoke($method))->isEqualTo($return)
				->string($mockController->invoke(strtoupper($method)))->isEqualTo($return)
				->string($mockController->invoke($otherMethod))->isEqualTo($otherReturn)
				->string($mockController->invoke(strtoupper($otherMethod)))->isEqualTo($otherReturn)
			->if($mockController->control(new \mock\mageekguy\atoum\tests\units\mock\with__callAndOtherMethods()))
			->then
				->string($mockController->invoke($method))->isEqualTo($return)
				->string($mockController->invoke(strtoupper($method)))->isEqualTo($return)
			->if($mockController->control($mock = new \mock\mageekguy\atoum\tests\units\mock\with__callAndOtherMethods()))
			->and($mockController->undefinedMethod = $returnOfUndefinedMethod = uniqid())
			->then
				->string($mockController->invoke('undefinedMethod'))->isEqualTo($returnOfUndefinedMethod)
		;
	}

	/** @php 5.4 */
	public function test__setAndBindToMock()
	{
		$this
			->if($mockController = new testedClass())
			->and($mockController->control($mock = new \mock\mageekguy\atoum\tests\units\mock\foo()))
			->and($mockController->doesSomething = function() use (& $public) { $this->public = $public = uniqid(); })
			->and($mock->doesSomething())
			->then
				->string($mock->public)->isEqualTo($public)
			->if($mockController = new testedClass())
			->and($mockController->__construct = function() use (& $public) { $this->public = $public = uniqid(); })
			->and($mock = new \mock\mageekguy\atoum\tests\units\mock\with__callAndOtherMethods())
			->then
				->string($mock->public)->isEqualTo($public)
			->if($mockController = new testedClass())
			->and($mockController->__construct = function() use (& $public) { $this->public = $public = uniqid(); })
			->and($mock = new \mock\mageekguy\atoum\tests\units\mock\with__callAndOtherMethods($mockController))
			->then
				->string($mock->public)->isEqualTo($public)
			->if($mockController->disableAutoBind())
			->and($mock = new \mock\mageekguy\atoum\tests\units\mock\with__callAndOtherMethods($mockController))
			->then
				->variable($mock->public)->isNull()
			->if(testedClass::disableAutoBindForNewMock())
			->and($mock = new \mock\mageekguy\atoum\tests\units\mock\with__callAndOtherMethods($mockController))
			->then
				->variable($mock->public)->isNull()
			->if($mockController = new testedClass())
			->and($mockController->__construct = function() use (& $public) { $this->public = $public = uniqid(); })
			->and($mockController->enableAutoBind())
			->and($mock = new \mock\mageekguy\atoum\tests\units\mock\with__callAndOtherMethods($mockController))
			->then
				->string($mock->public)->isEqualTo($public)
		;
	}

	/** @php 5.4 */
	public function testEnableAutoBind()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->object($mockController->enableAutoBind())->isIdenticalTo($mockController)
				->boolean($mockController->autoBindIsEnabled())->isTrue()
			->if($mockController->disableAutoBind())
			->then
				->object($mockController->enableAutoBind())->isIdenticalTo($mockController)
				->boolean($mockController->autoBindIsEnabled())->isTrue()
			->if($mockController->disableAutoBind())
			->and($mockController->doesSomething = function() { return $this; })
			->and($mock = new \mock\mageekguy\atoum\tests\units\mock\foo($mockController))
			->then
				->object($mockController->enableAutoBind())->isIdenticalTo($mockController)
				->boolean($mockController->autoBindIsEnabled())->isTrue()
				->object($mock->doesSomething())->isIdenticalTo($mock)
		;
	}

	/** @php 5.4 */
	public function testDisableAutoBind()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->object($mockController->disableAutoBind())->isIdenticalTo($mockController)
				->boolean($mockController->autoBindIsEnabled())->isFalse()
			->if($mockController->enableAutoBind())
			->then
				->object($mockController->disableAutoBind())->isIdenticalTo($mockController)
				->boolean($mockController->autoBindIsEnabled())->isFalse()
			->if($mockController->enableAutoBind())
			->and($mockController->doesSomething = function() { return $this; })
			->and($mock = new \mock\mageekguy\atoum\tests\units\mock\foo($mockController))
			->then
				->object($mockController->disableAutoBind())->isIdenticalTo($mockController)
				->boolean($mockController->autoBindIsEnabled())->isFalse()
				->boolean(isset($mockController->doesSomething))->isFalse()
		;
	}

	public function test__isset()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->boolean(isset($mockController->{uniqid()}))->isFalse()
			->if($mockController->{$method = uniqid()} = function() {})
			->then
				->boolean(isset($mockController->{uniqid()}))->isFalse()
				->boolean(isset($mockController->{$method}))->isTrue()
				->boolean(isset($mockController->{strtoupper($method)}))->isTrue()
		;
	}

	public function test__get()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->object($mockController->{uniqid()})->isInstanceOf('mageekguy\atoum\test\adapter\invoker')
			->if($mockController->{$method = uniqid()} = $function = function() {})
			->then
				->object($mockController->{uniqid()})->isInstanceOf('mageekguy\atoum\test\adapter\invoker')
				->object($mockController->{$method}->getClosure())->isIdenticalTo($function)
				->object($mockController->{strtoupper($method)}->getClosure())->isIdenticalTo($function)
			->if($mockController->{$otherMethod = uniqid()} = $return = uniqid())
			->then
				->object($mockController->{uniqid()})->isInstanceOf('mageekguy\atoum\test\adapter\invoker')
				->object($mockController->{$method}->getClosure())->isIdenticalTo($function)
				->object($mockController->{strtoupper($method)}->getClosure())->isIdenticalTo($function)
				->object($mockController->{$otherMethod}->getClosure())->isInstanceOf('closure')
				->object($mockController->{strtoupper($otherMethod)}->getClosure())->isInstanceOf('closure')
				->string($mockController->{$otherMethod}->invoke())->isEqualTo($return)
				->string($mockController->{strtoupper($otherMethod)}->invoke())->isEqualTo($return)
		;
	}

	public function test__unset()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->boolean(isset($mockController->{$method = uniqid()}))->isFalse()
			->if($mockController->{$method} = uniqid())
			->then
				->boolean(isset($mockController->{$method}))->isTrue()
				->boolean(isset($mockController->{strtoupper($method)}))->isTrue()
			->when(function() use ($mockController, $method) { unset($mockController->{$method}); })
			->then
				->boolean(isset($mockController->{$method}))->isFalse()
				->boolean(isset($mockController->{strtoupper($method)}))->isFalse()
			->if($mockController->notControlNextNewMock())
			->and($reflectionClass = new \mock\reflectionClass($this))
			->and($mockController = new testedClass())
			->and($mockController->control($reflectionClass))
			->then
				->boolean(isset($mockController->getMethods))->isFalse()
			->if($mockController->getMethods = null)
			->then
				->boolean(isset($mockController->getMethods))->isTrue()
				->boolean(isset($mockController->GetMethods))->isTrue()
				->boolean(isset($mockController->GETMETHODS))->isTrue()
			->when(function() use ($mockController) { unset($mockController->getMethods); })
			->then
				->boolean(isset($mockController->getMethods))->isFalse()
				->boolean(isset($mockController->GetMethods))->isFalse()
				->boolean(isset($mockController->GETMETHODS))->isFalse()
		;
	}

	public function testSetIterator()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->object($mockController->setIterator($iterator = new mock\controller\iterator()))->isIdenticalTo($mockController)
				->object($mockController->getIterator())->isEqualTo($iterator)
				->object($iterator->getMockController())->isIdenticalTo($mockController)
				->object($mockController->setIterator())->isIdenticalTo($mockController)
				->object($mockController->getIterator())
					->isNotIdenticalTo($iterator)
					->isEqualTo(new mock\controller\iterator($mockController))
		;
	}

	public function getMockClass()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->variable($mockController->getMockClass())->isNull()
			->if($mockController->control($mock = new \mock\object()))
			->then
				->string($mockController->getMockClass())->isEqualTo(get_class($mock))
		;
	}

	public function testGetMethods()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->array($mockController->getMethods())->isEmpty()
			->if($mockController->control($mock = new \mock\object()))
			->then
				->string($mockController->getMockClass())->isEqualTo(get_class($mock))
				->array($mockController->getMethods())->isEqualTo($mock->getMockedMethods())
			->if($mockController->control($mock = new \mock\mageekguy\atoum\tests\units\mock\with__callAndOtherMethods()))
			->then
				->string($mockController->getMockClass())->isEqualTo(get_class($mock))
				->array($mockController->getMethods())->isEqualTo($mock->getMockedMethods())
		;
	}

	public function testMethods()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->object($mockController->methods())->isEqualTo($mockController->getIterator())
				->array($mockController->getIterator()->getFilters())->isEmpty()
				->object($mockController->methods($filter = function() {}))->isEqualTo($mockController->getIterator())
				->array($mockController->getIterator()->getFilters())->isEqualTo(array($filter))
				->object($mockController->methods($otherFilter = function() {}))->isEqualTo($mockController->getIterator())
				->array($mockController->getIterator()->getFilters())->isEqualTo(array($otherFilter))
		;
	}

	public function testMethodsMatching()
	{
		$this
			->if($mockController = new testedClass())
			->and($mockController->control(new \mock\mageekguy\atoum\tests\units\mock\with__callAndOtherMethods()))
			->then
				->object($mockController->methodsMatching('/Else$/i'))->isEqualTo($mockController->getIterator())
				->array($mockController->getIterator()->getMethods())->isEqualTo(array('doessomethingelse'))
				->object($mockController->methodsMatching('/^doesSomething/i'))->isEqualTo($mockController->getIterator())
				->array($mockController->getIterator()->getMethods())->isEqualTo(array('doessomething', 'doessomethingelse'))
		;
	}

	public function testDoesNothing()
	{
		$this
			->if($mock = new \mock\mageekguy\atoum\tests\units\mock\foo())
			->and($this->calling($mock)->doesSomething->doesNothing())
			->then
				->variable($mock->doesSomething())->isNull()
		;
	}

	public function testDoesSomething()
	{
		$this
			->if($mock = new \mock\mageekguy\atoum\tests\units\mock\with__callAndOtherMethods())
			->and($this->calling($mock)->doesSomething->doesNothing())
			->and($this->calling($mock)->doesSomething->doesSomething())
			->then
				->string($mock->doesSomething())->isEqualTo('something done')
		;
	}

	public function testControl()
	{
		$this
			->if->mockGenerator->shunt('__construct')
			->and($aMock = new \mock\reflectionClass(uniqid()))
			->and($mockController = new testedClass())
			->then
				->variable($mockController->getMockClass())->isNull()
				->array($mockController->getInvokers())->isEmpty()
				->sizeOf($mockController->getCalls())->isZero()
				->object($mockController->control($aMock))->isIdenticalTo($mockController)
				->string($mockController->getMockClass())->isEqualTo(get_class($aMock))
				->array($mockController->getInvokers())->hasSize(sizeof(\mock\reflectionClass::getMockedMethods()))
				->array($mockController->getMethods())->isEqualTo(\mock\reflectionClass::getMockedMethods())
				->sizeOf($mockController->getCalls())->isZero()
			->if($mock = new \mock\foo())
			->and($mockController = new testedClass())
			->and($mockController->controlNextNewMock())
			->and($mockController->control($mock))
			->then
				->variable(testedClass::get())->isNull()
			->if($mockController = new testedClass())
			->and($mockController->{$method = uniqid()} = uniqid())
			->then
				->exception(function() use ($mockController, $aMock) { $mockController->control($aMock); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Method \'' . get_class($aMock) . '::' . $method . '()\' does not exist')
			->if($mockController->disableMethodChecking())
			->and($mockController->{uniqid()} = uniqid())
			->then
				->object($mockController->control($aMock))->isIdenticalTo($mockController)
		;
	}

	public function testControlNextNewMock()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->object($mockController->controlNextNewMock())->isIdenticalTo($mockController)
				->object(testedClass::get())->isIdenticalTo($mockController)
		;
	}

	public function testNotControlNextNewMock()
	{
		$this
			->if($mockController = new testedClass())
			->and($mockController->controlNextNewMock())
			->then
				->object($mockController->notControlNextNewMock())->isIdenticalTo($mockController)
				->variable(testedClass::get())->isNull()
			->if($mockController = new testedClass())
			->and($otherMockController = new testedClass())
			->and($otherMockController->controlNextNewMock())
			->then
				->object($mockController->notControlNextNewMock())->isIdenticalTo($mockController)
				->object(testedClass::get())->isIdenticalTo($otherMockController)
		;
	}

	public function testInvoke()
	{
		$this
			->if($calls = new \mock\mageekguy\atoum\test\adapter\calls())
			->and($mockController = new testedClass())
			->and($mockController->notControlNextNewMock())
			->and($mockController->setCalls($calls))
			->and($method = uniqid())
			->then
				->exception(function() use ($mockController, $method) {
						$mockController->invoke($method, array());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Method ' . $method . '() is not under control')
			->if($return = uniqid())
			->and($mockController->test = function() use ($return) { return $return; })
			->and($argument1 = uniqid())
			->and($argument2 = uniqid())
			->and($argument3 = uniqid())
			->then
				->string($mockController->invoke('test'))->isEqualTo($return)
				->mock($calls)->call('addCall')->withArguments(new adapter\call('test', array()))->once()
				->string($mockController->invoke('test', array($argument1)))->isEqualTo($return)
				->mock($calls)->call('addCall')->withArguments(new adapter\call('test', array($argument1)))->once()
				->string($mockController->invoke('test', array($argument1, $argument2)))->isEqualTo($return)
				->mock($calls)->call('addCall')->withArguments(new adapter\call('test', array($argument1, $argument2)))->once()
				->string($mockController->invoke('test', array($argument1, $argument2, $argument3)))->isEqualTo($return)
				->mock($calls)->call('addCall')->withArguments(new adapter\call('test', array($argument1, $argument2, $argument3)))->once()
			->if($mockController->test2 = function() use ($return) { return $return; })
			->then
				->string($mockController->invoke('test2', array($argument1)))->isEqualTo($return)
				->mock($calls)->call('addCall')->withArguments(new adapter\call('test2', array($argument1)))->once()
				->string($mockController->invoke('test2', array($argument1, $argument2)))->isEqualTo($return)
				->mock($calls)->call('addCall')->withArguments(new adapter\call('test2', array($argument1, $argument2)))->once()
				->string($mockController->invoke('test2', array($argument1, $argument2, $argument3)))->isEqualTo($return)
				->mock($calls)->call('addCall')->withArguments(new adapter\call('test2', array($argument1, $argument2, $argument3)))->once()
			->if($mockController = new testedClass())
			->and($mockController->control($mock = new \mock\mageekguy\atoum\tests\units\mock\bar()))
			->then
				->exception(function() use ($mockController) { $mockController->foo = uniqid(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Method \'mock\\' . __NAMESPACE__ . '\bar::foo()\' does not exist')
		;
	}

	public function testGet()
	{
		$this
			->variable(testedClass::get())->isNull()
			->if($mockController = new testedClass())
			->and($mockController->controlNextNewMock())
			->then
				->object(testedClass::get(false))->isIdenticalTo($mockController)
				->object(testedClass::get())->isIdenticalTo($mockController)
				->variable(testedClass::get())->isNull()
		;
	}

	public function testReset()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->variable($mockController->getMockClass())->isNull()
				->array($mockController->getInvokers())->isEmpty()
				->array($mockController->getMethods())->isEmpty()
				->sizeof($mockController->getCalls())->isZero()
				->object($mockController->reset())->isIdenticalTo($mockController)
				->variable($mockController->getMockClass())->isNull()
				->array($mockController->getInvokers())->isEmpty()
				->array($mockController->getMethods())->isEmpty()
				->sizeof($mockController->getCalls())->isZero()
			->if($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = true)
			->and($mock = new \mock\mageekguy\atoum\tests\units\mock\controller($adapter))
			->and($mockController->control($mock))
			->and($mockController->{$method = __FUNCTION__} = function() {})
			->and($mockController->invoke($method, array()))
			->then
				->variable($mockController->getMockClass())->isNotNull()
				->array($mockController->getInvokers())->isNotEmpty()
				->array($mockController->getMethods())->isNotEmpty()
				->sizeof($mockController->getCalls())->isGreaterThan(0)
				->object($mockController->reset())->isIdenticalTo($mockController)
				->variable($mockController->getMockClass())->isNull()
				->array($mockController->getInvokers())->isEmpty()
				->array($mockController->getMethods())->isEmpty()
				->sizeof($mockController->getCalls())->isZero()
		;
	}

	public function testSetCalls()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->object($mockController->setCalls($calls = new adapter\calls()))->isIdenticalTo($mockController)
				->object($mockController->getCalls())->isIdenticalTo($calls)
				->object($mockController->setCalls())->isIdenticalTo($mockController)
				->object($mockController->getCalls())
					->isNotIdenticalTo($calls)
					->isEqualTo(new adapter\calls())
			->if($calls = new adapter\calls())
			->and($calls[] = new adapter\call(uniqid()))
			->and($mockController->setCalls($calls))
			->then
				->object($mockController->getCalls())
					->isIdenticalTo($calls)
					->hasSize(0)
		;
	}

	public function testGetCalls()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->object($mockController->getCalls())
					->isInstanceOf('mageekguy\atoum\test\adapter\calls')
					->hasSize(0)
			->if($mockController->setCalls($calls = new adapter\calls()))
			->then
				->object($mockController->getCalls())->isIdenticalTo($calls)
		;
	}

	public function testResetCalls()
	{
		$this
			->if($mockController = new testedClass())
			->and($mockController->{$method = uniqid()} = function() {})
			->then
				->sizeof($mockController->getCalls())->isZero()
			->if($mockController->invoke($method, array()))
			->then
				->sizeof($mockController->getCalls())->isGreaterThan(0)
				->object($mockController->resetCalls())->isIdenticalTo($mockController)
				->sizeof($mockController->getCalls())->isZero()
		;
	}

	public function testGetForMock()
	{
		$this
			->if($mockController = new testedClass())
			->and($mockController->control($mock = new \mock\object()))
			->then
				->object(testedClass::getForMock($mock))->isIdenticalTo($mockController)
			->if($otherMockController = new testedClass())
			->and($otherMockController->control($otherMock = new \mock\object()))
			->then
				->object(testedClass::getForMock($mock))->isIdenticalTo($mockController)
				->object(testedClass::getForMock($otherMock))->isIdenticalTo($otherMockController)
		;
	}
}
