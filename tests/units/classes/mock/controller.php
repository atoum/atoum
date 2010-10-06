<?php

namespace mageekguy\atoum\tests\units\mock;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;

require_once(__DIR__ . '/../../runner.php');

class controller extends atoum\test
{
	public function beforeTestMethod()
	{
		$this->assert->setAlias('array', 'collection');
	}

	public function test__construct()
	{
		$mockController = new mock\controller();

		$this->assert
			->variable($mockController->getMock())->isNull()
			->array($mockController->getMethods())->isEmpty()
			->array($mockController->getCalls())->isEmpty()
		;
	}

	public function test__isset()
	{
		$mockController = new mock\controller();

		$this->assert
			->boolean(isset($mockController->{uniqid()}))->isFalse()
		;

		$method = uniqid();

		$mockController->{$method} = function() {};

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

		$method = uniqid();
		$function = function() {};

		$mockController->{$method} = $function;

		$this->assert
			->variable($mockController->{uniqid()})->isNull()
			->object($mockController->{$method})->isIdenticalTo($function)
		;
	}

	public function test__unset()
	{
		$mockController = new mock\controller();

		$method = uniqid();

		$this->assert
			->variable($mockController->{$method})->isNull()
		;

		unset($mockController->{$method});

		$this->assert
			->variable($mockController->{$method})->isNull()
		;

		$mockController->{$method} = function() {};

		$this->assert
			->variable($mockController->{$method})->isNotNull()
		;

		unset($mockController->{$method});

		$this->assert
			->variable($mockController->{$method})->isNull()
		;
	}

	public function testSetReflectionClassInjecter()
	{
		$mockController = new mock\controller();

		$this->assert
			->exception(function() use ($mockController) {
					$mockController->setReflectionClassInjecter(function() {});
				}
			)
				->isInstanceOf('\runtimeException')
				->hasMessage('Reflection class injecter must take one argument')
		;

		$reflectionClass = new \reflectionClass($this);

		$this->assert
			->object($mockController->getReflectionClass(__CLASS__))->isInstanceOf('\reflectionClass')
			->object($mockController->setReflectionClassInjecter(function($class) use ($reflectionClass) { return $reflectionClass; }))->isIdenticalTo($mockController)
			->object($mockController->getReflectionClass(__CLASS__))->isIdenticalTo($reflectionClass)
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

		$reflectionClassInjecterController = new mock\controller();
		$reflectionClassInjecterController->__construct = function() {};
		$reflectionClassInjecterController->getMethods = function($modifier) use ($methods) { return $methods[$modifier]; };

		$reflectionClassInjecter = new \mageekguy\atoum\mock\reflectionClass(uniqid(), $reflectionClassInjecterController);

		$mockController->setReflectionClassInjecter(function ($class) use ($reflectionClassInjecter) { return $reflectionClassInjecter; });

		$aMockController = new mock\controller();
		$aMockController->__construct = function() {};

		$aMock = new \mageekguy\atoum\mock\reflectionClass(uniqid(), $aMockController);

		$this->assert
			->variable($mockController->getMock())->isNull()
			->array($mockController->getMethods())->isEmpty()
			->array($mockController->getCalls())->isEmpty()
			->object($mockController->control($aMock))->isIdenticalTo($mockController)
			->object($mockController->getMock())->isIdenticalTo($aMock)
			->array($mockController->getMethods())->isEqualTo(array(
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
				->isInstanceOf('\logicException')
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
			->variable($mockController->getMock())->isNull()
			->array($mockController->getMethods())->isEmpty()
			->array($mockController->getCalls())->isEmpty()
			->object($mockController->reset())->isIdenticalTo($mockController)
			->variable($mockController->getMock())->isNull()
			->array($mockController->getMethods())->isEmpty()
			->array($mockController->getCalls())->isEmpty()
		;

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate(__CLASS__)
		;

		$mock = new \mageekguy\atoum\mock\mageekguy\atoum\tests\units\mock\controller();
		$mockController->control($mock);

		$mockController->{__FUNCTION__} = function() {};
		$mockController->invoke(__FUNCTION__, array());

		$this->assert
			->variable($mockController->getMock())->isNotNull()
			->array($mockController->getMethods())->isNotEmpty()
			->array($mockController->getCalls())->isNotEmpty()
			->object($mockController->reset())->isIdenticalTo($mockController)
			->variable($mockController->getMock())->isNull()
			->array($mockController->getMethods())->isEmpty()
			->array($mockController->getCalls())->isEmpty()
		;
	}
}

?>
