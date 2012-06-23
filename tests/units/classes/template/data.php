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
		$data = new template\data();

		$this->assert
			->string($data->getData())->isEmpty()
			->variable($data->getTag())->isNull()
			->variable($data->getId())->isNull()
			->array($data->getChildren())->isEmpty()
		;

		$data = new template\data($string = uniqid());

		$this->assert
			->string($data->getData())->isEqualTo($string)
			->variable($data->getTag())->isNull()
			->variable($data->getId())->isNull()
			->array($data->getChildren())->isEmpty()
		;
	}

	public function test__toString()
	{
		$data = new template\data();

		$this->assert
			->castToString($data)->isEmpty()
		;

		$data = new template\data($string = uniqid());

		$this->assert
			->castToString($data)->isEqualTo($string)
		;
	}

	public function testGetData()
	{
		$data = new template\data();

		$this->assert
			->string($data->getData())->isEmpty()
		;

		$data = new template\data($string = uniqid());

		$this->assert
			->string($data->getData())->isEqualTo($string)
		;
	}

	public function testSetData()
	{
		$data = new template\data();

		$this->assert
			->object($data->setData($string = uniqid()))->isIdenticalTo($data)
			->string($data->getData())->isEqualTo($string)
		;
	}

	public function testAddData()
	{
		$string = uniqid();

		$data = new template\data();

		$this->assert
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
		$data = new template\data();

		$this->assert
			->object($data->setParent($tag = new template\tag(uniqid())))->isIdenticalTo($data)
			->object($data->getParent())->isIdenticalTo($tag)
			->object($tag->getChild(0))->isIdenticalTo($data)
		;
	}

	public function testGetParent()
	{
		$data = new template\data();

		$this->assert
			->variable($data->getParent())->isNull()
		;

		$data->setParent($tag = new template\tag(uniqid()));

		$this->assert
			->object($data->getParent())->isIdenticalTo($tag)
		;
	}

	public function testParentIsSet()
	{
		$data = new template\data();

		$this->assert
			->boolean($data->parentIsSet())->isFalse()
		;

		$data->setParent(new template\tag(uniqid()));

		$this->assert
			->boolean($data->parentIsSet())->isTrue()
		;
	}

	public function testUnsetParent()
	{
		$data = new template\data();

		$this->assert
			->object($data->unsetParent())->isIdenticalTo($data)
			->boolean($data->parentIsSet())->isFalse()
		;

		$data->setParent(new template\tag(uniqid()));

		$this->assert
			->object($data->unsetParent())->isIdenticalTo($data)
			->boolean($data->parentIsSet())->isFalse()
		;
	}

	public function testHasChildren()
	{
		$data = new template\data();

		$this->assert
			->boolean($data->hasChildren())->isFalse()
		;
	}

	public function testGetChildren()
	{
		$data = new template\data();

		$this->assert
			->array($data->getChildren())->isEmpty()
		;
	}

	public function testGetChild()
	{
		$data = new template\data();

		$this->assert
			->variable($data->getChild(0))->isNull()
			->variable($data->getChild(rand(1, PHP_INT_MAX)))->isNull()
		;
	}

	public function testGetById()
	{
		$data = new template\data();

		$this->assert
			->variable($data->getById(uniqid()))->isNull()
		;
	}

	public function testGetByTag()
	{
		$data = new template\data();

		$this->assert
			->array($data->getByTag(uniqid()))->isEmpty()
		;
	}

	public function testGetId()
	{
		$data = new template\data();

		$this->assert
			->variable($data->getId())->isNull()
		;
	}

	public function testGetTag()
	{
		$data = new template\data();

		$this->assert
			->variable($data->getTag())->isNull()
		;
	}

	public function testResetData()
	{
		$data = new template\data();

		$this->assert
			->object($data->resetData())->isIdenticalTo($data)
			->string($data->getData())->isEmpty()
		;

		$data = new template\data($string = uniqid());

		$this->assert
			->object($data->resetData())->isIdenticalTo($data)
			->string($data->getData())->isEmpty()
		;
	}

	public function testIsRoot()
	{
		$data = new template\data();

		$this->assert
			->boolean($data->isRoot())->isTrue()
		;

		$data->setParent(new template\tag(uniqid()));

		$this->assert
			->boolean($data->isRoot())->isFalse()
		;
	}

	public function testBuild()
	{
		$data = new template\data();

		$this->assert
			->object($data->build())->isIdenticalTo($data)
		;

		$data = new template\data(uniqid());

		$this->assert
			->object($data->build())->isIdenticalTo($data)
		;
	}
}
