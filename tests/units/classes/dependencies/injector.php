<?php

namespace mageekguy\atoum\tests\units\dependencies;

use
	mageekguy\atoum\test,
	mageekguy\atoum\dependencies\injector as testedClass
;

require_once __DIR__ . '/../../runner.php';

class injector extends test
{
	public function testClass()
	{
		$this->testedClass->hasInterface('arrayAccess');
	}

	public function test__construct()
	{
		$this
			->if($injector = new testedClass($closure = function() {}))
			->then
				->object($injector->getClosure())->isIdenticalTo($closure)
				->array($injector->getArguments())->isEmpty()
		;
	}

	public function test__invoke()
	{
		$this
			->if($injector = new testedClass(function() use (& $return) { return ($return = uniqid()); }))
			->then
				->string($injector())->isEqualTo($return)
			->if($injector = new testedClass(function($argument) { return $argument; }))
			->and($injector->setArgument(1, $argument = uniqid()))
			->then
				->string($injector())->isEqualTo($argument)
			->if($injector = new testedClass(function($argument1, $argument2) { return $argument1 . $argument2; }))
			->and($injector->setArgument(1, $argument1 = uniqid()))
			->and($injector->setArgument(2, $argument2 = uniqid()))
			->then
				->string($injector())->isEqualTo($argument1 . $argument2)
			->if($injector = new testedClass(function($a, $b) { return $a . $b; }))
			->and($injector->setArgument('b', $valueB = uniqid()))
			->and($injector->setArgument('a', $valueA = uniqid()))
			->then
				->string($injector())->isEqualTo($valueA . $valueB)
				->string($injector($otherValueA = uniqid(), $otherValueB = uniqid()))->isEqualTo($otherValueA . $otherValueB)
		;
	}

	public function testSetArgument()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->object($injector->setArgument(1, $argument1 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEqualTo(array(1 => $argument1))
				->object($injector->setArgument(2, $argument2 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEqualTo(array(1 => $argument1, 2 => $argument2))
			->if($injector = new testedClass(function() {}))
			->then
				->object($injector->setArgument(2, $argument2 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEqualTo(array(2 => $argument2))
				->object($injector->setArgument(1, $argument1 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEqualTo(array(1 => $argument1, 2 => $argument2))
			->if($injector = new testedClass(function() {}))
			->then
				->object($injector->setArgument('a', $argument1 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEqualTo(array('a' => $argument1))
				->object($injector->setArgument('b', $argument2 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEqualTo(array('a' => $argument1, 'b' => $argument2))
			->if($injector = new testedClass(function() {}))
			->then
				->object($injector->setArgument('b', $argument2 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEqualTo(array('b' => $argument2))
				->object($injector->setArgument('a', $argument1 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEqualTo(array('a' => $argument1, 'b' => $argument2))
		;
	}

	public function testOffsetSet()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->object($injector->offsetSet(1, $argument1 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEqualTo(array(1 => $argument1))
				->object($injector->offsetSet(2, $argument2 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEqualTo(array(1 => $argument1, 2 => $argument2))
			->if($injector = new testedClass(function() {}))
			->then
				->object($injector->offsetSet(2, $argument2 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEqualTo(array(2 => $argument2))
				->object($injector->offsetSet(1, $argument1 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEqualTo(array(1 => $argument1, 2 => $argument2))
			->if($injector = new testedClass(function() {}))
			->then
				->object($injector->offsetSet('a', $argument1 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEqualTo(array('a' => $argument1))
				->object($injector->offsetSet('b', $argument2 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEqualTo(array('a' => $argument1, 'b' => $argument2))
			->if($injector = new testedClass(function() {}))
			->then
				->object($injector->offsetSet('b', $argument2 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEqualTo(array('b' => $argument2))
				->object($injector->offsetSet('a', $argument1 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEqualTo(array('a' => $argument1, 'b' => $argument2))
		;
	}
}
