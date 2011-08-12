<?php

namespace mageekguy\atoum\tests\units\mock;

use
	mageekguy\atoum,
	mageekguy\atoum\mock
;

require_once(__DIR__ . '/../../runner.php');

class controller extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubclassOf('mageekguy\atoum\test\adapter')
		;
	}

	public function test__construct()
	{
		$mockController = new mock\controller();

		$this->assert
			->variable($mockController->getMockClass())->isNull()
			->array($mockController->getCallables())->isEmpty()
			->array($mockController->getCalls())->isEmpty()
		;
	}

	public function test__set()
	{
		$mockController = new mock\controller();

		$return = uniqid();

		$mockController->{$method = uniqid()} = function() use ($return) { return $return; };

		$this->assert
			->string($mockController->invoke($method))->isEqualTo($return)
		;

		$mockController->{$method = uniqid()} = $return = uniqid();

		$this->assert
			->string($mockController->invoke($method))->isEqualTo($return)
		;
	}

	public function test__isset()
	{
		$mockController = new mock\controller();

		$this->assert
			->boolean(isset($mockController->{uniqid()}))->isFalse()
		;

		$mockController->{$method = uniqid()} = function() {};

		$this->assert
			->boolean(isset($mockController->{uniqid()}))->isFalse()
			->boolean(isset($mockController->{$method}))->isTrue()
		;
	}

	public function test__get()
	{
		$mockController = new mock\controller();

		$this->assert
			->object($mockController->{uniqid()})->isInstanceOf('mageekguy\atoum\test\adapter\callable')
		;

		$mockController->{$method = uniqid()} = $function = function() {};

		$this->assert
			->object($mockController->{uniqid()})->isInstanceOf('mageekguy\atoum\test\adapter\callable')
			->object($mockController->{$method}->getClosure())->isIdenticalTo($function)
		;

		$mockController->{$otherMethod = uniqid()} = $return = uniqid();

		$this->assert
			->object($mockController->{uniqid()})->isInstanceOf('mageekguy\atoum\test\adapter\callable')
			->object($mockController->{$method}->getClosure())->isIdenticalTo($function)
			->object($mockController->{$otherMethod}->getClosure())->isInstanceOf('closure')
			->string($mockController->{$otherMethod}->invoke())->isEqualTo($return)
		;
	}

	public function test__unset()
	{
		$mockController = new mock\controller();

		$this->assert
			->boolean(isset($mockController->{$method = uniqid()}))->isFalse()
			->when(function() use ($mockController, $method) { $mockController->{$method} = uniqid(); })
				->boolean(isset($mockController->{$method}))->isTrue()
			->when(function() use ($mockController, $method) { unset($mockController->{$method}); })
				->boolean(isset($mockController->{$method}))->isFalse()
		;

		$this->mockGenerator
			->generate('reflectionClass')
		;

		$reflectionClass = new \mock\reflectionClass($this);

		$mockController = new mock\controller();
		$mockController->control($reflectionClass);

		$this->assert
			->boolean(isset($mockController->getMethods))->isFalse()
			->when(function() use ($mockController) { $mockController->getMethods = null; })
				->boolean(isset($mockController->getMethods))->isTrue()
			->when(function() use ($mockController) { unset($mockController->getMethods); })
				->boolean(isset($mockController->getMethods))->isFalse()
		;
	}

	public function testSetReflectionClassInjector()
	{
		$mockController = new mock\controller();

		$this->mockGenerator
			->shunt('__construct')
			->generate('reflectionClass')
		;

		$this->assert
			->object($mockController->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new \mock\reflectionClass($class)); }))->isIdenticalTo($mockController)
			->object($mockController->getReflectionClass($class = uniqid()))->isIdenticalTo($reflectionClass)
			->exception(function() use ($mockController) {
					$mockController->setReflectionClassInjector(function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Reflection class injector must take one argument')
		;
	}

	public function testGetReflectionClass()
	{
		$mockController = new mock\controller();

		$this->assert
			->object($mockController->getReflectionClass(__CLASS__))->isInstanceOf('reflectionClass')
			->string($mockController->getReflectionClass(__CLASS__)->getName())->isEqualTo(__CLASS__)
		;

		$this->mockGenerator
			->shunt('__construct')
			->generate('reflectionClass')
		;

		$mockController->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new \mock\reflectionClass($class)); });

		$this->assert
			->object($mockController->getReflectionClass($class = uniqid()))->isIdenticalTo($reflectionClass)
			->mock($reflectionClass)->call('__construct')->withArguments($class)->once()
		;

		$mockController->setReflectionClassInjector(function($class) use (& $reflectionClass) { return uniqid(); });

		$this->assert
			->exception(function() use ($mockController) {
						$mockController->getReflectionClass(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime\unexpectedValue')
					->hasMessage('Reflection class injector must return a \reflectionClass instance')
		;
	}

	public function testControl()
	{
		$mockController = new mock\controller();

		$this->mockGenerator
			->generate('reflectionMethod')
			->generate('reflectionClass')
		;

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
		$setMockController->injectInNextMockInstance();

		$methods[\reflectionMethod::IS_PUBLIC][] = new \mock\reflectionMethod('setMockController');

		$getMockController = new mock\controller();
		$getMockController->__construct = function() {};
		$getMockController->getName = function() { return 'getMockController'; };
		$getMockController->getPrototype = function() use ($mockAggregator) { return $mockAggregator; };
		$getMockController->injectInNextMockInstance();

		$methods[\reflectionMethod::IS_PUBLIC][] = new \mock\reflectionMethod('getMockController');

		$resetMockController = new mock\controller();
		$resetMockController->__construct = function() {};
		$resetMockController->getName = function() { return 'resetMockController'; };
		$resetMockController->getPrototype = function() use ($mockAggregator) { return $mockAggregator; };
		$resetMockController->injectInNextMockInstance();

		$methods[\reflectionMethod::IS_PUBLIC][] = new \mock\reflectionMethod('resetMockController');

		$protectedMethodController = new mock\controller();
		$protectedMethodController->__construct = function() {};
		$protectedMethodController->getName = function() { return 'protected'; };
		$protectedMethodController->getPrototype = function() { throw new \exception(); };
		$protectedMethodController->injectInNextMockInstance();

		$methods[\reflectionMethod::IS_PROTECTED][] = new \mock\reflectionMethod('protectedMethod');

		$privateMethodController = new mock\controller();
		$privateMethodController->__construct = function() {};
		$privateMethodController->getName = function() { return 'private'; };
		$privateMethodController->getPrototype = function() { throw new \exception(); };
		$privateMethodController->injectInNextMockInstance();

		$methods[\reflectionMethod::IS_PRIVATE][] = new \mock\reflectionMethod('privateMethod');

		$aMethodController = new mock\controller();
		$aMethodController->__construct = function() {};
		$aMethodController->getName = function() { return 'a'; };
		$aMethodController->getPrototype = function() { throw new \exception(); };
		$aMethodController->injectInNextMockInstance();

		$methods[\reflectionMethod::IS_PUBLIC][] = new \mock\reflectionMethod('a');

		$bMethodController = new mock\controller();
		$bMethodController->__construct = function() {};
		$bMethodController->getName = function() { return 'b'; };
		$bMethodController->getPrototype = function() { throw new \exception(); };
		$bMethodController->injectInNextMockInstance();

		$methods[\reflectionMethod::IS_PUBLIC][] = new \mock\reflectionMethod('b');

		$reflectionClassInjectorController = new mock\controller();
		$reflectionClassInjectorController->__construct = function() {};
		$reflectionClassInjectorController->getMethods = function($modifier) use ($methods) { return $methods[$modifier]; };

		$reflectionClassInjector = new \mock\reflectionClass(uniqid(), $reflectionClassInjectorController);

		$mockController->setReflectionClassInjector(function ($class) use ($reflectionClassInjector) { return $reflectionClassInjector; });

		$aMockController = new mock\controller();
		$aMockController->__construct = function() {};

		$aMock = new \mock\reflectionClass(uniqid(), $aMockController);

		$this->assert
			->variable($mockController->getMockClass())->isNull()
			->array($mockController->getCallables())->isEmpty()
			->array($mockController->getCalls())->isEmpty()
			->object($mockController->control($aMock))->isIdenticalTo($mockController)
			->string($mockController->getMockClass())->isEqualTo(get_class($aMock))
			->array($mockController->getCallables())->isEqualTo(array(
					'a' => null,
					'b' => null
				)
			)
			->array($mockController->getCalls())->isEmpty()
		;
	}

	public function testInvoke()
	{
		$mockController = new mock\controller();

		$method = uniqid();

		$this->assert
			->exception(function() use ($mockController, $method) {
					$mockController->invoke($method, array());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Method ' . $method . '() is not under control')
		;

		$return = uniqid();

		$mockController->test = function() use ($return) { return $return; };

		$argument1 = uniqid();
		$argument2 = uniqid();
		$argument3 = uniqid();

		$this->assert
			->array($mockController->getCalls())->isEmpty()
			->string($mockController->invoke('test'))->isEqualTo($return)
			->sizeOf($mockController->getCalls())->isEqualTo(1)
			->array($mockController->getCalls('test'))->isEqualTo(array(1 => array()))
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
			->when(function() use ($mockController, $return) { $mockController->test2 = function() use ($return) { return $return; }; })
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
		;
	}

	public function testGet()
	{
		$this->assert
			->variable(mock\controller::get())->isNull()
		;

		$mockController = new mock\controller();

		$this->assert
			->object($mockController->injectInNextMockInstance())->isIdenticalTo($mockController)
			->object(mock\controller::get())->isIdenticalTo($mockController)
			->variable(mock\controller::get())->isNull()
		;
	}

	public function testReset()
	{
		$mockController = new mock\controller();

		$this->assert
			->variable($mockController->getMockClass())->isNull()
			->array($mockController->getCallables())->isEmpty()
			->array($mockController->getCalls())->isEmpty()
			->object($mockController->reset())->isIdenticalTo($mockController)
			->variable($mockController->getMockClass())->isNull()
			->array($mockController->getCallables())->isEmpty()
			->array($mockController->getCalls())->isEmpty()
		;

		$this->mockGenerator
			->generate(__CLASS__)
		;

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$mock = new \mock\mageekguy\atoum\tests\units\mock\controller(null, null, $adapter);
		$mockController->control($mock);

		$mockController->{$method = __FUNCTION__} = function() {};

		$this->assert
			->when(function() use ($mockController, $method) { $mockController->invoke($method, array()); })
				->variable($mockController->getMockClass())->isNotNull()
				->array($mockController->getCallables())->isNotEmpty()
				->array($mockController->getCalls())->isNotEmpty()
				->object($mockController->reset())->isIdenticalTo($mockController)
				->variable($mockController->getMockClass())->isNull()
				->array($mockController->getCallables())->isEmpty()
				->array($mockController->getCalls())->isEmpty()
		;
	}

	public function testGetCalls()
	{
		$mockController = new mock\controller();

		$this->assert
			->array($mockController->getCalls())->isEmpty()
			->when(function() use ($mockController, & $method) { $mockController->{$method = uniqid()} = function($arg) {}; })
				->array($mockController->getCalls())->isEmpty()
			->when(function() use ($mockController, $method, & $arg) { $mockController->invoke($method, array($arg = uniqid())); })
				->array($mockController->getCalls())->isEqualTo(array($method => array(1 => array($arg))))
				->array($mockController->getCalls($method))->isEqualTo(array(1 => array($arg)))
				->array($mockController->getCalls($method, array($arg)))->isEqualTo(array(1 => array($arg)))
			->when(function() use ($mockController, $method, & $otherArg) { $mockController->invoke($method, array($otherArg = uniqid())); })
				->array($mockController->getCalls())->isEqualTo(array($method => array(1 => array($arg), 2 => array($otherArg))))
				->array($mockController->getCalls($method))->isEqualTo(array(1 => array($arg), 2 => array($otherArg)))
				->array($mockController->getCalls($method, array($arg)))->isEqualTo(array(1 => array($arg)))
				->array($mockController->getCalls($method, array($otherArg)))->isEqualTo(array(2 => array($otherArg)))
		;
	}

	public function testResetCalls()
	{
		$mockController = new mock\controller();
		$mockController->{$method = uniqid()} = function() {};

		$this->assert
			->array($mockController->getCalls())->isEmpty()
			->when(function() use ($mockController, $method) { $mockController->invoke($method, array()); })
				->array($mockController->getCalls())->isNotEmpty()
				->object($mockController->resetCalls())->isIdenticalTo($mockController)
				->array($mockController->getCalls())->isEmpty()
		;
	}
}

?>
