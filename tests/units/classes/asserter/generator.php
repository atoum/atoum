<?php

namespace mageekguy\atoum\tests\units\asserter;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter
;

require_once __DIR__ . '/../../runner.php';

class generator extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($generator = new asserter\generator())
			->then
				->object($generator->getLocale())->isEqualTo(new atoum\locale())
			->if($generator = new asserter\generator($locale = new atoum\locale()))
			->then
				->object($generator->getLocale())->isIdenticalTo($locale)
		;
	}

	public function test__get()
	{
		$this
			->if($generator = new asserter\generator())
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
			->if($generator = new asserter\generator())
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
			->if($generator = new asserter\generator())
			->then
				->exception(function() use ($generator, & $asserter) { $generator->{$asserter = uniqid()}(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Asserter \'' . $asserter . '\' does not exist')
				->object($generator->variable(uniqid()))->isInstanceOf('mageekguy\atoum\asserters\variable')
		;
	}

	public function testSetLocale()
	{
		$this
			->if($generator = new asserter\generator())
			->then
				->object($generator->setLocale($locale = new atoum\locale()))->isIdenticalTo($generator)
				->object($generator->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetAlias()
	{
		$this
			->if($generator = new asserter\generator())
			->then
				->object($generator->setAlias($alias = uniqid(), $asserter = uniqid()))->isIdenticalTo($generator)
				->array($generator->getAliases())->isEqualTo(array($alias => $asserter))
				->object($generator->setAlias($otherAlias = uniqid(), $otherAsserter = uniqid()))->isIdenticalTo($generator)
				->array($generator->getAliases())->isEqualTo(array($alias => $asserter, $otherAlias => $otherAsserter))
		;
	}

	public function testResetAliases()
	{
		$this
			->if($generator = new asserter\generator())
			->and($generator->setAlias(uniqid(), uniqid()))
			->then
				->array($generator->getAliases())->isNotEmpty()
				->object($generator->resetAliases())->isIdenticalTo($generator)
				->array($generator->getAliases())->isEmpty()
		;
	}
}
