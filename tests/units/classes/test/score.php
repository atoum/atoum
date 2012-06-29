<?php

namespace mageekguy\atoum\tests\units\test;

use
	mageekguy\atoum,
	mageekguy\atoum\test\score as testedClass
;

require_once __DIR__ . '/../../runner.php';

class score extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubClassOf('mageekguy\atoum\score');
	}

	public function test__construct()
	{
		$this
			->if($score = new testedClass())
			->then
				->variable($score->getCase())->isNull()
				->variable($score->getDataSetKey())->isNull()
				->variable($score->getDataSetProvider())->isNull()
		;
	}

	public function testReset()
	{
		$this
			->if($score = new testedClass())
			->then
				->object($score->reset())->isIdenticalTo($score)
				->variable($score->getCase())->isNull()
				->variable($score->getDataSetKey())->isNull()
				->variable($score->getDataSetProvider())->isNull()
			->if($score->setCase(uniqid()))
			->and($score->setDataSet(uniqid(), uniqid()))
			->then
				->object($score->reset())->isIdenticalTo($score)
				->variable($score->getCase())->isNull()
				->variable($score->getDataSetKey())->isNull()
				->variable($score->getDataSetProvider())->isNull()
		;
	}

	public function testSetCase()
	{
		$this
			->if($score = new testedClass())
			->then
				->object($score->setCase($case = uniqid()))->isIdenticalTo($score)
				->string($score->getCase())->isEqualTo($case)
				->object($score->setCase($case = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->string($score->getCase())->isEqualTo((string) $case)
		;
	}

	public function testUnsetCase()
	{
		$this
			->if($score = new testedClass())
			->then
				->variable($score->getCase())->isNull()
				->object($score->unsetCase())->isIdenticalTo($score)
				->variable($score->getCase())->isNull()
			->if($score->setCase(uniqid()))
			->then
				->string($score->getCase())->isNotNull()
				->object($score->unsetCase())->isIdenticalTo($score)
				->variable($score->getCase())->isNull()
		;
	}

	public function testSetDataSet()
	{
		$this
			->if($score = new testedClass())
			->then
				->object($score->setDataSet($key = rand(1, PHP_INT_MAX), $dataProvider = uniqid()))->isIdenticalTo($score)
				->integer($score->getDataSetKey())->isEqualTo($key)
				->string($score->getDataSetProvider())->isEqualTo($dataProvider)
		;
	}

	public function testUnsetDataSet()
	{
		$this
			->if($score = new testedClass())
			->then
				->object($score->unsetDataSet())->isIdenticalTo($score)
				->variable($score->getDataSetKey())->isNull()
				->variable($score->getDataSetProvider())->isNull()
			->if($score->setDataSet(rand(1, PHP_INT_MAX), uniqid()))
			->then
				->object($score->unsetDataSet())->isIdenticalTo($score)
				->variable($score->getDataSetKey())->isNull()
				->variable($score->getDataSetProvider())->isNull()
		;
	}
}
