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
			->generate('reflectionClass')
		;
	}

	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\asserter')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\phpClass($generator = new asserter\generator($this));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($this->getScore())
			->object($asserter->getLocale())->isIdenticalTo($this->getLocale())
			->object($asserter->getGenerator())->isIdenticalTo($generator)
		;
	}

	public function testGetClass()
	{
		$asserter = new asserters\phpClass(new asserter\generator($this));

		$this->assert
			->variable($asserter->getClass())->isNull()
		;

		$asserter->setWith(__CLASS__);

		$this->assert
			->string($asserter->getClass())->isEqualTo(__CLASS__)
		;
	}

	public function testSetReflectionClassInjector()
	{
		$asserter = new asserters\phpClass(new asserter\generator($this));

		$this->assert
			->object($asserter->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new \mock\reflectionClass($class)); }))->isIdenticalTo($asserter)
			->object($asserter->getReflectionClass($class = uniqid()))->isIdenticalTo($reflectionClass)
			->exception(function() use ($asserter) {
					$asserter->setReflectionClassInjector(function() {});
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Reflection class injector must take one argument')
		;
	}

	public function testGetReflectionClass()
	{
		$asserter = new asserters\phpClass(new asserter\generator($this));

		$this->assert
			->object($asserter->getReflectionClass(__CLASS__))->isInstanceOf('reflectionClass')
			->string($asserter->getReflectionClass(__CLASS__)->getName())->isEqualTo(__CLASS__)
		;

		$asserter->setReflectionClassInjector(function($class) use (& $reflectionClass) { return ($reflectionClass = new \mock\reflectionClass($class)); });

		$this->assert
			->object($asserter->getReflectionClass($class = uniqid()))->isIdenticalTo($reflectionClass)
			->mock($reflectionClass)->call('__construct')->withArguments($class)->once()
		;

		$asserter->setReflectionClassInjector(function($class) use (& $reflectionClass) { return uniqid(); });

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->getReflectionClass(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime\unexpectedValue')
					->hasMessage('Reflection class injector must return a \reflectionClass instance')
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\phpClass(new asserter\generator($test = new self($score = new atoum\score())));

		$mockController = new atoum\mock\controller();
		$mockController->__construct = function() { throw new \reflectionException();};

		$asserter->setReflectionClassInjector(function($class) use ($mockController) { return new \mock\reflectionClass($class, $mockController); });

		$class = uniqid();

		$this->assert
			->exception(function() use ($asserter, $class) {
					$asserter->setWith($class);
				}
			)
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('Class \'%s\' does not exist'), $class))
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isZero()
		;

		$asserter = new asserters\phpClass(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->object($asserter->setWith(__CLASS__))->isIdenticalTo($asserter)
			->string($asserter->getClass())->isEqualTo(__CLASS__)
			->integer($score->getFailNumber())->isZero()
			->integer($score->getPassNumber())->isEqualTo(1)
		;
	}

	public function testHasParent()
	{
		$asserter = new asserters\phpClass(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->hasParent(uniqid());
					}
				)
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')
		;

		$class = uniqid();
		$parent = uniqid();

		$mockController = new atoum\mock\controller();
		$mockController->__construct = function() {};
		$mockController->getName = function() use ($class) { return $class; };

		$asserter
			->setReflectionClassInjector(function($class) use ($mockController) { return new \mock\reflectionClass($class, $mockController); })
			->setWith($class)
			->getScore()->reset()
		;

		$parentMockController = new atoum\mock\controller();
		$parentMockController->__construct = function() {};
		$parentMockController->getName = function() { return uniqid(); };

		$mockController->getParentClass = function() use ($parent, $parentMockController) { return new \mock\reflectionClass($parent, $parentMockController); };

		$this->assert
			->exception(function() use ($asserter, $parent) {
					$asserter->hasParent($parent);
				}
			)
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not the parent of class %s'), $parent, $class))
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isZero()
		;

		$parentMockController->getName = function() use ($parent) { return $parent; };

		$this->assert
			->object($asserter->hasParent($parent))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
		;
	}

	public function testHasNoParent()
	{
		$asserter = new asserters\phpClass(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->hasNoParent();
					}
				)
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')
		;

		$reflectionClass = new \mock\reflectionClass($className = uniqid());

		$asserter
			->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
			->setWith($class = uniqid())
			->getScore()
				->reset()
		;

		$reflectionClass->getMockController()->getName = function() use ($className) { return $className; };
		$reflectionClass->getMockController()->getParentClass = function() { return false; };

		$this->assert
			->object($asserter->hasNoParent())->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isZero()
			->integer($score->getPassNumber())->isEqualTo(1)
		;

		$parentClass = new \mock\reflectionClass($parentClassName = uniqid());
		$parentClass->getMockController()->__toString = function() use ($parentClassName) { return $parentClassName; };

		$reflectionClass->getMockController()->getParentClass = function() use ($parentClass) { return $parentClass; };

		$this->assert
			->exception(function() use ($asserter) {
					$asserter->hasNoParent();
				}
			)
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('class %s has parent %s'), $className, $parentClass))
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
		;
	}

	public function testHasInterface()
	{
		$asserter = new asserters\phpClass(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->hasInterface(uniqid());
					}
				)
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')
		;

		$class = uniqid();
		$interface = uniqid();

		$mockController = new atoum\mock\controller();
		$mockController->__construct = function() {};
		$mockController->getName = function() use ($class) { return $class; };

		$asserter
			->setReflectionClassInjector(function($class) use ($mockController) { return new \mock\reflectionClass($class, $mockController); })
			->setWith($class)
				->getScore()->reset()
		;

		$mockController->getInterfaceNames = array();

		$this->assert
			->exception(function() use ($asserter, $interface) {
					$asserter->hasInterface($interface);
				}
			)
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('Class %s does not implement interface %s'), $class, $interface))
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isZero()
		;

		$mockController->getInterfaceNames = array(uniqid(), $interface, uniqid());

		$this->assert
			->object($asserter->hasInterface($interface))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
		;
	}

	public function testIsAbstract()
	{
		$asserter = new asserters\phpClass(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->isAbstract();
					}
				)
					->isInstanceOf('logicException')
					->hasMessage('Class is undefined')
		;

		$class = uniqid();

		$mockController = new atoum\mock\controller();
		$mockController->__construct = function() {};
		$mockController->getName = function() use ($class) { return $class; };

		$asserter
			->setReflectionClassInjector(function($class) use ($mockController) { return new \mock\reflectionClass($class, $mockController); })
			->setWith($class)
				->getScore()->reset()
		;

		$mockController->isAbstract = false;

		$this->assert
			->exception(function() use ($asserter) {
					$asserter->isAbstract();
				}
			)
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('Class %s is not abstract'), $class))
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isZero()
		;

		$mockController->isAbstract = true;

		$this->assert
			->object($asserter->isAbstract())->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
		;
	}

	public function testHasMethod()
	{
		$asserter = new asserters\phpClass(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->hasMethod(uniqid());
					}
					)
			->isInstanceOf('logicException')
			->hasMessage('Class is undefined')
		;

		$class = uniqid();
		$method = uniqid();

		$reflectionClass = new \mock\reflectionClass($class = uniqid());
		$reflectionClassController = $reflectionClass->getMockController();
		$reflectionClassController->getName = $class;
		$reflectionClassController->hasMethod = false;

		$asserter
			->setReflectionClassInjector(function($class) use ($reflectionClass) { return $reflectionClass; })
			->setWith($class)
			->getScore()->reset()
		;

		$this->assert
			->exception(function() use ($asserter, $method) {
					$asserter->hasMethod($method);
				}
			)
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('Method %s::%s() does not exist'), $class, $method))
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isZero()
		;

		$reflectionClassController->hasMethod = true;

		$this->assert
			->object($asserter->hasMethod(uniqid()))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
		;
	}
}

?>
