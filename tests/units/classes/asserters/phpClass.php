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

	public function test__toString()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->castToString($this->testedInstance)->isEmpty

			->if(
				$this->testedInstance
					->setReflectionClassInjector(function($class) {
							$mockController = new atoum\mock\controller();
							$mockController->__construct->doesNothing();
							$mockController->getName = $class;

							return new \mock\reflectionClass($class, $mockController);
						}
					),
				$this->testedInstance->setWith($class = uniqid())
			)
			->then
				->castToString($this->testedInstance)->isEqualTo($class)
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
				->mock($reflectionClass)->call('__construct')->withArguments($class)->once

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
			->given($asserter = $this->newTestedInstance)

			->if(
				$this->testedInstance
					->setReflectionClassInjector(function($class) {
							$mockController = new atoum\mock\controller();
							$mockController->__construct = function() { throw new \reflectionException();};

							return new \mock\reflectionClass($class, $mockController);
						}
					)
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $notExists = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $class) { $asserter->setWith($class = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notExists)
				->mock($locale)->call('_')->withArguments('Class \'%s\' does not exist', $class)->once

			->if(
				$this->testedInstance
					->setReflectionClassInjector(function($class) {
							$mockController = new atoum\mock\controller();
							$mockController->__construct->doesNothing();
							$mockController->getName = $class;

							return new \mock\reflectionClass($class, $mockController);
						}
					)
			)
			->then
				->object($this->testedInstance->setWith($class = uniqid()))->isTestedInstance
				->string($this->testedInstance->getClass())->isEqualTo($class)
		;
	}

	public function testHasParent()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->hasParent(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

			->if(
				$this->testedInstance
					->setReflectionClassInjector(function($class) use (& $parent) {
							$parentMockController = new atoum\mock\controller();
							$parentMockController->getName = $parent = uniqid();

							$mockController = new atoum\mock\controller();
							$mockController->getName = $class;
							$mockController->getParentClass = new \mock\reflectionClass(uniqid(), $parentMockController);

							return new \mock\reflectionClass($class, $mockController);
						}
					)
					->setWith(uniqid())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $isNotChild = uniqid()
			)
			->then
				->exception(function() use ($asserter, & $notParent) { $asserter->hasParent($notParent = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($isNotChild)
				->mock($locale)->call('_')->withArguments('%s is not the parent of class %s', $notParent, $asserter)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->hasParent(uniqid(), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->object($this->testedInstance->hasParent($parent))->isTestedInstance
			->object($this->testedInstance->hasParent(strtoupper($parent)))->isTestedInstance
		;
	}

	public function testHasNoParent()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->hasNoParent(); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

				->exception(function() use ($asserter) { $asserter->hasNoParent; })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

			->if(
				$reflectionClass = new \mock\reflectionClass($className = uniqid()),
				$this->calling($reflectionClass)->getName = $className,
				$this->calling($reflectionClass)->getParentClass = false,

				$asserter
					->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
					->setWith($class = uniqid())
			)
			->then
				->object($asserter->hasNoParent())->isIdenticalTo($asserter)
				->object($asserter->hasNoParent)->isIdenticalTo($asserter)

			->if(
				$this->calling($reflectionClass)->getParentClass = $parentClass = new \mock\reflectionClass(uniqid()),
				$this->calling($parentClass)->__toString = $parentClassName = uniqid(),
				$this->testedInstance->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $hasParent = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->hasNoParent(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($hasParent)
				->mock($locale)->call('_')->withArguments('%s has parent %s', $asserter, $parentClass)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->hasNoParent($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->exception(function() use ($asserter) { $asserter->hasNoParent; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($hasParent)
				->mock($locale)->call('_')->withArguments('%s has parent %s', $asserter, $parentClass)->twice
		;
	}

	public function testIsSubclassOf()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->isSubclassOf(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

				->exception(function() use ($asserter) { $asserter->extends(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

				->exception(function() use ($asserter) { $asserter->exTENDs(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

			->given(
				$this->testedInstance
					->setReflectionClassInjector(function($class) use (& $reflectionClass) {
							$mockController = new atoum\mock\controller();
							$mockController->__construct = function() {};
							$mockController->getName = $class;

							return $reflectionClass = new \mock\reflectionClass($class, $mockController);
						}
					)
					->setWith(uniqid())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $isNotSubclass = uniqid()
			)

			->if($this->calling($reflectionClass)->isSubclassOf->throw = $exception = new \reflectionException())
			->then
				->exception(function() use ($asserter) { $asserter->isSubclassOf(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Argument of ' . $this->getTestedClassName() . '::isSubClassOf() must be a class name')
					->hasNestedException($exception)
		
			->if($this->calling($reflectionClass)->isSubclassOf = false)
			->then
				->exception(function() use ($asserter, & $parentClass) { $asserter->isSubclassOf($parentClass = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($isNotSubclass)
				->mock($locale)->call('_')->withArguments('%s does not extend %s', $asserter, $parentClass)->once

				->exception(function() use ($asserter, & $parentClass) { $asserter->extends($parentClass = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($isNotSubclass)
				->mock($locale)->call('_')->withArguments('%s does not extend %s', $asserter, $parentClass)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->isSubclassOf(uniqid(), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->exception(function() use ($asserter, & $failMessage) { $asserter->extends(uniqid(), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($this->calling($reflectionClass)->isSubclassOf = true)
			->then
				->object($this->testedInstance->isSubclassOf(uniqid()))->isTestedInstance
				->object($this->testedInstance->extends(uniqid()))->isTestedInstance
		;
	}

	public function testHasInterface()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->hasInterface(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

				->exception(function() use ($asserter) { $asserter->implements(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

				->exception(function() use ($asserter) { $asserter->imPLEMENts(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

			->given(
				$this->testedInstance
					->setReflectionClassInjector(function($class) use (& $reflectionClass) {
							$mockController = new atoum\mock\controller();
							$mockController->__construct = function() {};
							$mockController->getName = $class;

							return $reflectionClass = new \mock\reflectionClass($class, $mockController);
						}
					)
					->setWith(uniqid())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $notImplements = uniqid()
			)

			->if($this->calling($reflectionClass)->implementsInterface->throw = $exception = new \reflectionException())
			->then
				->exception(function() use ($asserter) { $asserter->hasInterface(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Argument of ' . $this->getTestedClassName() . '::hasInterface() must be an interface name')
					->hasNestedException($exception)

			->if($this->calling($reflectionClass)->implementsInterface = false)
			->then
				->exception(function() use ($asserter, & $interface) { $asserter->hasInterface($interface = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notImplements)
				->mock($locale)->call('_')->withArguments('%s does not implement %s', $asserter, $interface)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->hasInterface(uniqid(), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->exception(function() use ($asserter, & $interface) { $asserter->implements($interface = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notImplements)
				->mock($locale)->call('_')->withArguments('%s does not implement %s', $asserter, $interface)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->implements(uniqid(), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($this->calling($reflectionClass)->implementsInterface = true)
			->then
				->object($this->testedInstance->hasInterface(uniqid()))->isTestedInstance
				->object($this->testedInstance->implements(uniqid()))->isTestedInstance
		;
	}

	public function testIsAbstract()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->isAbstract(); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

				->exception(function() use ($asserter) { $asserter->isAbstract; })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

			->given(
				$this->testedInstance
					->setReflectionClassInjector(function($class) use (& $reflectionClass) {
							$mockController = new atoum\mock\controller();
							$mockController->__construct = function() {};
							$mockController->getName = $class;

							return $reflectionClass = new \mock\reflectionClass($class, $mockController);
						}
					)
					->setWith(uniqid())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $notAbstract = uniqid()
			)

			->if($this->calling($reflectionClass)->isAbstract = false)
			->then
				->exception(function() use ($asserter) { $asserter->isAbstract(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notAbstract)
				->mock($locale)->call('_')->withArguments('%s is not abstract', $asserter)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->isAbstract($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->exception(function() use ($asserter) { $asserter->isAbstract; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notAbstract)
				->mock($locale)->call('_')->withArguments('%s is not abstract', $asserter)->twice

			->if($this->calling($reflectionClass)->isAbstract = true)
				->object($this->testedInstance->isAbstract())->isTestedInstance
				->object($this->testedInstance->isAbstract)->isTestedInstance
		;
	}

	public function testIsFinal()
	{
		$this
			->given($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->isFinal(); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

			->given(
				$this->testedInstance
					->setReflectionClassInjector(function($class) use (& $reflectionClass) {
							$mockController = new atoum\mock\controller();
							$mockController->__construct = function() {};
							$mockController->getName = $class;

							return $reflectionClass = new \mock\reflectionClass($class, $mockController);
						}
					)
					->setWith(uniqid())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $notFinal = uniqid()
			)

			->if($this->calling($reflectionClass)->isFinal = false)
			->then
				->exception(function() use ($asserter) { $asserter->isFinal(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notFinal)
				->mock($locale)->call('_')->withArguments('%s is not final', $asserter)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->isFinal($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

				->exception(function() use ($asserter) { $asserter->isFinal; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notFinal)
				->mock($locale)->call('_')->withArguments('%s is not final', $asserter)->twice

			->if($this->calling($reflectionClass)->isFinal = true)
				->object($this->testedInstance->isFinal())->isTestedInstance
				->object($this->testedInstance->isFinal)->isTestedInstance
		;
	}

	public function testHasMethod()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->hasMethod(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

			->given(
				$this->testedInstance
					->setReflectionClassInjector(function($class) use (& $reflectionClass) {
							$mockController = new atoum\mock\controller();
							$mockController->__construct = function() {};
							$mockController->getName = $class;

							return $reflectionClass = new \mock\reflectionClass($class, $mockController);
						}
					)
					->setWith(uniqid())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $methodUnknown = uniqid()
			)

			->if($this->calling($reflectionClass)->hasMethod = false)
			->then
				->exception(function() use ($asserter, & $method) { $asserter->hasMethod($method = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($methodUnknown)
				->mock($locale)->call('_')->withArguments('%s::%s() does not exist', $asserter, $method)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->hasMethod(uniqid(), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($this->calling($reflectionClass)->hasMethod = true)
			->then
				->object($this->testedInstance->hasMethod(uniqid()))->isTestedInstance
		;
	}

	public function testHasConstant()
	{
		$this
			->if($asserter = $this->newTestedInstance)
			->then
				->exception(function() use ($asserter) { $asserter->hasConstant(uniqid()); })
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')

			->given(
				$this->testedInstance
					->setReflectionClassInjector(function($class) use (& $reflectionClass) {
							$mockController = new atoum\mock\controller();
							$mockController->__construct = function() {};
							$mockController->getName = $class;

							return $reflectionClass = new \mock\reflectionClass($class, $mockController);
						}
					)
					->setWith(uniqid())
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $constantUnknown = uniqid()
			)

			->if($this->calling($reflectionClass)->hasConstant = false)
			->then
				->exception(function() use ($asserter, & $constant) { $asserter->hasConstant($constant = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($constantUnknown)
				->mock($locale)->call('_')->withArguments('%s::%s does not exist', $asserter, $constant)->once

				->exception(function() use ($asserter, & $failMessage) { $asserter->hasConstant(uniqid(), $failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if(
				$this->calling($reflectionClass)->hasConstant = true,
				$this->calling($reflectionClass)->getConstant = $constantObject = uniqid(),
				$this->testedInstance->setGenerator($generator = new \mock\atoum\asserter\generator()),
				$this->calling($generator)->getAsserterInstance = $asserter = uniqid()
			)
			->then
				->string($this->testedInstance->hasConstant($constant = uniqid()))->isEqualTo($asserter)
				->mock($generator)->call('getAsserterInstance')->withArguments('constant', array($constantObject))->once
		;
	}
}
