<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters\extension as sut // use sut here instead of testedClass because atoum\asserters\testedClass exists !
;

require_once __DIR__ . '/../../runner.php';

class extension extends atoum\test
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
				->object($asserter->getGenerator())->isEqualTo(new asserter\generator())
				->object($asserter->getLocale())->isIdenticalTo($asserter->getGenerator()->getLocale())
				->object($asserter->getAdapter())->isEqualTo(new atoum\adapter())
				->variable($asserter->getName())->isNull()
			->if($asserter = new sut($generator = new asserter\generator(), $adapter = new atoum\adapter()))
			->then
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getAdapter())->isIdenticalTo($adapter)
				->variable($asserter->getName())->isNull()
		;
	}

	public function test__toString()
	{
		$this
			->if($asserter = new sut(new asserter\generator()))
			->then
				->castToString($asserter)->isEmpty()
			->if($asserter->setWith($extensionName = uniqid()))
			->then
				->castToString($asserter)->isEqualTo($extensionName)
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new sut(new asserter\generator()))
			->then
				->object($asserter->setWith($extensionName = uniqid()))->isIdenticalTo($asserter)
				->string($asserter->getName())->isEqualTo($extensionName)
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($asserter = new sut(new asserter\generator()))
			->then
				->object($asserter->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($asserter)
				->object($asserter->getAdapter())->isIdenticalTo($adapter)
				->object($asserter->setAdapter())->isIdenticalTo($asserter)
				->object($asserter->getAdapter())
					->isNotIdenticalTo($adapter)
					->isEqualTo(new atoum\adapter())
		;
	}

	public function testReset()
	{
		$this
			->if($asserter = new sut(new asserter\generator()))
			->then
				->object($asserter->reset())->isIdenticalTo($asserter)
				->variable($asserter->getName())->isNull()
			->if($asserter->setWith(uniqid()))
			->then
				->object($asserter->reset())->isIdenticalTo($asserter)
				->variable($asserter->getName())->isNull()
		;
	}

	public function testIsLoaded()
	{
		$this
			->if($asserter = new sut(new asserter\generator()))
			->then
				->exception(function() use ($asserter) {
						$asserter->isLoaded();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Name of PHP extension is undefined')
			->if($asserter->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->extension_loaded = false)
			->and($asserter->setWith($extensionName = uniqid()))
			->then
				->exception(function() use ($asserter) {
						$asserter->isLoaded();
					}
				)
					->isInstanceOf('mageekguy\atoum\test\exceptions\skip')
					->hasMessage('PHP extension \'' . $extensionName . '\' is not loaded')
			->if($adapter->extension_loaded = true)
			->then
				->object($asserter->isLoaded())->isIdenticalTo($asserter)
		;
	}
}
