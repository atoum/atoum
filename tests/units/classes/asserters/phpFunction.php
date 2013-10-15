<?php

namespace mageekguy\atoum\tests\units\asserters;

require __DIR__ . '/../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\asserters\phpFunction as sut
;

class phpFunction extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new sut())
			->then
				->string($asserter->getNamespace())->isEmpty()
				->string($asserter->getFunction())->isEmpty()
		;
	}

	public function __toString()
	{
		$this
			->if($asserter = new sut())
			->then
				->castToString($asserter)->isEmpty()
			->if($asserter->setNamespace($namespace = uniqid()))
			->then
				->castToString($asserter)->isEmpty()
			->if($asserter->setFunction($function = uniqid()))
			->then
				->castToString($asserter)->isEqualTo($namespace . '\\' . $function)
		;
	}

	public function testSetNamespace()
	{
		$this
			->if($asserter = new sut())
			->then
				->object($asserter->setNamespace($namespace = ''))->isIdenticalTo($asserter)
				->string($asserter->getNamespace())->isEmpty()
				->object($asserter->setNamespace($namespace = uniqid()))->isIdenticalTo($asserter)
				->string($asserter->getNamespace())->isEqualTo($namespace . '\\')
				->object($asserter->setNamespace(($namespace = uniqid()) . '\\'))->isIdenticalTo($asserter)
				->string($asserter->getNamespace())->isEqualTo($namespace . '\\')
				->object($asserter->setNamespace('\\' . ($namespace = uniqid()) . '\\'))->isIdenticalTo($asserter)
				->string($asserter->getNamespace())->isEqualTo($namespace . '\\')
				->object($asserter->setNamespace('\\' . ($namespace = uniqid())))->isIdenticalTo($asserter)
				->string($asserter->getNamespace())->isEqualTo($namespace . '\\')
		;
	}

	public function testSetFunction()
	{
		$this
			->if($asserter = new sut())
			->then
				->object($asserter->setFunction($function = uniqid()))->isIdenticalTo($asserter)
				->string($asserter->getFunction())->isEqualTo($function)
		;
	}

	public function testGetFullyQualifiedFunctionName()
	{
		$this
			->if($asserter = new sut())
			->then
				->string($asserter->getFullyQualifiedFunctionName())->isEmpty()
			->if($asserter->setNamespace($namespace = uniqid()))
			->then
				->string($asserter->getFullyQualifiedFunctionName())->isEmpty()
			->if($asserter->setFunction($function = uniqid()))
			->then
				->string($asserter->getFullyQualifiedFunctionName())->isEqualTo($namespace . '\\' . $function)
		;
	}

	public function testSetWithTest()
	{
		$this
			->if($asserter = new sut())
			->then
				->object($asserter->setWithTest($this))->isIdenticalTo($asserter)
				->string($asserter->getNamespace())->isEqualTo($this->getTestedClassNamespace() . '\\')
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new sut())
			->then
				->object($asserter->setWith($function = uniqid()))->isIdenticalTo($asserter)
				->string($asserter->getFunction())->isEqualTo($function)
		;
	}

	public function testIsCalled()
	{
		$this
			->if($asserter = new sut())
			->and($this->function->function_exists = false)
			->then
				->exception(function() use ($asserter) { $asserter->isCalled(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')
			->if($asserter->setNamespace($namespace = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->isCalled(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function is undefined')
			->if($asserter->setFunction($function = uniqid()))
			->then
				->exception(function() use ($asserter) { $asserter->isCalled(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Function \'' . $asserter->getFullyQualifiedFunctionName() . '\' does not exist')
			->if($this->function->function_exists = true)
			->then
				->exception(function() use ($asserter) { $asserter->isCalled(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($asserter->getLocale()->_('function %s is called 0 time'), $asserter))
			->if(php\mocker::getAdapter()->addCall($asserter->getFullyQualifiedFunctionName()))
			->then
				->object($asserter->isCalled())->isIdenticalTo($asserter)
		;
	}
}
