<?php

namespace mageekguy\atoum\tests\units\test\assertion;

require __DIR__ . '/../../../runner.php';

use
	atoum,
	atoum\asserter
;

class aliaser extends atoum
{
	public function test__construct()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->getResolver())->isEqualTo(new asserter\resolver())

			->given($this->newTestedInstance($resolver = new asserter\resolver()))
			->then
				->object($this->testedInstance->getResolver())->isIdenticalTo($resolver)
		;
	}

	public function testSetResolver()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->setResolver($resolver = new asserter\resolver()))->isTestedInstance
				->object($this->testedInstance->getResolver())->isIdenticalTo($resolver)

				->object($this->testedInstance->setResolver())->isTestedInstance
				->object($this->testedInstance->getResolver())
					->isNotIdenticalTo($resolver)
					->isEqualTo(new asserter\resolver())
		;
	}

	public function testAliasClass()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->aliasClass($class = uniqid(), $alias = uniqid()))->isTestedInstance
				->string($this->testedInstance->resolveClass($alias))->isEqualTo($class)

				->string($this->testedInstance->resolveClass($unknownAlias = uniqid()))->isEqualTo($unknownAlias)
		;
	}

	public function testAliasMethod()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->aliasMethod($class = uniqid(), $method = uniqid(), $alias = uniqid()))->isTestedInstance
				->string($this->testedInstance->resolveMethod($class, $alias))->isEqualTo($method)

				->string($this->testedInstance->resolveMethod($class, $unknownAlias = uniqid()))->isEqualTo($unknownAlias)
		;
	}

	public function testFrom()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->from(uniqid()))->isTestedInstance
		;
	}

	public function testAlias()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->alias(uniqid()))->isTestedInstance
		;
	}

	public function testTo()
	{
		$this
			->given($this->newTestedInstance)

			->if($this->testedInstance->alias($class = uniqid()))
			->then
				->object($this->testedInstance->to($alias = uniqid()))->isTestedInstance
				->string($this->testedInstance->resolveClass($alias))->isEqualTo($class)

			->if(
				$this->testedInstance->from($class = uniqid()),
				$this->testedInstance->alias($method = uniqid())
			)
			->then
				->object($this->testedInstance->to($alias = uniqid()))->isTestedInstance
				->string($this->testedInstance->resolveMethod($class, $alias))->isEqualTo($method)
		;
	}
}