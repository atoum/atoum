<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class phpClass extends atoum\test
{
	public function beforeTestMethod($testMethod)
	{
		$this->mockGenerator
			->shunt('__construct')
		;
	}

	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this->assert
			->if($asserter = new asserters\phpClass($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
		;
	}

	public function testGetClass()
	{
		$this
			->if($asserter = new asserters\phpClass($generator = new asserter\generator()))
			->then
				->variable($asserter->getClass())->isNull()
			->if($asserter->setWith(__CLASS__))
			->then
				->string($asserter->getClass())->isEqualTo(__CLASS__)
		;
	}

	public function testSetReflectionClassInjector()
	{
		$this
			->if($asserter = new asserters\phpClass($generator = new asserter\generator()))
			->then
				->object($asserter->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new \mock\reflectionClass($class)); }))->isIdenticalTo($asserter)
				->object($asserter->getReflectionClass($class = uniqid()))->isIdenticalTo($reflectionClass)
				->exception(function() use ($asserter) { $asserter->setReflectionClassInjector(function() {}); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Reflection class injector must take one argument')
		;
	}

	public function testGetReflectionClass()
	{
		$this
			->if($asserter = new asserters\phpClass($generator = new asserter\generator()))
			->then
				->object($asserter->getReflectionClass(__CLASS__))->isInstanceOf('reflectionClass')
				->string($asserter->getReflectionClass(__CLASS__)->getName())->isEqualTo(__CLASS__)
			->if($asserter->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new \mock\reflectionClass($class)); }))
			->then
				->object($asserter->getReflectionClass($class = uniqid()))->isIdenticalTo($reflectionClass)
				->mock($reflectionClass)->call('__construct')->withArguments($class)->once()
			->if($asserter->setReflectionClassInjector(function($class) use (& $reflectionClass) { return uniqid(); }))
			->then
				->exception(function() use ($asserter) { $asserter->getReflectionClass(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime\unexpectedValue')
					->hasMessage('Reflection class injector must return a \reflectionClass instance')
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new asserters\phpClass($generator = new asserter\generator()))
			->and($mockController = new atoum\mock\controller())
			->and($mockController->__construct = function() { throw new \reflectionException();})
			->and($asserter->setReflectionClassInjector(function($class) use ($mockController) { return new \mock\reflectionClass($class, $mockController); }))
			->and($class = uniqid())
			->then
				->exception(function() use ($asserter, $class) { $asserter->setWith($class); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Class \'%s\' does not exist'), $class))
			->if($asserter = new asserters\phpClass($generator = new asserter\generator()))
			->then
				->object($asserter->setWith(__CLASS__))->isIdenticalTo($asserter)
				->string($asserter->getClass())->isEqualTo(__CLASS__)
		;
	}

	public function testHasParent()
	{
		$this
			->if($asserter = new asserters\phpClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasParent(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')
			->if($class = uniqid())
			->and($parent = uniqid())
			->and($mockController = new atoum\mock\controller())
			->and($mockController->getName = function() use ($class) { return $class; })
			->and($asserter
				->setReflectionClassInjector(function($class) use ($mockController) { return new \mock\reflectionClass($class, $mockController); })
				->setWith($class)
			)
			->and($parentMockController = new atoum\mock\controller())
			->and($parentMockController->getName = function() { return uniqid(); })
			->and($mockController->getParentClass = function() use ($parent, $parentMockController) { return new \mock\reflectionClass($parent, $parentMockController); })
			->then
				->exception(function() use ($asserter, $parent) { $asserter->hasParent($parent); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not the parent of class %s'), $parent, $class))
			->if($parentMockController->getName = function() use ($parent) { return $parent; })
			->then
				->object($asserter->hasParent($parent))->isIdenticalTo($asserter)
				->object($asserter->hasParent(strtoupper($parent)))->isIdenticalTo($asserter)
		;
	}

	public function testHasNoParent()
	{
		$this
			->if($asserter = new asserters\phpClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasNoParent(); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')
			->if($reflectionClass = new \mock\reflectionClass($className = uniqid()))
			->and($asserter
				->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
				->setWith($class = uniqid())
			)
			->and($reflectionClass->getMockController()->getName = function() use ($className) { return $className; })
			->and($reflectionClass->getMockController()->getParentClass = function() { return false; })
			->then
				->object($asserter->hasNoParent())->isIdenticalTo($asserter)
			->if($parentClass = new \mock\reflectionClass($parentClassName = uniqid()))
			->and($parentClass->getMockController()->__toString = function() use ($parentClassName) { return $parentClassName; })
			->and($reflectionClass->getMockController()->getParentClass = function() use ($parentClass) { return $parentClass; })
			->then
				->exception(function() use ($asserter) { $asserter->hasNoParent(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('class %s has parent %s'), $className, $parentClass))
		;
	}

	public function testIsSubclassOf()
	{
		$this
			->if($asserter = new asserters\phpClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isSubclassOf(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')
			->if($class = uniqid())
			->and($parentClass = uniqid())
			->and($mockController = new atoum\mock\controller())
			->and($mockController->__construct = function() {})
			->and($mockController->getName = function() use ($class) { return $class; })
			->and($asserter
				->setReflectionClassInjector(function($class) use ($mockController) { return new \mock\reflectionClass($class, $mockController); })
				->setWith($class)
			)
			->and($mockController->isSubclassOf = false)
			->then
				->exception(function() use ($asserter, $parentClass) { $asserter->isSubclassOf($parentClass); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Class %s is not a sub-class of %s'), $class, $parentClass))
			->if($mockController->isSubclassOf = true)
			->then
				->object($asserter->isSubclassOf($parentClass))->isIdenticalTo($asserter)
		;
	}

	public function testHasInterface()
	{
		$this
			->if($asserter = new asserters\phpClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasInterface(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')
			->if($class = uniqid())
			->and($interface = uniqid())
			->and($mockController = new atoum\mock\controller())
			->and($mockController->__construct = function() {})
			->and($mockController->getName = function() use ($class) { return $class; })
			->and($asserter
				->setReflectionClassInjector(function($class) use ($mockController) { return new \mock\reflectionClass($class, $mockController); })
				->setWith($class)
			)
			->and($mockController->implementsInterface = false)
			->then
				->exception(function() use ($asserter, $interface) { $asserter->hasInterface($interface); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Class %s does not implement interface %s'), $class, $interface))
			->if($mockController->implementsInterface = true)
			->then
				->object($asserter->hasInterface($interface))->isIdenticalTo($asserter)
		;
	}

	public function testIsAbstract()
	{
		$this
			->if($asserter = new asserters\phpClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isAbstract(); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')
			->if($class = uniqid())
			->and($mockController = new atoum\mock\controller())
			->and($mockController->__construct = function() {})
			->and($mockController->getName = function() use ($class) { return $class; })
			->and($asserter
				->setReflectionClassInjector(function($class) use ($mockController) { return new \mock\reflectionClass($class, $mockController); })
				->setWith($class)
			)
			->and($mockController->isAbstract = false)
			->then
				->exception(function() use ($asserter) { $asserter->isAbstract(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Class %s is not abstract'), $class))
			->if($mockController->isAbstract = true)
			->then
				->object($asserter->isAbstract())->isIdenticalTo($asserter)
		;
	}

	public function testHasMethod()
	{
		$this
			->if($asserter = new asserters\phpClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasMethod(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')
			->if($class = uniqid())
			->and($method = uniqid())
			->and($reflectionClass = new \mock\reflectionClass($class = uniqid()))
			->and($reflectionClassController = $reflectionClass->getMockController())
			->and($reflectionClassController->getName = $class)
			->and($reflectionClassController->hasMethod = false)
			->and($asserter
				->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
				->setWith($class)
			)
			->then
				->exception(function() use ($asserter, $method) { $asserter->hasMethod($method); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Method %s::%s() does not exist'), $class, $method))
			->if($reflectionClassController->hasMethod = true)
			->then
				->object($asserter->hasMethod(uniqid()))->isIdenticalTo($asserter)
		;
	}
}
