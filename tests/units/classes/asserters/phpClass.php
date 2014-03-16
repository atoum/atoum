<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\variable
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
		$this->testedClass->extends('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->getGenerator())->isEqualTo(new asserter\generator())
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new variable\analyzer())
				->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())

			->if($this->newTestedInstance($generator = new asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
			->then
				->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
				->object($this->testedInstance->getAnalyzer())->isIdenticalTo($analyzer)
				->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testGetClass()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getClass())->isNull()

			->if($this->testedInstance->setWith(__CLASS__))
			->then
				->string($this->testedInstance->getClass())->isEqualTo(__CLASS__)
		;
	}

	public function testSetReflectionClassInjector()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->object($this->testedInstance->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new \mock\reflectionClass($class)); }))->isTestedInstance
				->object($this->testedInstance->getReflectionClass($class = uniqid()))->isIdenticalTo($reflectionClass)

				->exception(function() use ($asserter) { $asserter->setReflectionClassInjector(function() {}); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Reflection class injector must take one argument')
		;
	}

	public function testGetReflectionClass()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->object($this->testedInstance->getReflectionClass(__CLASS__))->isInstanceOf('reflectionClass')
				->string($this->testedInstance->getReflectionClass(__CLASS__)->getName())->isEqualTo(__CLASS__)

			->if($this->testedInstance->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new \mock\reflectionClass($class)); }))
			->then
				->object($this->testedInstance->getReflectionClass($class = uniqid()))->isIdenticalTo($reflectionClass)
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
			->given($asserter = $this->newTestedInstance($generator = new asserter\generator()))

			->if(
				$mockController = new atoum\mock\controller(),
				$mockController->__construct = function() { throw new \reflectionException();},
				$asserter->setReflectionClassInjector(function($class) use ($mockController) { return new \mock\reflectionClass($class, $mockController); }),
				$class = uniqid()
			)
			->then
				->exception(function() use ($asserter, $class) { $asserter->setWith($class); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Class \'%s\' does not exist'), $class))

			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setWith(__CLASS__))->isTestedInstance
				->string($this->testedInstance->getClass())->isEqualTo(__CLASS__)
		;
	}

	public function testHasParent()
	{
		$this
			->given($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasParent(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

			->if(
				$mockController = new atoum\mock\controller(),
				$parent = uniqid(),
				$mockController->getName = $class = uniqid(),
				$asserter
					->setReflectionClassInjector(function($class) use ($mockController) { return new \mock\reflectionClass($class, $mockController); })
					->setWith($class),
				$parentMockController = new atoum\mock\controller(),
				$parentMockController->getName = uniqid(),
				$mockController->getParentClass = $parentClass = new \mock\reflectionClass($parent, $parentMockController)
			)
			->then
				->exception(function() use ($asserter, $parent) { $asserter->hasParent($parent); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not the parent of class %s'), $parent, $class))

			->if($parentMockController->getName = $parent)
			->then
				->object($this->testedInstance->hasParent($parent))->isTestedInstance
				->object($this->testedInstance->hasParent(strtoupper($parent)))->isTestedInstance
		;
	}

	public function testHasNoParent()
	{
		$this
			->given($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasNoParent(); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

			->if(
				$reflectionClass = new \mock\reflectionClass($className = uniqid()),
				$asserter
					->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
					->setWith($class = uniqid()),
				$reflectionClass->getMockController()->getName = function() use ($className) { return $className; },
				$reflectionClass->getMockController()->getParentClass = function() { return false; }
			)
			->then
				->object($asserter->hasNoParent())->isIdenticalTo($asserter)

			->if(
				$parentClass = new \mock\reflectionClass($parentClassName = uniqid()),
				$parentClass->getMockController()->__toString = function() use ($parentClassName) { return $parentClassName; },
				$reflectionClass->getMockController()->getParentClass = function() use ($parentClass) { return $parentClass; }
			)
			->then
				->exception(function() use ($asserter) { $asserter->hasNoParent(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('class %s has parent %s'), $className, $parentClass))
		;
	}

	public function testIsSubclassOf()
	{
		$this
			->given($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isSubclassOf(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

			->if(
				$class = uniqid(),
				$parentClass = uniqid(),
				$mockController = new atoum\mock\controller(),
				$mockController->__construct = function() {},
				$mockController->getName = function() use ($class) { return $class; },
				$asserter
					->setReflectionClassInjector(function($class) use ($mockController) { return new \mock\reflectionClass($class, $mockController); })
					->setWith($class),
				$mockController->isSubclassOf = false
			)
			->then
				->exception(function() use ($asserter, $parentClass) { $asserter->isSubclassOf($parentClass); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Class %s is not a sub-class of %s'), $class, $parentClass))

			->if($mockController->isSubclassOf = true)
			->then
				->object($this->testedInstance->isSubclassOf($parentClass))->isTestedInstance
		;
	}

	public function testExtends()
	{
		$this
			->if($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->extends(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

			->if(
				$class = uniqid(),
				$parentClass = uniqid(),
				$mockController = new atoum\mock\controller(),
				$mockController->__construct = function() {},
				$mockController->getName = function() use ($class) { return $class; },
				$asserter
					->setReflectionClassInjector(function($class) use ($mockController) { return new \mock\reflectionClass($class, $mockController); })
					->setWith($class),
				$mockController->isSubclassOf = false
			)
			->then
				->exception(function() use ($asserter, $parentClass) { $asserter->extends($parentClass); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Class %s is not a sub-class of %s'), $class, $parentClass))

			->if($mockController->isSubclassOf = true)
			->then
				->object($this->testedInstance->extends($parentClass))->isTestedInstance
		;
	}

	public function testHasInterface()
	{
		$this
			->given($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasInterface(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

			->if(
				$class = uniqid(),
				$interface = uniqid(),
				$mockController = new atoum\mock\controller(),
				$mockController->__construct = function() {},
				$mockController->getName = function() use ($class) { return $class; },
				$asserter
					->setReflectionClassInjector(function($class) use ($mockController) { return new \mock\reflectionClass($class, $mockController); })
					->setWith($class),
				$mockController->implementsInterface = false
			)
			->then
				->exception(function() use ($asserter, $interface) { $asserter->hasInterface($interface); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Class %s does not implement interface %s'), $class, $interface))

			->if($mockController->implementsInterface = true)
			->then
				->object($this->testedInstance->hasInterface($interface))->isTestedInstance
		;
	}

	public function testImplements()
	{
		$this
			->given($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->implements(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

			->if(
				$class = uniqid(),
				$interface = uniqid(),
				$mockController = new atoum\mock\controller(),
				$mockController->__construct = function() {},
				$mockController->getName = function() use ($class) { return $class; },
				$asserter
					->setReflectionClassInjector(function($class) use ($mockController) { return new \mock\reflectionClass($class, $mockController); })
					->setWith($class),
				$mockController->implementsInterface = false
			)
			->then
				->exception(function() use ($asserter, $interface) { $asserter->implements($interface); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Class %s does not implement interface %s'), $class, $interface))

			->if($mockController->implementsInterface = true)
			->then
				->object($this->testedInstance->implements($interface))->isTestedInstance
		;
	}

	public function testIsAbstract()
	{
		$this
			->given($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isAbstract(); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

			->if(
				$class = uniqid(),
				$mockController = new atoum\mock\controller(),
				$mockController->__construct = function() {},
				$mockController->getName = function() use ($class) { return $class; },
				$asserter
					->setReflectionClassInjector(function($class) use ($mockController) { return new \mock\reflectionClass($class, $mockController); })
					->setWith($class),
				$mockController->isAbstract = false
			)
			->then
				->exception(function() use ($asserter) { $asserter->isAbstract(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Class %s is not abstract'), $class))

			->if($mockController->isAbstract = true)
			->then
				->object($this->testedInstance->isAbstract())->isTestedInstance
		;
	}

	public function testHasMethod()
	{
		$this
			->if($asserter = $this->newTestedInstance($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasMethod(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

			->if(
				$class = uniqid(),
				$method = uniqid(),
				$reflectionClass = new \mock\reflectionClass($class = uniqid()),
				$reflectionClassController = $reflectionClass->getMockController(),
				$reflectionClassController->getName = $class,
				$reflectionClassController->hasMethod = false,
				$asserter
					->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
					->setWith($class)
			)
			->then
				->exception(function() use ($asserter, $method) { $asserter->hasMethod($method); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('Method %s::%s() does not exist'), $class, $method))

			->if($reflectionClassController->hasMethod = true)
			->then
				->object($this->testedInstance->hasMethod(uniqid()))->isTestedInstance
		;
	}
}
