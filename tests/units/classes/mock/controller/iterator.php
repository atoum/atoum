<?php

namespace mageekguy\atoum\tests\units\mock\controller;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\mock\controller\iterator as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class foo
{
	public function __construct() {}
	public function doesSomething() {}
	public function doesSomethingElse() {}
}

class iterator extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->implements('iteratorAggregate');
	}

	public function test__construct()
	{
		$this
			->if($iterator = new testedClass())
			->then
				->variable($iterator->getMockController())->isNull()
				->array($iterator->getMethods())->isEmpty()
				->array($iterator->getFilters())->isEmpty()
			->if($iterator = new testedClass($controller = new mock\controller()))
			->then
				->object($iterator->getMockController())->isIdenticalTo($controller)
				->array($iterator->getMethods())->isEmpty()
				->array($iterator->getFilters())->isEmpty()
		;
	}

	public function test__set()
	{
		$this
			->if($iterator = new testedClass($controller = new mock\controller()))
			->and($controller->control($mock = new \mock\mageekguy\atoum\tests\units\mock\controller\foo()))
			->and($iterator->return = $return = uniqid())
			->then
				->boolean(isset($controller->__construct))->isFalse()
				->string($controller->doesSomething->invoke())->isEqualTo($return)
				->string($controller->doesSomethingElse->invoke())->isEqualTo($return)
			->if($iterator->addFilter(function($name) { return (strtolower($name) == 'doessomething'); }))
			->and($iterator->return = $otherReturn = uniqid())
			->then
				->boolean(isset($controller->__construct))->isFalse()
				->string($controller->doesSomething->invoke())->isEqualTo($otherReturn)
				->string($controller->doesSomethingElse->invoke())->isEqualTo($return)
			->if($iterator->resetFilters())
			->and($iterator->return = $otherReturn)
			->then
				->boolean(isset($controller->__construct))->isFalse()
				->string($controller->doesSomething->invoke())->isEqualTo($otherReturn)
				->string($controller->doesSomethingElse->invoke())->isEqualTo($otherReturn)
			->if($iterator->return = $mock)
			->then
				->boolean(isset($controller->__construct))->isFalse()
				->object($controller->doesSomething->invoke())->isIdenticalTo($mock)
				->object($controller->doesSomethingElse->invoke())->isIdenticalTo($mock)
			->if($iterator->throw = $exception = new \exception())
			->then
				->exception(function() use ($controller) { $controller->doesSomething->invoke(); })->isIdenticalTo($exception)
				->exception(function() use ($controller) { $controller->doesSomethingElse->invoke(); })->isIdenticalTo($exception)
			->exception(function() use ($iterator) { $iterator->{uniqid()} = uniqid(); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
		;
	}

	public function testSetMockController()
	{
		$this
			->if($iterator = new testedClass())
			->then
				->object($iterator->setMockController($controller = new mock\controller()))->isIdenticalTo($iterator)
				->object($iterator->getMockController())->isIdenticalTo($controller)
				->object($iterator->setMockController($otherController = new mock\controller()))->isIdenticalTo($iterator)
				->object($iterator->getMockController())->isIdenticalTo($otherController)
		;
	}

	public function testResetFilters()
	{
		$this
			->if($iterator = new testedClass($controller = new mock\controller()))
			->then
				->object($iterator->resetFilters())->isIdenticalTo($iterator)
				->array($iterator->getFilters())->isEmpty()
			->if($iterator->addFilter(function() {}))
			->then
				->object($iterator->resetFilters())->isIdenticalTo($iterator)
				->array($iterator->getFilters())->isEmpty()
		;
	}

	public function testAddFilter()
	{
		$this
			->if($iterator = new testedClass($controller = new mock\controller()))
			->then
				->object($iterator->addFilter($filter = function() {}))->isIdenticalTo($iterator)
				->array($iterator->getFilters())->isIdenticalTo(array($filter))
				->object($iterator->addFilter($otherFilter = function() {}))->isIdenticalTo($iterator)
				->array($iterator->getFilters())->isIdenticalTo(array($filter, $otherFilter))
		;
	}

	public function testGetMethods()
	{
		$this
			->if($iterator = new testedClass($controller = new mock\controller()))
			->then
				->array($iterator->getMethods())->isEmpty()
			->if($controller->control($mock = new \mock\mageekguy\atoum\tests\units\mock\controller\foo()))
			->then
				->array($iterator->getMethods())->isEqualTo(array('doessomething', 'doessomethingelse'))
			->if($iterator->addFilter(function($method) { return true; }))
			->then
				->array($iterator->getMethods())->isEqualTo(array('doessomething', 'doessomethingelse'))
			->if($iterator->addFilter(function($method) { return false; }))
			->then
				->array($iterator->getMethods())->isEmpty()
			->if($iterator->resetFilters()->addFilter(function($name) { return (strtolower($name) == 'doessomething'); }))
			->then
				->array($iterator->getMethods())->isEqualTo(array('doessomething'))
		;
	}

	public function testGetIterator()
	{
		$this
			->if($iterator = new testedClass($controller = new mock\controller()))
			->then
				->object($iterator->getIterator())->isEqualTo(new \arrayIterator($iterator->getMethods()))
			->if($controller->control(new \mock\mageekguy\atoum\tests\units\mock\controller\foo()))
			->then
				->object($iterator->getIterator())->isEqualTo(new \arrayIterator($iterator->getMethods()))
		;
	}
}
