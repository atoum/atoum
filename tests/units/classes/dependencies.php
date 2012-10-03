<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mageekguy\atoum\dependencies as testedClass
;

require_once __DIR__ . '/../runner.php';

class dependencies extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->hasInterface('arrayAccess');
	}

	public function test__construct()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->variable($dependencies->getValue())->isNull()
		;
	}

	public function test__invoke()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->exception(function() use ($dependencies) { $dependencies(); })
					->isInstanceOf('mageekguy\atoum\dependencies\exception')
					->hasMessage('Value is undefined')
			->if($dependencies->setValue($value = uniqid()))
			->then
				->string($dependencies())->isEqualTo($value)
			->if($dependencies->setValue(function() use (& $value) { return ($value = uniqid()); }))
			->then
				->string($dependencies())->isEqualTo($value)
			->if($dependencies->setValue(function($dependencies) { return $dependencies; }))
			->then
				->object($dependencies())->isIdenticalTo($dependencies)
			->if($dependencies->setValue(function($dependencies) { return $dependencies['argument'](); }))
			->then
				->string($dependencies(array('argument' => $argument = uniqid())))->isEqualTo($argument)
		;
	}

	public function testSetInjector()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->object($dependencies->setValue($injector = function() {}))->isIdenticalTo($dependencies)
				->object($dependencies->getValue())->isIdenticalTo($injector)
				->object($dependencies->setValue($injector = uniqid()))->isIdenticalTo($dependencies)
				->string($dependencies->getValue())->isIdenticalTo($injector)
		;
	}

	public function testGetInjector()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->variable($dependencies->getValue())->isNull()
				->variable($dependencies->getValue(uniqid()))->isNull()
			->if($dependencies->setValue($injector = uniqid()))
			->then
				->string($dependencies->getValue())->isEqualTo($injector)
				->variable($dependencies->getValue(uniqid()))->isNull()
			->if($dependencies->setDependence($name = uniqid(), $otherInjector = uniqid()))
			->then
				->string($dependencies->getValue())->isEqualTo($injector)
				->variable($dependencies->getValue(uniqid()))->isNull()
				->string($dependencies->getValue($name))->isEqualTo($otherInjector)
		;
	}

	public function testSetDependence()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->object($dependencies->setDependence($name = uniqid(), $value = uniqid()))->isIdenticalTo($dependencies)
				->object($dependencies->getDependence($name))->isInstanceOf($dependencies)
				->string($dependencies->getValue($name))->isEqualTo($value)
				->object($dependencies->setDependence($otherName = uniqid(), $subDependence = new testedClass()))->isIdenticalTo($dependencies)
				->object($dependencies->getDependence($name))->isInstanceOf($dependencies)
				->string($dependencies->getValue($name))->isEqualTo($value)
				->object($dependencies->getDependence($otherName))->isIdenticalTo($subDependence)
		;
	}

	public function testGetDependence()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->object($dependencies->getDependence(uniqid()))->isInstanceOf($dependencies)
			->if($dependencies->setDependence($name = uniqid(), uniqid()))
			->then
				->object($dependencies->getDependence(uniqid()))->isInstanceOf($dependencies)
				->object($dependencies->getDependence($name))->isInstanceOf($dependencies)
		;
	}

	public function testDependenceExists()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->boolean($dependencies->dependenceExists(uniqid()))->isFalse()
			->if($dependencies->setDependence($name = uniqid(), uniqid()))
			->then
				->boolean($dependencies->dependenceExists(uniqid()))->isFalse()
				->boolean($dependencies->dependenceExists($name))->isTrue()
		;
	}

	public function testUnsetDependence()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->object($dependencies->unsetDependence(uniqid()))->isIdenticalTo($dependencies)
			->if($dependencies->setDependence($name = uniqid(), uniqid()))
			->then
				->object($dependencies->unsetDependence(uniqid()))->isIdenticalTo($dependencies)
				->object($dependencies->unsetDependence($name))->isIdenticalTo($dependencies)
				->boolean($dependencies->dependenceExists($name))->isFalse()
		;
	}

	public function testOffsetSet()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->object($dependencies->offsetSet($name = uniqid(), $value = uniqid()))->isIdenticalTo($dependencies)
				->object($dependencies->getDependence($name))->isInstanceOf($dependencies)
				->string($dependencies->getValue($name))->isEqualTo($value)
				->object($dependencies->offsetSet($otherName = uniqid(), $subDependence = new testedClass()))->isIdenticalTo($dependencies)
				->object($dependencies->getDependence($name))->isInstanceOf($dependencies)
				->string($dependencies->getValue($name))->isEqualTo($value)
				->object($dependencies->getDependence($otherName))->isIdenticalTo($subDependence)
		;
	}

	public function testOffsetGet()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->object($dependencies->offsetGet(uniqid()))->isInstanceOf($dependencies)
			->if($dependencies->setDependence($name = uniqid(), uniqid()))
			->then
				->object($dependencies->offsetGet(uniqid()))->isInstanceOf($dependencies)
				->object($dependencies->offsetGet($name))->isInstanceOf($dependencies)
		;
	}

	public function testOffsetExists()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->boolean($dependencies->offsetExists(uniqid()))->isFalse()
			->if($dependencies->setDependence($name = uniqid(), uniqid()))
			->then
				->boolean($dependencies->offsetExists(uniqid()))->isFalse()
				->boolean($dependencies->offsetExists($name))->isTrue()
		;
	}

	public function testOffsetUnset()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->object($dependencies->offsetUnset(uniqid()))->isIdenticalTo($dependencies)
			->if($dependencies->setDependence($name = uniqid(), uniqid()))
			->then
				->object($dependencies->offsetUnset(uniqid()))->isIdenticalTo($dependencies)
				->object($dependencies->offsetUnset($name))->isIdenticalTo($dependencies)
				->boolean($dependencies->dependenceExists($name))->isFalse()
		;
	}
}
