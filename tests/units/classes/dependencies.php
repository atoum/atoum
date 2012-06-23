<?php

namespace mageekguy\atoum\tests\units;

require_once __DIR__ . '/../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\dependencies as testedClass
;

class dependencies extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->hasInterface('arrayAccess')
			->hasInterface('countable')
		;
	}

	public function test__construct()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->sizeOf($dependencies)->isZero()
				->variable($dependencies())->isNull()
			->if($return = uniqid())
			->and($dependencies = new testedClass($injector = function() use ($return) { return $return; }))
			->then
				->sizeOf($dependencies)->isZero()
				->string($dependencies())->isEqualTo($return)
			->if($dependencies = new testedClass($return = uniqid()))
			->then
				->sizeOf($dependencies)->isZero()
				->string($dependencies())->isEqualTo($return)
		;
	}

	public function test__invoke()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->variable($dependencies())->isNull()
			->if($dependencies = new testedClass(function() { return true; }))
			->then
				->boolean($dependencies())->isTrue()
			->if($dependencies = new testedClass(function($dependencies) { return $dependencies; }))
			->then
				->object($dependencies())->isIdenticalTo($dependencies)
		;
	}

	public function test__set()
	{
		$this
			->if($dependencies = new testedClass())
			->and($dependencies->{$name = uniqid()} = $value = uniqid())
			->then
				->string($dependencies->getArgument($name))->isEqualTo($value)
		;
	}

	public function test__get()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->exception(function() use ($dependencies, & $argument) { $dependencies->{$argument = uniqid()}; })
					->isInstanceOf('mageekguy\atoum\dependencies\exception')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($dependencies->setArgument($name = uniqid(), $value = uniqid()))
			->then
				->string($dependencies->{$name})->isEqualTo($value)
		;
	}

	public function test__isset()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->boolean(isset($dependencies->{uniqid()}))->isFalse()
			->if($dependencies->setArgument($name = uniqid(), uniqid()))
			->then
				->boolean(isset($dependencies->{uniqid()}))->isFalse()
				->boolean(isset($dependencies->{$name}))->isTrue()
			->if($dependencies->setArgument($otherName = uniqid(), null))
			->then
				->boolean(isset($dependencies->{uniqid()}))->isFalse()
				->boolean(isset($dependencies->{$name}))->isTrue()
				->boolean(isset($dependencies->{$otherName}))->isTrue()
		;
	}

	public function test__unset()
	{
	}

	public function testSetDependence()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->object($dependencies->setDependence($name = uniqid(), $dependence = new testedClass(uniqid())))->isIdenticalTo($dependencies)
				->sizeOf($dependencies)->isEqualTo(1)
				->string($dependencies[$name]())->isEqualTo($dependence())
				->object($dependencies->setDependence($otherName = uniqid(), $otherDependence = new testedClass(uniqid())))->isIdenticalTo($dependencies)
				->sizeOf($dependencies)->isEqualTo(2)
				->string($dependencies[$name]())->isEqualTo($dependence())
				->string($dependencies[$otherName]())->isEqualTo($otherDependence())
		;
	}

	public function testOffsetSet()
	{
		$this
			->if($dependencies = new testedClass())
			->and($dependencies[$name = uniqid()] = $dependence = new testedClass(uniqid()))
			->then
				->sizeOf($dependencies)->isEqualTo(1)
				->string($dependencies[$name]())->isEqualTo($dependence())
			->if($dependencies[$otherName = uniqid()] = $otherDependence = new testedClass(uniqid()))
			->then
				->sizeOf($dependencies)->isEqualTo(2)
				->string($dependencies[$name]())->isEqualTo($dependence())
				->string($dependencies[$otherName]())->isEqualTo($otherDependence())
		;
	}

	public function testGetDependence()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->variable($dependencies->getDependence(uniqid()))->isNull()
			->if($dependencies->setDependence($name = uniqid(), $dependence = new testedClass()))
			->then
				->variable($dependencies->getDependence(uniqid()))->isNull()
				->object($dependencies->getDependence($name))->isIdenticalTo($dependence)
		;
	}

	public function testOffsetGet()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->variable($dependencies[uniqid()])->isNull()
		;
	}

	public function testDependenceExists()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->boolean($dependencies->dependenceExists(uniqid()))->isFalse()
			->if($dependencies->setDependence($name = uniqid(), new testedClass()))
			->then
				->boolean($dependencies->dependenceExists(uniqid()))->isFalse()
				->boolean($dependencies->dependenceExists($name))->isTrue()
		;
	}

	public function testOffsetExists()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->boolean(isset($dependencies[uniqid()]))->isFalse()
			->if($dependencies->setDependence($name = uniqid(), new testedClass()))
			->then
				->boolean(isset($dependencies[uniqid()]))->isFalse()
				->boolean(isset($dependencies[$name]))->isTrue()
		;
	}

	public function testUnsetDependence()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->object($dependencies->unsetDependence(uniqid()))->isIdenticalTo($dependencies)
			->if($dependencies->setDependence($name = uniqid(), new testedClass()))
			->then
				->object($dependencies->unsetDependence($name))->isIdenticalTo($dependencies)
				->boolean($dependencies->dependenceExists($name))->isFalse()
		;
	}

	public function testOffsetUnset()
	{
		$this
			->if($dependencies = new testedClass())
			->and($dependencies->setDependence($name = uniqid(), new testedClass()))
			->when(function() use ($dependencies, $name) { unset($dependencies[$name]); })
			->then
				->boolean($dependencies->dependenceExists($name))->isFalse()
		;
	}

	public function testSetArgument()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->object($dependencies->setArgument($name = uniqid(), $value = uniqid()))->isIdenticalTo($dependencies)
				->string($dependencies->getArgument($name))->isEqualTo($value)
		;
	}

	public function testGetArgument()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->exception(function() use ($dependencies, & $argument) { $dependencies->getArgument($argument = uniqid()); })
					->isInstanceOf('mageekguy\atoum\dependencies\exception')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($dependencies->setArgument($name = uniqid(), $value = uniqid()))
			->then
				->string($dependencies->getArgument($name))->isEqualTo($value)
		;
	}

	public function testArgumentExists()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->boolean($dependencies->argumentExists(uniqid()))->isFalse()
			->if($dependencies->setArgument($name = uniqid(), uniqid()))
			->then
				->boolean($dependencies->argumentExists(uniqid()))->isFalse()
				->boolean($dependencies->argumentExists($name))->isTrue()
			->if($dependencies->setArgument($otherName = uniqid(), null))
			->then
				->boolean($dependencies->argumentExists(uniqid()))->isFalse()
				->boolean($dependencies->argumentExists($name))->isTrue()
				->boolean($dependencies->argumentExists($otherName))->isTrue()
		;
	}

	public function testUnsetArgument()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->object($dependencies->unsetArgument(uniqid()))->isIdenticalTo($dependencies)
			->if($dependencies->setArgument($name = uniqid(), uniqid()))
			->then
				->object($dependencies->unsetArgument($name))->isIdenticalTo($dependencies)
				->boolean($dependencies->argumentExists($name))->isFalse()
		;
	}
}