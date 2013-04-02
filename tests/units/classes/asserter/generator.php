<?php

namespace mageekguy\atoum\tests\units\asserter;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter\generator as testedClass
;

require_once __DIR__ . '/../../runner.php';

class generator extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->getLocale())->isEqualTo(new atoum\locale())
				->object($generator->getAdapter())->isEqualTo(new atoum\adapter())
				->string($generator->getAsserterNamespace())->isEqualTo(testedClass::defaultAsserterNamespace)
			->if($generator = new testedClass($locale = new atoum\locale(), $adapter = new atoum\adapter(), $asserterNamespace = uniqid()))
			->then
				->object($generator->getLocale())->isIdenticalTo($locale)
				->object($generator->getAdapter())->isIdenticalTo($adapter)
				->string($generator->getAsserterNamespace())->isEqualTo($asserterNamespace)
		;
	}

	public function test__get()
	{
		$this
			->if($generator = new testedClass())
			->then
				->exception(function() use ($generator, & $asserter) { $generator->{$asserter = uniqid()}; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Asserter \'' . $asserter . '\' does not exist')
				->object($generator->variable)->isInstanceOf('mageekguy\atoum\asserters\variable')
		;
	}

	public function test__set()
	{
		$this
			->if($generator = new testedClass())
			->then
				->when(function() use ($generator, & $alias, & $asserter) { $generator->{$alias = uniqid()} = ($asserter = uniqid()); })
					->array($generator->getAliases())->isEqualTo(array($alias => $asserter))
				->when(function() use ($generator, & $otherAlias, & $otherAsserter) { $generator->{$otherAlias = uniqid()} = ($otherAsserter = uniqid()); })
					->array($generator->getAliases())->isEqualTo(array($alias => $asserter, $otherAlias => $otherAsserter))
		;
	}

	public function test__call()
	{
		$this
			->if($generator = new testedClass())
			->then
				->exception(function() use ($generator, & $asserter) { $generator->{$asserter = uniqid()}(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Asserter \'' . $asserter . '\' does not exist')
				->object($generator->variable(uniqid()))->isInstanceOf('mageekguy\atoum\asserters\variable')
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($generator)
				->object($generator->getAdapter())->isIdenticalTo($adapter)
				->object($generator->setAdapter())->isIdenticalTo($generator)
				->object($generator->getAdapter())
					->isNotIdenticalTo($adapter)
					->isEqualTo(new atoum\adapter())
		;
	}

	public function testSetLocale()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setLocale($locale = new atoum\locale()))->isIdenticalTo($generator)
				->object($generator->getLocale())->isIdenticalTo($locale)
				->object($generator->setLocale())->isIdenticalTo($generator)
				->object($generator->getLocale())
					->isNotIdenticalTo($locale)
					->isEqualTo(new atoum\locale())
		;
	}

	public function testSetAsserterNamespace()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setAsserterNamespace($namespace = uniqid()))->isIdenticalTo($generator)
				->string($generator->getAsserterNamespace())->isEqualTo($namespace)
				->object($generator->setAsserterNamespace())->isIdenticalTo($generator)
				->string($generator->getAsserterNamespace())->isEqualTo(testedClass::defaultAsserterNamespace)
		;
	}

	public function testSetAlias()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setAlias($alias = uniqid(), $asserter = uniqid()))->isIdenticalTo($generator)
				->array($generator->getAliases())->isEqualTo(array($alias => $asserter))
				->object($generator->setAlias($otherAlias = 'FOO', $otherAsserter = uniqid()))->isIdenticalTo($generator)
				->array($generator->getAliases())->isEqualTo(array($alias => $asserter, 'foo' => $otherAsserter))
		;
	}

	public function testResetAliases()
	{
		$this
			->if($generator = new testedClass())
			->and($generator->setAlias(uniqid(), uniqid()))
			->then
				->array($generator->getAliases())->isNotEmpty()
				->object($generator->resetAliases())->isIdenticalTo($generator)
				->array($generator->getAliases())->isEmpty()
		;
	}

	public function testGetAsserterClass()
	{
		$this
			->if($generator = new testedClass())
			->and($generator->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->class_exists = true)
			->then
				->string($generator->getAsserterClass($asserter = uniqid()))->isEqualTo('mageekguy\atoum\asserters\\' . $asserter)
				->string($generator->getAsserterClass('\\' . $asserter))->isEqualTo('\\' . $asserter)
			->if($generator->setAlias($alias = uniqid(), $asserter))
			->then
				->string($generator->getAsserterClass($asserter))->isEqualTo(testedClass::defaultAsserterNamespace . '\\' . $asserter)
				->string($generator->getAsserterClass($alias))->isEqualTo(testedClass::defaultAsserterNamespace . '\\' . $asserter)
			->if($generator->setAsserterNamespace($namespace = uniqid()))
			->then
				->string($generator->getAsserterClass($asserter))->isEqualTo($namespace . '\\' . $asserter)
				->string($generator->getAsserterClass($alias))->isEqualTo($namespace . '\\' . $asserter)
			->if($adapter->class_exists = false)
				->variable($generator->getAsserterClass($asserter))->isNull()
		;
	}
}
