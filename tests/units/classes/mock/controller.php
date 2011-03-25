<?php

namespace mageekguy\atoum\tests\units\mock;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;

require_once(__DIR__ . '/../../runner.php');

class controller extends atoum\test
{
	public function test__construct()
	{
		$mockController = new mock\controller();

		$this->assert
			->variable($mockController->getMockClass())->isNull()
			->array($mockController->getMethods())->isEmpty()
			->array($mockController->getCalls())->isEmpty()
		;
	}

	public function test_set()
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
			->variable($mockController->{uniqid()})->isNull()
		;

		$mockController->{$method = uniqid()} = $function = function() {};

		$this->assert
			->variable($mockController->{uniqid()})->isNull()
			->object($mockController->{$method})->isIdenticalTo($function)
		;

		$mockController->{$otherMethod = uniqid()} = $return = uniqid();

		$this->assert
			->variable($mockController->{uniqid()})->isNull()
			->object($mockController->{$method})->isIdenticalTo($function)
			->object($mockController->{$otherMethod})->isInstanceOf('\closure')
			->string($mockController->{$otherMethod}->__invoke())->isEqualTo($return)
		;
	}

	public function test__unset()
	{
		$mockController = new mock\controller();

		$this->assert
			->variable($mockController->{uniqid()})->isNull()
		;

		$mockController->{$method = uniqid()} = function() {};

		$this->assert
			->variable($mockController->{$method})->isNotNull()
		;

		unset($mockController->{$method});

		$this->assert
			->variable($mockController->{$method})->isNull()
		;

		$mockController->{$otherMethod = uniqid()} = uniqid();

		$this->assert
			->variable($mockController->{$otherMethod})->isNotNull()
		;

		unset($mockController->{$otherMethod});

		$this->assert
			->variable($mockController->{$otherMethod})->isNull()
		;
	}

	public function testSetReflectionClassInjector()
	{
		$mockController = new mock\controller();

		$mockGenerator = new mock\generator();
		$mockGenerator->shunt('__construct')->generate('\reflectionClass');

		$this->assert
			->object($mockController->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new mock\reflectionClass($class)); }))->isIdenticalTo($mockController)
			->object($mockController->getReflectionClass($class = uniqid()))->isIdenticalTo($reflectionClass)
			->exception(function() use ($mockController) {
					$mockController->setReflectionClassInjector(function() {});
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Reflection class injector must take one argument')
		;
	}

	public function testGetReflectionClass()
	{
		$mockController = new mock\controller();

		$this->assert
			->object($mockController->getReflectionClass(__CLASS__))->isInstanceOf('\reflectionClass')
			->string($mockController->getReflectionClass(__CLASS__)->getName())->isEqualTo(__CLASS__)
		;

		$mockGenerator = new mock\generator();
		$mockGenerator->shunt('__construct')->generate('\reflectionClass');

		$mockController->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new mock\reflectionClass($class)); });

		$this->assert
			->object($mockController->getReflectionClass($class = uniqid()))->isIdenticalTo($reflectionClass)
			->mock($reflectionClass)->call('__construct', array($class))
		;

		$mockController->setReflectionClassInjector(function($class) use (& $reflectionClass) { return uniqid(); });

		$this->assert
			->exception(function() use ($mockController) {
						$mockController->getReflectionClass(uniqid());
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\runtime\unexpectedValue')
					->hasMessage('Reflection class injector must return a \reflectionClass instance')
		;
	}

	public function testControl()
	{
		$mockController = new mock\controller();

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\reflectionMethod')
			->generate('\reflectionClass')
		;

		$mockAggregatorController = new mock\controller();
		$mockAggregatorController->__construct = function() {};
		$mockAggregatorController->getName = function() { return 'mageekguy\atoum\mock\aggregator'; };
		$mockAggregator = new \mageekguy\atoum\mock\reflectionClass(uniqid(), $mockAggregatorController);

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

		$methods[\reflectionMethod::IS_PUBLIC][] = new \mageekguy\atoum\mock\reflectionMethod('setMockController');

		$getMockController = new mock\controller();
		$getMockController->__construct = function() {};
		$getMockController->getName = function() { return 'getMockController'; };
		$getMockController->getPrototype = function() use ($mockAggregator) { return $mockAggregator; };
		$getMockController->injectInNextMockInstance();

		$methods[\reflectionMethod::IS_PUBLIC][] = new \mageekguy\atoum\mock\reflectionMethod('getMockController');

		$resetMockController = new mock\controller();
		$resetMockController->__construct = function() {};
		$resetMockController->getName = function() { return 'resetMockController'; };
		$resetMockController->getPrototype = function() use ($mockAggregator) { return $mockAggregator; };
		$resetMockController->injectInNextMockInstance();

		$methods[\reflectionMethod::IS_PUBLIC][] = new \mageekguy\atoum\mock\reflectionMethod('resetMockController');

		$protectedMethodController = new mock\controller();
		$protectedMethodController->__construct = function() {};
		$protectedMethodController->getName = function() { return 'protected'; };
		$protectedMethodController->getPrototype = function() { throw new \exception(); };
		$protectedMethodController->injectInNextMockInstance();

		$methods[\reflectionMethod::IS_PROTECTED][] = new \mageekguy\atoum\mock\reflectionMethod('protectedMethod');

		$privateMethodController = new mock\controller();
		$privateMethodController->__construct = function() {};
		$privateMethodController->getName = function() { return 'private'; };
		$privateMethodController->getPrototype = function() { throw new \exception(); };
		$privateMethodController->injectInNextMockInstance();

		$methods[\reflectionMethod::IS_PRIVATE][] = new \mageekguy\atoum\mock\reflectionMethod('privateMethod');

		$aMethodController = new mock\controller();
		$aMethodController->__construct = function() {};
		$aMethodController->getName = function() { return 'a'; };
		$aMethodController->getPrototype = function() { throw new \exception(); };
		$aMethodController->injectInNextMockInstance();

		$methods[\reflectionMethod::IS_PUBLIC][] = new \mageekguy\atoum\mock\reflectionMethod('a');

		$bMethodController = new mock\controller();
		$bMethodController->__construct = function() {};
		$bMethodController->getName = function() { return 'b'; };
		$bMethodController->getPrototype = function() { throw new \exception(); };
		$bMethodController->injectInNextMockInstance();

		$methods[\reflectionMethod::IS_PUBLIC][] = new \mageekguy\atoum\mock\reflectionMethod('b');

		$reflectionClassInjectorController = new mock\controller();
		$reflectionClassInjectorController->__construct = function() {};
		$reflectionClassInjectorController->getMethods = function($modifier) use ($methods) { return $methods[$modifier]; };

		$reflectionClassInjector = new \mageekguy\atoum\mock\reflectionClass(uniqid(), $reflectionClassInjectorController);

		$mockController->setReflectionClassInjector(function ($class) use ($reflectionClassInjector) { return $reflectionClassInjector; });

		$aMockController = new mock\controller();
		$aMockController->__construct = function() {};

		$aMock = new \mageekguy\atoum\mock\reflectionClass(uniqid(), $aMockController);

		$this->assert
			->variable($mockController->getMockClass())->isNull()
			->array($mockController->getMethods())->isEmpty()
			->array($mockController->getCalls())->isEmpty()
			->object($mockController->control($aMock))->isIdenticalTo($mockController)
			->string($mockController->getMockClass())->isEqualTo(get_class($aMock))
			->array($mockController->getMethods())->isEqualTo(array(
					'a' => null,
					'b' => null
				)
			)
			->array($mockController->getCalls())->isEmpty()
		;

		$mockController = new mock\controller();
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
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
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
			->sizeof($mockController->getCalls())->isEqualTo(1)
			->array($mockController->getCalls('test'))->isEqualTo(array(array()))
			->string($mockController->invoke('test', array($argument1)))->isEqualTo($return)
			->array($mockController->getCalls())->isEqualTo(array(
					'test' => array(
						array(),
						array($argument1)
					)
				)
			)
			->array($mockController->getCalls('test'))->isEqualTo(array(
					array(),
					array($argument1)
				)
			)
			->string($mockController->invoke('test', array($argument1, $argument2)))->isEqualTo($return)
			->array($mockController->getCalls())->isEqualTo(array(
					'test' => array(
						array(),
						array($argument1),
						array($argument1, $argument2)
					)
				)
			)
			->array($mockController->getCalls('test'))->isEqualTo(array(
					array(),
					array($argument1),
					array($argument1, $argument2)
				)
			)
			->string($mockController->invoke('test', array($argument1, $argument2, $argument3)))->isEqualTo($return)
			->array($mockController->getCalls())->isEqualTo(array(
					'test' => array(
						array(),
						array($argument1),
						array($argument1, $argument2),
						array($argument1, $argument2, $argument3)
					)
				)
			)
			->array($mockController->getCalls('test'))->isEqualTo(array(
					array(),
					array($argument1),
					array($argument1, $argument2),
					array($argument1, $argument2, $argument3)
				)
			)
		;

		$mockController->test2 = function() use ($return) { return $return; };

		$this->assert
			->string($mockController->invoke('test2', array($argument1)))->isEqualTo($return)
			->array($mockController->getCalls())->isEqualTo(array(
					'test' => array(
						array(),
						array($argument1),
						array($argument1, $argument2),
						array($argument1, $argument2, $argument3)
					),
					'test2' => array(
						array($argument1)
					)
				)
			)
			->array($mockController->getCalls('test2'))->isEqualTo(array(
					array($argument1)
				)
			)
			->string($mockController->invoke('test2', array($argument1, $argument2)))->isEqualTo($return)
			->array($mockController->getCalls())->isEqualTo(array(
					'test' => array(
						array(),
						array($argument1),
						array($argument1, $argument2),
						array($argument1, $argument2, $argument3)
					),
					'test2' => array(
						array($argument1),
						array($argument1, $argument2)
					)
				)
			)
			->array($mockController->getCalls('test2'))->isEqualTo(array(
					array($argument1),
					array($argument1, $argument2)
				)
			)
			->string($mockController->invoke('test2', array($argument1, $argument2, $argument3)))->isEqualTo($return)
			->array($mockController->getCalls())->isEqualTo(array(
					'test' => array(
						array(),
						array($argument1),
						array($argument1, $argument2),
						array($argument1, $argument2, $argument3)
					),
					'test2' => array(
						array($argument1),
						array($argument1, $argument2),
						array($argument1, $argument2, $argument3)
					)
				)
			)
			->array($mockController->getCalls('test2'))->isEqualTo(array(
					array($argument1),
					array($argument1, $argument2),
					array($argument1, $argument2, $argument3)
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
			->array($mockController->getMethods())->isEmpty()
			->array($mockController->getCalls())->isEmpty()
			->object($mockController->reset())->isIdenticalTo($mockController)
			->variable($mockController->getMockClass())->isNull()
			->array($mockController->getMethods())->isEmpty()
			->array($mockController->getCalls())->isEmpty()
		;

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate(__CLASS__)
		;

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$mock = new mock\mageekguy\atoum\tests\units\mock\controller(null, null, $adapter);
		$mockController->control($mock);

		$mockController->{__FUNCTION__} = function() {};
		$mockController->invoke(__FUNCTION__, array());

		$this->assert
			->variable($mockController->getMockClass())->isNotNull()
			->array($mockController->getMethods())->isNotEmpty()
			->array($mockController->getCalls())->isNotEmpty()
			->object($mockController->reset())->isIdenticalTo($mockController)
			->variable($mockController->getMockClass())->isNull()
			->array($mockController->getMethods())->isEmpty()
			->array($mockController->getCalls())->isEmpty()
		;
	}

	public function testGetCalls()
	{
		$mockController = new mock\controller();

		$this->assert
			->array($mockController->getCalls())->isEmpty()
		;

		$mockController->{$method = uniqid()} = function($arg) {};

		$this->assert
			->array($mockController->getCalls())->isEmpty()
		;

		$mockController->invoke($method, array($arg = uniqid()));

		$this->assert
			->array($mockController->getCalls())->isEqualTo(array($method => array(array($arg))))
			->array($mockController->getCalls($method))->isEqualTo(array(array($arg)))
			->exception(function() use ($mockController, & $unmockedMethod) {
						$mockController->getCalls($unmockedMethod = uniqid());
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Method \'' . $unmockedMethod . '\' is not mocked')
		;
	}

	public function testResetCalls()
	{
		$mockController = new mock\controller();
		$mockController->{$method = uniqid()} = function() {};
		$mockController->invoke($method, array());

		$this->assert
			->array($mockController->getCalls())->isNotEmpty()
			->object($mockController->resetCalls())->isIdenticalTo($mockController)
			->array($mockController->getCalls())->isEmpty()
		;
	}

	public function testAtCall()
	{
		$mockController = new mock\controller();

		$this->setCase('Call is equal to 0');

		$this->assert
			->exception(function() use ($mockController) {
					$mockController->atCall(0);
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to 1')
		;

		$this->setCase('Call is equal to 0');

		$this->assert
			->exception(function() use ($mockController) {
					$mockController->atCall(- rand(1, PHP_INT_MAX));
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Call number must be greater than or equal to 1')
		;

		$mockController->{$method = uniqid()} = $return = uniqid();

		$this->setCase('Call is equal to 1');

		$mockController->atCall(1)->{$method} = $returnAt1 = uniqid();

		$this->assert
			->string($mockController->invoke($method))->isEqualTo($returnAt1)
			->string($mockController->invoke($method))->isEqualTo($return)
		;

		unset($mockController->resetCalls()->atCall(1)->{$method});

		$mockController->resetCalls();

		$this->setCase('Call is greater than 1');

		$mockController->atCall(2)->{$method} = $returnAt2 = uniqid();
		$mockController->atCall(3)->{$method} = $returnAt3 = uniqid();

		$this->assert
			->string($mockController->invoke($method))->isEqualTo($return)
			->string($mockController->invoke($method))->isEqualTo($returnAt2)
			->string($mockController->invoke($method))->isEqualTo($returnAt3)
			->string($mockController->invoke($method))->isEqualTo($return)
		;
	}
}

?>
