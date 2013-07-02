<?php

namespace mageekguy\atoum\tests\units\template;

use
	mageekguy\atoum,
	mageekguy\atoum\template
;

require_once __DIR__ . '/../../runner.php';

class data extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($data = new template\data())
			->then
				->string($data->getData())->isEmpty()
				->variable($data->getTag())->isNull()
				->variable($data->getId())->isNull()
				->array($data->getChildren())->isEmpty()
			->if($data = new template\data($string = uniqid()))
			->then
				->string($data->getData())->isEqualTo($string)
				->variable($data->getTag())->isNull()
				->variable($data->getId())->isNull()
				->array($data->getChildren())->isEmpty()
		;
	}

	public function test__toString()
	{
		$this
			->if($data = new template\data())
			->then
				->castToString($data)->isEmpty()
			->if($data = new template\data($string = uniqid()))
			->then
				->castToString($data)->isEqualTo($string)
		;
	}

	public function testGetData()
	{
		$this
			->if($data = new template\data())
			->then
				->string($data->getData())->isEmpty()
			->if($data = new template\data($string = uniqid()))
			->then
				->string($data->getData())->isEqualTo($string)
		;
	}

	public function testSetData()
	{
		$this
			->if($data = new template\data())
			->then
				->object($data->setData($string = uniqid()))->isIdenticalTo($data)
				->string($data->getData())->isEqualTo($string)
		;
	}

	public function testAddData()
	{
		$this
			->if($string = uniqid())
			->and($data = new template\data())
			->then
				->object($data->addData($string = uniqid()))->isIdenticalTo($data)
				->string($data->getData())->isEqualTo($string)
				->object($data->addData($string))->isIdenticalTo($data)
				->string($data->getData())->isEqualTo($string . $string)
				->object($data->addData($otherString = uniqid()))->isIdenticalTo($data)
				->string($data->getData())->isEqualTo($string . $string . $otherString)
		;
	}

	public function testSetParent()
	{
		$this
			->if($data = new template\data())
			->then
				->object($data->setParent($tag = new template\tag(uniqid())))->isIdenticalTo($data)
				->object($data->getParent())->isIdenticalTo($tag)
				->object($tag->getChild(0))->isIdenticalTo($data)
		;
	}

	public function testGetParent()
	{
		$this
			->if($data = new template\data())
			->then
				->variable($data->getParent())->isNull()
			->if($data->setParent($tag = new template\tag(uniqid())))
			->then
				->object($data->getParent())->isIdenticalTo($tag)
		;
	}

	public function testParentIsSet()
	{
		$this
			->if($data = new template\data())
			->then
				->boolean($data->parentIsSet())->isFalse()
			->if($data->setParent($tag = new template\tag(uniqid())))
			->then
				->boolean($data->parentIsSet())->isTrue()
		;
	}

	public function testUnsetParent()
	{
		$this
			->if($data = new template\data())
			->then
				->object($data->unsetParent())->isIdenticalTo($data)
				->boolean($data->parentIsSet())->isFalse()
			->if($data->setParent($tag = new template\tag(uniqid())))
			->then
				->object($data->unsetParent())->isIdenticalTo($data)
				->boolean($data->parentIsSet())->isFalse()
		;
	}

	public function testHasChildren()
	{
		$this
			->if($data = new template\data())
			->then
				->boolean($data->hasChildren())->isFalse()
		;
	}

	public function testGetChildren()
	{
		$this
			->if($data = new template\data())
			->then
				->array($data->getChildren())->isEmpty()
		;
	}

	public function testGetChild()
	{
		$this
			->if($data = new template\data())
			->then
				->variable($data->getChild(0))->isNull()
				->variable($data->getChild(rand(1, PHP_INT_MAX)))->isNull()
		;
	}

	public function testGetById()
	{
		$this
			->if($data = new template\data())
			->then
				->variable($data->getById(uniqid()))->isNull()
		;
	}

	public function testGetByTag()
	{
		$this
			->if($data = new template\data())
			->then
				->array($data->getByTag(uniqid()))->isEmpty()
		;
	}

	public function testGetId()
	{
		$this
			->if($data = new template\data())
			->then
				->variable($data->getId())->isNull()
		;
	}

	public function testGetTag()
	{
		$this
			->if($data = new template\data())
			->then
				->variable($data->getTag())->isNull()
		;
	}

	public function testResetData()
	{
		$this
			->if($data = new template\data())
			->then
				->object($data->resetData())->isIdenticalTo($data)
				->string($data->getData())->isEmpty()
			->if($data = new template\data($string = uniqid()))
			->then
				->object($data->resetData())->isIdenticalTo($data)
				->string($data->getData())->isEmpty()
		;
	}

	public function testIsRoot()
	{
		$this
			->if($data = new template\data())
			->then
				->boolean($data->isRoot())->isTrue()
			->if($data->setParent(new template\tag(uniqid())))
			->then
				->boolean($data->isRoot())->isFalse()
		;
	}

	public function testBuild()
	{
		$this
			->if($data = new template\data())
			->then
				->object($data->build())->isIdenticalTo($data)
			->if($data = new template\data(uniqid()))
			->then
				->object($data->build())->isIdenticalTo($data)
		;
	}
}
