<?php

namespace mageekguy\atoum\tests\units\mock;

use
	mageekguy\atoum,
	mageekguy\atoum\mock
;

require_once __DIR__ . '/../../runner.php';

class controller extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\test\adapter');
	}

	public function test__construct()
	{
		$this
			->if($mockController = new mock\controller())
			->then
				->object($mockController->getFactory())->isInstanceOf('mageekguy\atoum\factory')
				->variable($mockController->getMockClass())->isNull()
				->array($mockController->getInvokers())->isEmpty()
				->array($mockController->getCalls())->isEmpty()
				->object($mockController->getFactory())->isInstanceOf('mageekguy\atoum\factory')
		;
	}

	public function test__set()
	{
		$this
			->if($mockController = new mock\controller())
			->and($mockController->{$method = uniqid()} = function() use (& $return) { return $return = uniqid(); })
			->then
				->string($mockController->invoke($method))->isEqualTo($return)
				->string($mockController->invoke(strtoupper($method)))->isEqualTo($return)
			->if($mockController->{$method = uniqid()} = $return = uniqid())
			->then
				->string($mockController->invoke($method))->isEqualTo($return)
				->string($mockController->invoke(strtoupper($method)))->isEqualTo($return)
		;
	}

	public function test__isset()
	{
		$this
			->if($mockController = new mock\controller())
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
			->if($mockController = new mock\controller())
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
			->if($mockController = new mock\controller())
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
			->and($mockController = new mock\controller())
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

	public function testControl()
	{
		$mockAggregatorController = new mock\controller();
		$mockAggregatorController->__construct = function() {};
		$mockAggregatorController->getName = function() { return 'mageekguy\atoum\mock\aggregator'; };
		$mockAggregator = new \mock\reflectionClass(uniqid(), $mockAggregatorController);

		$methods = array(
			\reflectionMethod::IS_PUBLIC => array(),
			\reflectionMethod::IS_PROTECTED => array(),
			\reflectionMethod::IS_PRIVATE => array()
		);

		$setMockController = new mock\controller();
		$setMockController->__construct = function() {};
		$setMockController->getName = function() { return 'setMockController'; };
		$setMockController->getPrototype = function() use ($mockAggregator) { return $mockAggregator; };

		$methods[\reflectionMethod::IS_PUBLIC][] = new \mock\reflectionMethod('setMockController');

		$getMockController = new mock\controller();
		$getMockController->__construct = function() {};
		$getMockController->getName = function() { return 'getMockController'; };
		$getMockController->getPrototype = function() use ($mockAggregator) { return $mockAggregator; };

		$methods[\reflectionMethod::IS_PUBLIC][] = new \mock\reflectionMethod('getMockController');

		$resetMockController = new mock\controller();
		$resetMockController->__construct = function() {};
		$resetMockController->getName = function() { return 'resetMockController'; };
		$resetMockController->getPrototype = function() use ($mockAggregator) { return $mockAggregator; };

		$methods[\reflectionMethod::IS_PUBLIC][] = new \mock\reflectionMethod('resetMockController');

		$protectedMethodController = new mock\controller();
		$protectedMethodController->__construct = function() {};
		$protectedMethodController->getName = function() { return 'protected'; };
		$protectedMethodController->getPrototype = function() { throw new \exception(); };

		$methods[\reflectionMethod::IS_PROTECTED][] = new \mock\reflectionMethod('protectedMethod');

		$privateMethodController = new mock\controller();
		$privateMethodController->__construct = function() {};
		$privateMethodController->getName = function() { return 'private'; };
		$privateMethodController->getPrototype = function() { throw new \exception(); };

		$methods[\reflectionMethod::IS_PRIVATE][] = new \mock\reflectionMethod('privateMethod');

		$aMethodController = new mock\controller();
		$aMethodController->__construct = function() {};
		$aMethodController->getName = function() { return 'a'; };
		$aMethodController->getPrototype = function() { throw new \exception(); };

		$methods[\reflectionMethod::IS_PUBLIC][] = new \mock\reflectionMethod('a');

		$bMethodController = new mock\controller();
		$bMethodController->__construct = function() {};
		$bMethodController->getName = function() { return 'b'; };
		$bMethodController->getPrototype = function() { throw new \exception(); };

		$methods[\reflectionMethod::IS_PUBLIC][] = new \mock\reflectionMethod('b');

		$reflectionClassController = new mock\controller();
		$reflectionClassController->__construct = function() {};
		$reflectionClassController->getMethods = function($modifier) use ($methods) { return $methods[$modifier]; };

		$reflectionClass = new \mock\reflectionClass(uniqid(), $reflectionClassController);


		$aMockController = new mock\controller();
		$aMockController->__construct = function() {};

		$aMock = new \mock\reflectionClass(uniqid(), $aMockController);

		$this
			->if($factory = new atoum\factory())
			->and($factory['reflectionClass'] = $reflectionClass)
			->and($mockController = new mock\controller($factory))
			->then
				->variable($mockController->getMockClass())->isNull()
				->array($mockController->getInvokers())->isEmpty()
				->array($mockController->getCalls())->isEmpty()
				->object($mockController->control($aMock))->isIdenticalTo($mockController)
				->string($mockController->getMockClass())->isEqualTo(get_class($aMock))
				->array($mockController->getInvokers())->isEqualTo(array(
						'a' => null,
						'b' => null
					)
				)
				->array($mockController->getCalls())->isEmpty()
			->if($mock = new \mock\foo())
			->and($mockController = new mock\controller())
			->and($mockController->controlNextNewMock())
			->and($mockController->control($mock))
			->then
				->variable(mock\controller::get())->isNull()
		;
	}

	public function testControlNextNewMock()
	{
		$this
			->if($mockController = new mock\controller())
			->then
				->object($mockController->controlNextNewMock())->isIdenticalTo($mockController)
				->object(mock\controller::get())->isIdenticalTo($mockController)
		;
	}

	public function testNotControlNextNewMock()
	{
		$this
			->if($mockController = new mock\controller())
			->and($mockController->controlNextNewMock())
			->then
				->object($mockController->notControlNextNewMock())->isIdenticalTo($mockController)
				->variable(mock\controller::get())->isNull()
			->if($mockController = new mock\controller())
			->and($otherMockController = new mock\controller())
			->and($otherMockController->controlNextNewMock())
			->then
				->object($mockController->notControlNextNewMock())->isIdenticalTo($mockController)
				->object(mock\controller::get())->isIdenticalTo($otherMockController)
		;
	}

	public function testInvoke()
	{
		$this
			->if($mockController = new mock\controller())
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
		;
	}

	public function testGet()
	{
		$this
				->variable(mock\controller::get())->isNull()
			->if($mockController = new mock\controller())
			->then
				->object($mockController->controlNextNewMock())->isIdenticalTo($mockController)
				->object(mock\controller::get())->isIdenticalTo($mockController)
				->variable(mock\controller::get())->isNull()
		;
	}

	public function testReset()
	{
		$this
			->if($mockController = new mock\controller())
			->then
				->variable($mockController->getMockClass())->isNull()
				->array($mockController->getInvokers())->isEmpty()
				->array($mockController->getCalls())->isEmpty()
				->object($mockController->reset())->isIdenticalTo($mockController)
				->variable($mockController->getMockClass())->isNull()
				->array($mockController->getInvokers())->isEmpty()
				->array($mockController->getCalls())->isEmpty()
			->if($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = true)
			->and($mock = new \mock\mageekguy\atoum\tests\units\mock\controller(null, null, $adapter))
			->and($mockController->control($mock))
			->and($mockController->{$method = __FUNCTION__} = function() {})
			->and($mockController->invoke($method, array()))
			->then
				->variable($mockController->getMockClass())->isNotNull()
				->array($mockController->getInvokers())->isNotEmpty()
				->array($mockController->getCalls())->isNotEmpty()
				->object($mockController->reset())->isIdenticalTo($mockController)
				->variable($mockController->getMockClass())->isNull()
				->array($mockController->getInvokers())->isEmpty()
				->array($mockController->getCalls())->isEmpty()
		;
	}

	public function testGetCalls()
	{
		$this
			->if($mockController = new mock\controller())
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
			->if($mockController = new mock\controller())
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
}
