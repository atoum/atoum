<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserters;

require_once(__DIR__ . '/../../runner.php');

class phpClass extends atoum\test
{
	public function test__construct()
	{
		$asserter = new asserters\phpClass($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->object($asserter)->isInstanceOf('\mageekguy\atoum\asserter')
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetReflectionClassInjecter()
	{
		$asserter = new asserters\phpClass(new atoum\score(), new atoum\locale());

		$this->assert
			->exception(function() use ($asserter) {
					$asserter->setReflectionClassInjecter(function() {});
				}
			)
				->isInstanceOf('\runtimeException')
				->hasMessage('Reflection class injecter must take one argument')
		;

		$reflectionClass = new \reflectionClass($this);

		$this->assert
			->object($asserter->getReflectionClass(__CLASS__))->isInstanceOf('\reflectionClass')
			->object($asserter->setReflectionClassInjecter(function($class) use ($reflectionClass) { return $reflectionClass; }))->isIdenticalTo($asserter)
			->object($asserter->getReflectionClass(__CLASS__))->isIdenticalTo($reflectionClass)
		;
	}

	public function testGetClass()
	{
		$asserter = new asserters\phpClass(new atoum\score(), new atoum\locale());

		$this->assert
			->variable($asserter->getClass())->isNull()
		;

		$asserter->setWith(__CLASS__);

		$this->assert
			->string($asserter->getClass())->isEqualTo(__CLASS__)
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\phpClass($score = new atoum\score(), $locale = new atoum\locale());

		$mockGenerator = new atoum\mock\generator();
		$mockGenerator->generate('\reflectionClass');
		$mockController = new atoum\mock\controller();
		$mockController->__construct = function() { throw new \reflectionException();};

		$asserter->setReflectionClassInjecter(function($class) use ($mockController) { return new atoum\mock\reflectionClass($class, $mockController); });

		$class = uniqid();

		$this->assert
			->exception(function() use ($asserter, $class) {
					$asserter->setWith($class);
				}
			)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not a class'), $class))
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isZero()
		;

		$asserter = new asserters\phpClass($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->object($asserter->setWith(__CLASS__))->isIdenticalTo($asserter)
			->string($asserter->getClass())->isEqualTo(__CLASS__)
			->integer($score->getFailNumber())->isZero()
			->integer($score->getPassNumber())->isEqualTo(1)
		;
	}

	public function testHasParent()
	{
		$asserter = new asserters\phpClass($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->hasParent(uniqid());
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Class is undefined')
		;

		$class = uniqid();
		$parent = uniqid();

		$mockGenerator = new atoum\mock\generator();
		$mockGenerator->generate('\reflectionClass');

		$mockController = new atoum\mock\controller();
		$mockController->__construct = function() {};
		$mockController->getName = function() use ($class) { return $class; };

		$asserter
			->setReflectionClassInjecter(function($class) use ($mockController) { return new atoum\mock\reflectionClass($class, $mockController); })
			->setWith($class)
				->getScore()->reset()
		;

		$parentMockController = new atoum\mock\controller();
		$parentMockController->__construct = function() {};
		$parentMockController->getName = function() { return uniqid(); };

		$mockController->getParentClass = function() use ($parent, $parentMockController) { return new atoum\mock\reflectionClass($parent, $parentMockController); };

		$this->assert
			->exception(function() use ($asserter, $parent) {
					$asserter->hasParent($parent);
				}
			)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not the parent of class %s'), $parent, $class))
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

	public function testHasInterface()
	{
		$asserter = new asserters\phpClass($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->hasInterface(uniqid());
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Class is undefined')
		;

		$class = uniqid();
		$interface = uniqid();

		$mockGenerator = new atoum\mock\generator();
		$mockGenerator->generate('\reflectionClass');

		$mockController = new atoum\mock\controller();
		$mockController->__construct = function() {};
		$mockController->getName = function() use ($class) { return $class; };

		$asserter
			->setReflectionClassInjecter(function($class) use ($mockController) { return new atoum\mock\reflectionClass($class, $mockController); })
			->setWith($class)
				->getScore()->reset()
		;

		$mockController->getInterfaceNames = function() { return array(); };

		$this->assert
			->exception(function() use ($asserter, $interface) {
					$asserter->hasInterface($interface);
				}
			)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('Class %s does not implement interface %s'), $class, $interface))
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isZero()
		;

		$mockController->getInterfaceNames = function() use ($interface) { return array(uniqid(), $interface, uniqid()); };

		$this->assert
			->object($asserter->hasInterface($interface))->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
		;
	}
}

?>
