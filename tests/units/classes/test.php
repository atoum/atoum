<?php

namespace mageekguy\atoum\tests\units\test;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;

require_once(__DIR__ . '/../runner.php');

/** @ignore on */
class emptyTest extends atoum\test
{
}

/** @ignore on */
class notEmptyTest extends atoum\test
{
	public function testMethod1() {}

	/** @ignore off */
	public function testMethod2() {}
}

/** @isolation off */
class test extends atoum\test
{
	public function setUp()
	{
		$this->assert->setAlias('array', 'collection');
	}

	public function test__construct()
	{
		$test = new emptyTest();

		$this->assert
			->object($test->getScore())->isInstanceOf('\mageekguy\atoum\score')
			->object($test->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->object($test->getAsserterGenerator())->isInstanceOf('\mageekguy\atoum\asserter\generator')
			->boolean($test->isIgnored())->isTrue()
		;

		$score = new atoum\score();
		$locale = new atoum\locale();

		$test = new emptyTest($score, $locale);

		$this->assert
			->object($test->getScore())->isIdenticalTo($score)
			->object($test->getLocale())->isIdenticalTo($locale)
			->boolean($test->isIgnored())->isTrue()
		;

		$test = new self();

		$this->assert
			->object($test->getScore())->isInstanceOf('\mageekguy\atoum\score')
			->object($test->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->boolean($test->isIgnored())->isFalse()
		;

		$test = new self($score, $locale);

		$this->assert
			->object($test->getScore())->isIdenticalTo($score)
			->object($test->getLocale())->isIdenticalTo($locale)
			->boolean($test->isIgnored())->isFalse()
		;
	}

	public function testSetLocale()
	{
		$test = new emptyTest();

		$locale = new atoum\locale();

		$this->assert
			->object($test->getLocale())->isNotIdenticalTo($locale)
			->object($test->setLocale($locale))->isIdenticalTo($test)
			->object($test->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetScore()
	{
		$test = new emptyTest();

		$score = new atoum\score();

		$this->assert
			->object($test->getScore())->isNotIdenticalTo($score)
			->object($test->setScore($score))->isIdenticalTo($test)
			->object($test->getScore())->isIdenticalTo($score)
		;
	}

	public function testSetAsserterGenerator()
	{
		$test = new emptyTest();

		$asserterGenerator = new atoum\asserter\generator(new atoum\score(), new atoum\locale());

		$this->assert
			->object($asserterGenerator->getScore())->isNotIdenticalTo($test->getScore())
			->object($asserterGenerator->getLocale())->isNotIdenticalTo($test->getLocale())
			->object($test->getAsserterGenerator())->isNotIdenticalTo($asserterGenerator)
			->object($test->setAsserterGenerator($asserterGenerator))->isIdenticalTo($test)
			->object($test->getAsserterGenerator())->isIdenticalTo($asserterGenerator)
			->object($test->getAsserterGenerator()->getScore())->isIdenticalTo($test->getScore())
			->object($test->getAsserterGenerator()->getLocale())->isIdenticalTo($test->getLocale())
		;
	}

	public function testGetClass()
	{
		$test = new emptyTest();

		$this->assert
			->string($test->getClass())->isEqualTo(__NAMESPACE__ . '\emptyTest')
		;
	}

	public function testGetPath()
	{
		$test = new emptyTest();

		$this->assert
			->string($test->getPath())->isEqualTo(__FILE__)
		;
	}

	public function testIgnore()
	{
		$test = new emptyTest();

		$this->assert
			->boolean($test->isIgnored())->isTrue()
			->object($test->ignore(false))->isIdenticalTo($test)
			->boolean($test->isIgnored())->isFalse()
			->object($test->ignore(true))->isIdenticalTo($test)
			->boolean($test->isIgnored())->isTrue()
		;
	}

	public function testIsolate()
	{
		$test = new emptyTest();

		$this->assert
			->boolean($test->isIsolated())->isTrue()
			->object($test->isolate(false))->isIdenticalTo($test)
			->boolean($test->isIsolated())->isFalse()
			->object($test->isolate(true))->isIdenticalTo($test)
			->boolean($test->isIsolated())->isTrue()
		;
	}

	public function testGetCurrentMethod()
	{
		$test = new emptyTest();

		$this->assert
			->variable($test->getCurrentMethod())->isNull()
		;
	}

	public function testCount()
	{
		$this->assert
			->sizeof(new emptyTest())->isEqualTo(0)
		;

		$test = new notEmptyTest();

		$this->assert
			->boolean($test->isIgnored())->isTrue()
			->boolean($test->methodIsIgnored('testMethod1'))->isTrue()
			->boolean($test->methodIsIgnored('testMethod2'))->isFalse()
			->sizeof($test)->isEqualTo(1)
			->sizeof($test->ignore(false))->isEqualTo(2)
		;
	}

	public function testGetTestMethods()
	{
		$test = new emptyTest();

		$this->assert
			->boolean($test->ignore(false)->isIgnored())->isFalse()
			->sizeof($test)->isZero()
			->array($test->getTestMethods())->isEmpty()
		;

		$test = new notEmptyTest();

		$this->assert
			->boolean($test->isIgnored())->isTrue()
			->boolean($test->methodIsIgnored('testMethod1'))->isTrue()
			->boolean($test->methodIsIgnored('testMethod2'))->isFalse()
			->sizeof($test)->isEqualTo(1)
			->array($test->getTestMethods())->isEqualTo(array('testMethod2'))
			->boolean($test->ignore(false)->isIgnored())->isFalse()
			->boolean($test->methodIsIgnored('testMethod1'))->isFalse()
			->boolean($test->methodIsIgnored('testMethod2'))->isFalse()
			->sizeof($test)->isEqualTo(2)
			->array($test->getTestMethods())->isEqualTo(array('testMethod1', 'testMethod2'))
		;
	}

	public function testIgnoreMethod()
	{
		$test = new notEmptyTest();

		$this->assert
			->boolean($test->methodIsIgnored('testMethod1'))->isTrue()
			->boolean($test->methodIsIgnored('testMethod2'))->isFalse()
			->boolean($test->ignore(false)->methodIsIgnored('testMethod1'))->isFalse()
			->boolean($test->methodIsIgnored('testMethod2'))->isFalse()
		;
	}

	public function testRun()
	{
		$registryController = new mock\controller();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\registry');

		$registry = \mageekguy\atoum\mock\mageekguy\atoum\registry::getInstance();
		$registry->setMockController($registryController);

		$registryController->__set = function() {};
		$registryController->__get = function() {};
		$registryController->__unset = function() {};

		$test = new emptyTest();
		$test->setRegistryInjecter(function() use ($registry) { return $registry; });

		$this->assert
			->object($test->run())->isIdenticalTo($test)
			->mock($registry)
				->call('__set', array(atoum\test::getRegistryKey(), array($test)))
				->call('__unset', array(atoum\test::getRegistryKey()))
		;
	}
}

?>
