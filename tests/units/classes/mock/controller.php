<?php

namespace mageekguy\atoum\tests\units\mock;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\test\adapter\invoker,
	mageekguy\atoum\mock\controller as testedClass
;

require_once __DIR__ . '/../../runner.php';

class foo
{
	public $public = null;

	public function __construct() {}
	public function __call($method, $arguments) {}
	public function doSomething() { return 'something done'; }
	public function doSomethingElse() {}
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
				->array($mockController->getCalls())->isEmpty()
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
			->and($otherMockController = new testedClass())
			->then
				->boolean($mockController->autoBindIsEnabled())->isFalse()
				->boolean($otherMockController->autoBindIsEnabled())->isTrue()
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
				->array($mockController->getCalls($method))->hasSize(2)
				->array($mockController->getCalls(strtoupper($method)))->hasSize(2)
			->if($mockController->{$otherMethod = 'anOtherMethod'} = $otherReturn = uniqid())
			->then
				->string($mockController->invoke($method))->isEqualTo($return)
				->string($mockController->invoke(strtoupper($method)))->isEqualTo($return)
				->array($mockController->getCalls($method))->hasSize(4)
				->array($mockController->getCalls(strtoupper($method)))->hasSize(4)
				->string($mockController->invoke($otherMethod))->isEqualTo($otherReturn)
				->string($mockController->invoke(strtoupper($otherMethod)))->isEqualTo($otherReturn)
				->array($mockController->getCalls($otherMethod))->hasSize(2)
				->array($mockController->getCalls(strtoupper($otherMethod)))->hasSize(2)
			->if($mockController->control(new \mock\mageekguy\atoum\tests\units\mock\foo()))
			->then
				->boolean(isset($mockController->__call))->isTrue()
				->string($mockController->invoke($method))->isEqualTo($return)
				->string($mockController->invoke(strtoupper($method)))->isEqualTo($return)
				->array($mockController->getCalls($method))->hasSize(2)
				->array($mockController->getCalls(strtoupper($method)))->hasSize(2)
				->variable($mockController->getCalls('__call'))->isNull()
				->variable($mockController->getCalls(strtoupper('__call')))->isNull()
			->if($mockController->control($mock = new \mock\mageekguy\atoum\tests\units\mock\foo()))
			->and($mockController->bar = function() {})
			->and($mock->bar($arg = uniqid(), $otherArg = uniqid()))
			->then
				->array($mockController->getCalls('bar'))->hasSize(1)
				->array($mockController->getCalls(strtoupper('bar')))->hasSize(1)
				->array($mockController->getCalls('__call'))->hasSize(1)
				->array($mockController->getCalls(strtoupper('__call')))->hasSize(1)
				->mock($mock)->call('bar')->withArguments($arg)->once()
				->mock($mock)->call('__call')->withArguments('bar', array($arg, $otherArg))->once()
		;
	}

	/** @php 5.4 */
	public function test__setAndBindToMock()
	{
		$this
			->if($mockController = new testedClass())
			->and($mockController->control($mock = new \mock\mageekguy\atoum\tests\units\mock\foo()))
			->and($mockController->doSomething = function() use (& $public) { $this->public = $public = uniqid(); })
			->and($mock->doSomething())
			->then
				->string($mock->public)->isEqualTo($public)
			->if($mockController = new testedClass())
			->and($mockController->__construct = function() use (& $public) { $this->public = $public = uniqid(); })
			->and($mock = new \mock\mageekguy\atoum\tests\units\mock\foo())
			->then
				->string($mock->public)->isEqualTo($public)
			->if($mockController = new testedClass())
			->and($mockController->__construct = function() use (& $public) { $this->public = $public = uniqid(); })
			->and($mock = new \mock\mageekguy\atoum\tests\units\mock\foo($mockController))
			->then
				->string($mock->public)->isEqualTo($public)
			->if($mockController->disableAutoBind())
			->and($mock = new \mock\mageekguy\atoum\tests\units\mock\foo($mockController))
			->then
				->variable($mock->public)->isNull()
			->if(testedClass::disableAutoBindForNewMock())
			->and($mock = new \mock\mageekguy\atoum\tests\units\mock\foo($mockController))
			->then
				->variable($mock->public)->isNull()
			->if($mockController = new testedClass())
			->and($mockController->__construct = function() use (& $public) { $this->public = $public = uniqid(); })
			->and($mockController->enableAutoBind())
			->and($mock = new \mock\mageekguy\atoum\tests\units\mock\foo($mockController))
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
			->and($mockController->doSomething = function() { return $this; })
			->and($mock = new \mock\mageekguy\atoum\tests\units\mock\foo($mockController))
			->then
				->object($mockController->enableAutoBind())->isIdenticalTo($mockController)
				->boolean($mockController->autoBindIsEnabled())->isTrue()
				->object($mock->doSomething())->isIdenticalTo($mock)
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
			->and($mockController->doSomething = function() { return $this; })
			->and($mock = new \mock\mageekguy\atoum\tests\units\mock\foo($mockController))
			->then
				->object($mockController->disableAutoBind())->isIdenticalTo($mockController)
				->boolean($mockController->autoBindIsEnabled())->isFalse()
				->boolean(isset($mockController->doSomething))->isFalse()
		;
	}

	public function test__isset()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->boolean(isset($mockController->{uniqid()}))->isFalse()
				->boolean(isset($mockController->{strtoupper(uniqid())}))->isFalse()
			->if($mockController->{$method = uniqid()} = function() {})
			->then
				->boolean(isset($mockController->{uniqid()}))->isFalse()
				->boolean(isset($mockController->{strtoupper(uniqid())}))->isFalse()
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
		;
	}

	public function getMockClass()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->variable($mockController->getMockClass())->isNull()
			->if($mockController->control($foo = new \mock\foo()))
			->then
				->string($mockController->getMockClass())->isEqualTo(get_class($foo))
		;
	}

	public function testGetMethods()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->array($mockController->getMethods())->isEmpty()
			->if($mockController->control($foo = new \mock\foo()))
			->then
				->array($mockController->getMethods())->isEqualTo($foo->getMockedMethods())
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
			->and($mockController->control(new \mock\mageekguy\atoum\tests\units\mock\foo()))
			->then
				->object($mockController->methodsMatching('/Else$/i'))->isEqualTo($mockController->getIterator())
				->array($mockController->getIterator()->getMethods())->isEqualTo(array('dosomethingelse'))
				->object($mockController->methodsMatching('/^doSomething/i'))->isEqualTo($mockController->getIterator())
				->array($mockController->getIterator()->getMethods())->isEqualTo(array('dosomething', 'dosomethingelse'))
		;
	}

	public function testDoNothing()
	{
		$this
			->if($mock = new \mock\mageekguy\atoum\tests\units\mock\foo())
			->and($this->calling($mock)->doSomething->doNothing())
			->then
				->variable($mock->doSomething())->isNull()
		;
	}

	public function testDoSomething()
	{
		$this
			->if($mock = new \mock\mageekguy\atoum\tests\units\mock\foo())
			->and($this->calling($mock)->doSomething->doNothing())
			->and($this->calling($mock)->doSomething->doSomething())
			->then
				->string($mock->doSomething())->isEqualTo('something done')
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
				->array($mockController->getCalls())->isEmpty()
				->object($mockController->control($aMock))->isIdenticalTo($mockController)
				->string($mockController->getMockClass())->isEqualTo(get_class($aMock))
				->array($mockController->getInvokers())->hasSize(sizeof(\mock\reflectionClass::getMockedMethods()))
				->array($mockController->getMethods())->isEqualTo(\mock\reflectionClass::getMockedMethods())
				->array($mockController->getCalls())->isEmpty()
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
			->if($mockController = new testedClass())
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
				->array($mockController->getCalls())->isEmpty()
				->string($mockController->invoke('test'))->isEqualTo($return)
				->sizeOf($mockController->getCalls())->isEqualTo(1)
				->array($mockController->getCalls('test'))->isEqualTo(array(1 => array()))
				->array($mockController->getCalls('TEST'))->isEqualTo(array(1 => array()))
				->string($mockController->invoke('test', array($argument1)))->isEqualTo($return)
				->array($mockController->getCalls())->isEqualTo(array(
						'test' => array(
							1 => array(),
							2 => array($argument1)
						)
					)
				)
				->array($mockController->getCalls('test'))->isEqualTo(array(
						1 => array(),
						2 => array($argument1)
					)
				)
				->array($mockController->getCalls('TEST'))->isEqualTo(array(
						1 => array(),
						2 => array($argument1)
					)
				)
				->string($mockController->invoke('test', array($argument1, $argument2)))->isEqualTo($return)
				->array($mockController->getCalls())->isEqualTo(array(
						'test' => array(
							1 => array(),
							2 => array($argument1),
							3 => array($argument1, $argument2)
						)
					)
				)
				->array($mockController->getCalls('test'))->isEqualTo(array(
						1 => array(),
						2 => array($argument1),
						3 => array($argument1, $argument2)
					)
				)
				->array($mockController->getCalls('TEST'))->isEqualTo(array(
						1 => array(),
						2 => array($argument1),
						3 => array($argument1, $argument2)
					)
				)
				->string($mockController->invoke('test', array($argument1, $argument2, $argument3)))->isEqualTo($return)
				->array($mockController->getCalls())->isEqualTo(array(
						'test' => array(
							1 => array(),
							2 => array($argument1),
							3 => array($argument1, $argument2),
							4 => array($argument1, $argument2, $argument3)
						)
					)
				)
				->array($mockController->getCalls('test'))->isEqualTo(array(
						1 => array(),
						2 => array($argument1),
						3 => array($argument1, $argument2),
						4 => array($argument1, $argument2, $argument3)
					)
				)
				->array($mockController->getCalls('TEST'))->isEqualTo(array(
						1 => array(),
						2 => array($argument1),
						3 => array($argument1, $argument2),
						4 => array($argument1, $argument2, $argument3)
					)
				)
				->if($mockController->test2 = function() use ($return) { return $return; })
				->then
					->string($mockController->invoke('test2', array($argument1)))->isEqualTo($return)
					->array($mockController->getCalls())->isEqualTo(array(
							'test' => array(
								1 => array(),
								2 => array($argument1),
								3 => array($argument1, $argument2),
								4 => array($argument1, $argument2, $argument3)
							),
							'test2' => array(
								5 => array($argument1)
							)
						)
					)
					->array($mockController->getCalls('test2'))->isEqualTo(array(
							5 => array($argument1)
						)
					)
					->array($mockController->getCalls('TEST2'))->isEqualTo(array(
							5 => array($argument1)
						)
					)
					->string($mockController->invoke('test2', array($argument1, $argument2)))->isEqualTo($return)
					->array($mockController->getCalls())->isEqualTo(array(
							'test' => array(
								1 => array(),
								2 => array($argument1),
								3 => array($argument1, $argument2),
								4 => array($argument1, $argument2, $argument3)
							),
							'test2' => array(
								5 => array($argument1),
								6 => array($argument1, $argument2)
							)
						)
					)
					->array($mockController->getCalls('test2'))->isEqualTo(array(
							5 => array($argument1),
							6 => array($argument1, $argument2)
						)
					)
					->array($mockController->getCalls('TEST2'))->isEqualTo(array(
							5 => array($argument1),
							6 => array($argument1, $argument2)
						)
					)
					->string($mockController->invoke('test2', array($argument1, $argument2, $argument3)))->isEqualTo($return)
					->array($mockController->getCalls())->isEqualTo(array(
							'test' => array(
								1 => array(),
								2 => array($argument1),
								3 => array($argument1, $argument2),
								4 => array($argument1, $argument2, $argument3)
							),
							'test2' => array(
								5 => array($argument1),
								6 => array($argument1, $argument2),
								7 => array($argument1, $argument2, $argument3)
							)
						)
					)
					->array($mockController->getCalls('test2'))->isEqualTo(array(
							5 => array($argument1),
							6 => array($argument1, $argument2),
							7 => array($argument1, $argument2, $argument3)
						)
					)
					->array($mockController->getCalls('TEST2'))->isEqualTo(array(
							5 => array($argument1),
							6 => array($argument1, $argument2),
							7 => array($argument1, $argument2, $argument3)
						)
					)
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
			->then
				->object($mockController->controlNextNewMock())->isIdenticalTo($mockController)
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
				->array($mockController->getCalls())->isEmpty()
				->object($mockController->reset())->isIdenticalTo($mockController)
				->variable($mockController->getMockClass())->isNull()
				->array($mockController->getInvokers())->isEmpty()
				->array($mockController->getMethods())->isEmpty()
				->array($mockController->getCalls())->isEmpty()
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
				->array($mockController->getCalls())->isNotEmpty()
				->object($mockController->reset())->isIdenticalTo($mockController)
				->variable($mockController->getMockClass())->isNull()
				->array($mockController->getInvokers())->isEmpty()
				->array($mockController->getMethods())->isEmpty()
				->array($mockController->getCalls())->isEmpty()
		;
	}

	public function testGetCalls()
	{
		$this
			->if($mockController = new testedClass())
			->then
				->array($mockController->getCalls())->isEmpty()
			->if($mockController->{$method = uniqid()} = function($arg) {})
			->then
				->array($mockController->getCalls())->isEmpty()
			->if($mockController->invoke($method, array($arg = uniqid())))
			->then
				->array($mockController->getCalls())->isEqualTo(array($method => array(1 => array($arg))))
				->array($mockController->getCalls($method))->isEqualTo(array(1 => array($arg)))
				->array($mockController->getCalls(strtoupper($method)))->isEqualTo(array(1 => array($arg)))
				->array($mockController->getCalls($method, array($arg)))->isEqualTo(array(1 => array($arg)))
				->array($mockController->getCalls(strtoupper($method), array($arg)))->isEqualTo(array(1 => array($arg)))
			->if($mockController->invoke($method, array($otherArg = uniqid())))
			->then
				->array($mockController->getCalls())->isEqualTo(array($method => array(1 => array($arg), 2 => array($otherArg))))
				->array($mockController->getCalls($method))->isEqualTo(array(1 => array($arg), 2 => array($otherArg)))
				->array($mockController->getCalls(strtoupper($method)))->isEqualTo(array(1 => array($arg), 2 => array($otherArg)))
				->array($mockController->getCalls($method, array($arg)))->isEqualTo(array(1 => array($arg)))
				->array($mockController->getCalls(strtoupper($method), array($arg)))->isEqualTo(array(1 => array($arg)))
				->array($mockController->getCalls($method, array($otherArg)))->isEqualTo(array(2 => array($otherArg)))
				->array($mockController->getCalls(strtoupper($method), array($otherArg)))->isEqualTo(array(2 => array($otherArg)))
		;
	}

	public function testResetCalls()
	{
		$this
			->if($mockController = new testedClass())
			->and($mockController->{$method = uniqid()} = function() {})
			->then
				->array($mockController->getCalls())->isEmpty()
			->if($mockController->invoke($method, array()))
			->then
				->array($mockController->getCalls())->isNotEmpty()
				->object($mockController->resetCalls())->isIdenticalTo($mockController)
				->array($mockController->getCalls())->isEmpty()
		;
	}

	public function testGetForMock()
	{
		$this
			->if($mockController = new testedClass())
			->and($mock = new \mock\foo())
			->and($mockController->control($mock))
			->then
				->object(testedClass::getForMock($mock))->isIdenticalTo($mockController)
			->if($mockController->bar = function() {})
			->then
				->object(testedClass::getForMock($mock))->isIdenticalTo($mockController)
		;
	}
}
