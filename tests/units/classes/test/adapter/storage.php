<?php

namespace mageekguy\atoum\tests\units\test\adapter;

use
	mageekguy\atoum,
	mageekguy\atoum\test\adapter\storage as testedClass
;

require_once __DIR__ . '/../../runner.php';

class storage extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->implements('iteratorAggregate')
			->implements('countable')
		;
	}

	public function test__construct()
	{
		$this
			->given($storage = new testedClass())
			->then
				->sizeof($storage)->isZero()
		;
	}

	public function testAdd()
	{
		$this
			->given($storage = new testedClass())
			->then
				->object($storage->add($adapter = new atoum\test\adapter()))
					->isIdenticalTo($storage)
					->hasSize(1)
				->boolean($storage->contains($adapter))->isTrue()
				->object($storage->add($adapter))
					->isIdenticalTo($storage)
					->hasSize(1)
				->boolean($storage->contains($adapter))->isTrue()
		;
	}

	public function testReset()
	{
		$this
			->given($storage = new testedClass())
			->then
				->object($storage->reset())
					->isIdenticalTo($storage)
					->hasSize(0)
			->if($storage->add(new atoum\test\adapter()))
			->then
				->object($storage->reset())
					->isIdenticalTo($storage)
					->hasSize(0)
		;
	}

	public function testGetIterator()
	{
		$this
			->given($storage = new testedclass())
			->then
				->object($storage->getIterator())->isEqualTo(new \arrayIterator())
			->if($storage->add($adapter = new atoum\test\adapter()))
			->then
				->object($storage->getIterator())->isEqualTo(new \arrayIterator(array($adapter)))
		;
	}
}
