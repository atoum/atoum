<?php

namespace mageekguy\atoum\tests\units\test\assertion;

require __DIR__ . '/../../../runner.php';

use
	atoum,
	atoum\asserter
;

class aliaser extends atoum
{
	public function testClass()
	{
		$this->testedClass->implements('arrayAccess');
	}

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

	public function test__get()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->string($this->testedInstance->{$alias = uniqid()})->isEqualTo($alias)

			->if($this->testedInstance->{$alias} = $keyword = uniqid())
			->then
				->string($this->testedInstance->{$alias})->isEqualTo($keyword)
				->string($this->testedInstance[$context = uniqid()]->{$alias})->isEqualTo($alias)

			->if($this->testedInstance[$context]->{$alias} = $contextKeyword = uniqid())
			->then
				->string($this->testedInstance->{$alias})->isEqualTo($keyword)
				->string($this->testedInstance[$context]->{$alias})->isEqualTo($contextKeyword)
		;
	}

	public function test__set()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->if($this->newTestedInstance->{$alias = uniqid()} = $keyword = uniqid())
				->then
					->string($this->testedInstance->{$alias})->isEqualTo($keyword)
		;
	}

	public function test__isset()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->boolean(isset($this->testedInstance->{$alias = uniqid()}))->isFalse

			->if($this->testedInstance->{$alias} = uniqid())
			->then
				->boolean(isset($this->testedInstance->{$alias}))->isTrue
				->boolean(isset($this->testedInstance->{uniqid()}))->isFalse
		;
	}

	public function test__unset()
	{
		$this
			->given($aliaser = $this->newTestedInstance)
			->then
				->if($this->testedInstance->{$alias = uniqid()} = uniqid())
				->when(function() use ($aliaser, $alias) { unset($aliaser->{$alias}); })
				->then
					->boolean(isset($this->testedInstance->{$alias}))->isFalse
		;
	}

	public function testOffsetGet()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance[$context = uniqid()])->isTestedInstance

			->if($this->testedInstance[$context = uniqid()]->{$alias = uniqid()} = $keyword = uniqid())
			->then
				->string($this->testedInstance->resolveAlias($alias))->isEqualTo($alias)
				->string($this->testedInstance->resolveAlias($alias, $context))->isEqualTo($keyword)

			->if($this->testedInstance->{$otherAlias = uniqid()} = $otherKeyword = uniqid())
			->then
				->string($this->testedInstance->resolveAlias($alias))->isEqualTo($alias)
				->string($this->testedInstance->resolveAlias($alias, $context))->isEqualTo($keyword)
				->string($this->testedInstance->resolveAlias($otherAlias))->isEqualTo($otherKeyword)
				->string($this->testedInstance->resolveAlias($otherAlias, $context))->isEqualTo($otherAlias)
		;
	}

	public function testOffsetSet()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->if($this->testedInstance[$newContext = uniqid()] = $oldContext = uniqid())
				->then
					->boolean(isset($this->testedInstance[$newContext]))->isFalse

				->if(
					$this->testedInstance[$oldContext]->{$alias = uniqid()} = $keyword = uniqid(),
					$this->testedInstance[$newContext] = $oldContext
				)
				->then
					->boolean(isset($this->testedInstance[$newContext]))->isTrue
				->string($this->testedInstance->resolveAlias($alias, $newContext))->isEqualTo($keyword)
				->string($this->testedInstance->resolveAlias($alias, $oldContext))->isEqualTo($keyword)
		;
	}

	public function testOffsetUnset()
	{
		$this
			->given($aliaser = $this->newTestedInstance)
			->then
				->if($this->testedInstance[$context = uniqid()]->{$alias = uniqid()} = $keyword = uniqid())
				->when(function() use ($aliaser, $context) { unset($aliaser[$context]); })
				->then
					->string($this->testedInstance->resolveAlias($alias))->isEqualTo($alias)
		;
	}

	public function testOffsetExists()
	{
		$this
			->given($aliaser = $this->newTestedInstance)
			->then
				->boolean(isset($this->testedInstance[uniqid()]))->isFalse

			->if($this->testedInstance[$context = uniqid()]->{uniqid()} = uniqid())
			->then
				->boolean(isset($this->testedInstance[uniqid()]))->isFalse
				->boolean(isset($this->testedInstance[$context]))->isTrue

			->when(function() use ($aliaser, $context) { unset($aliaser[$context]); })
			->then
				->boolean(isset($this->testedInstance[uniqid()]))->isFalse
				->boolean(isset($this->testedInstance[$context]))->isFalse
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

	public function testAliasKeyword()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->aliasKeyword($keyword = uniqid(), $alias = uniqid()))->isTestedInstance
				->string($this->testedInstance->resolveAlias($alias))->isEqualTo($keyword)
				->string($this->testedInstance->{$alias})->isEqualTo($keyword)
				->string($this->testedInstance[uniqid()]->{$alias})->isEqualTo($alias)

				->object($this->testedInstance->aliasKeyword($otherKeyword = uniqid(), $otherAlias = uniqid(), $context = uniqid()))->isTestedInstance
				->string($this->testedInstance->resolveAlias($alias))->isEqualTo($keyword)
				->string($this->testedInstance->{$alias})->isEqualTo($keyword)
				->string($this->testedInstance[uniqid()]->{$alias})->isEqualTo($alias)
				->string($this->testedInstance->resolveAlias($otherAlias))->isEqualTo($otherAlias)
				->string($this->testedInstance->{$otherAlias})->isEqualTo($otherAlias)
				->string($this->testedInstance->resolveAlias($otherAlias, $context))->isEqualTo($otherKeyword)
				->string($this->testedInstance[$context]->{$otherAlias})->isEqualTo($otherKeyword)
				->string($this->testedInstance[uniqid()]->{$otherAlias})->isEqualTo($otherAlias)
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
			->from('object')->use('isTestedInstance')->as('isSut')
			->then
				->object($this->testedInstance->alias(uniqid()))->isSut
		;
	}

	public function testTo()
	{
		$this
			->given($this->newTestedInstance)

			->if($this->testedInstance->alias($keyword = uniqid()))
			->then
				->object($this->testedInstance->to($alias = uniqid()))->isTestedInstance
				->string($this->testedInstance->resolveAlias($alias))->isEqualTo($keyword)

			->if(
				$this->testedInstance->from($context = uniqid()),
				$this->testedInstance->alias($keyword = uniqid())
			)
			->then
				->object($this->testedInstance->to($alias = uniqid()))->isTestedInstance
				->string($this->testedInstance->resolveAlias($alias, $context))->isEqualTo($keyword)
		;
	}
}
