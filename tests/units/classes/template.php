<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../runner.php';

class template extends atoum\test
{
	public function test__construct()
	{
		$template = new atoum\template();

		$this->assert
			->string($template->getData())->isEmpty()
		;

		$template = new atoum\template($data = uniqid());

		$this->assert
			->string($template->getData())->isEqualTo($data)
		;
	}

	public function test__toString()
	{
		$template = new atoum\template();

		$this->assert
			->string($template->getData())->isEmpty()
			->boolean($template->hasChildren())->isFalse()
			->variable($template->getId())->isNull()
			->variable($template->getTag())->isNull()
		;

		$template = new atoum\template($data = uniqid());

		$this->assert
			->string($template->getData())->isEqualTo($data)
			->boolean($template->hasChildren())->isFalse()
			->variable($template->getId())->isNull()
			->variable($template->getTag())->isNull()
		;
	}

	public function test__get()
	{
		$template = new atoum\template();

		$this->assert
			->object($iterator = $template->{uniqid()})->isInstanceOf('mageekguy\atoum\template\iterator')
			->sizeOf($iterator)->isZero()
		;

		$template->addChild($childTag = new atoum\template\tag(uniqid()));

		$this->assert
			->object($iterator = $template->{$childTag->getTag()})->isInstanceOf('mageekguy\atoum\template\iterator')
			->sizeOf($iterator)->isEqualTo(1)
			->object($iterator->current())->isIdenticalTo($childTag)
		;

		$template->addChild($otherChildTag = new atoum\template\tag($childTag->getTag()));

		$this->assert
			->object($iterator = $template->{$childTag->getTag()})->isInstanceOf('mageekguy\atoum\template\iterator')
			->sizeOf($iterator)->isEqualTo(2)
			->object($iterator->current())->isIdenticalTo($childTag)
			->object($iterator->next()->current())->isIdenticalTo($otherChildTag)
		;

		$template->addChild($anotherChildTag = new atoum\template\tag(uniqid()));

		$this->assert
			->object($iterator = $template->{$childTag->getTag()})->isInstanceOf('mageekguy\atoum\template\iterator')
			->sizeOf($iterator)->isEqualTo(2)
			->object($iterator->current())->isIdenticalTo($childTag)
			->object($iterator->next()->current())->isIdenticalTo($otherChildTag)
			->object($iterator = $template->{$anotherChildTag->getTag()})->isInstanceOf('mageekguy\atoum\template\iterator')
			->sizeOf($iterator)->isEqualTo(1)
			->object($iterator->current())->isIdenticalTo($anotherChildTag)
		;

		$childTag->addChild($littleChildTag  = new atoum\template\tag($childTag->getTag()));

		$this->assert
			->object($iterator = $template->{$childTag->getTag()})->isInstanceOf('mageekguy\atoum\template\iterator')
			->sizeOf($iterator)->isEqualTo(3)
			->object($iterator->current())->isIdenticalTo($childTag)
			->object($iterator->next()->current())->isIdenticalTo($littleChildTag)
			->object($iterator->next()->current())->isIdenticalTo($otherChildTag)
			->object($iterator = $template->{$anotherChildTag->getTag()})->isInstanceOf('mageekguy\atoum\template\iterator')
			->sizeOf($iterator)->isEqualTo(1)
			->object($iterator->current())->isIdenticalTo($anotherChildTag)
		;
	}

	public function test__set()
	{
		$template = new atoum\template();

		$template
			->addChild($tag  = new atoum\template\tag(uniqid()))
			->{$tag->getTag()} = $data = uniqid()
		;

		$this->assert
			->string($tag->getData())->isEqualTo($data)
		;

		$tag->addChild($childTag  = new atoum\template\tag($tag->getTag()));

		$template->{$tag->getTag()} = $data;

		$this->assert
			->string($tag->getData())->isEqualTo($data)
			->string($childTag->getData())->isEqualTo($data)
		;

		$tag->addChild($otherChildTag = new atoum\template\tag(uniqid()));

		$template->{$otherChildTag->getTag()} = $otherData = uniqid();

		$this->assert
			->string($tag->getData())->isEqualTo($data)
			->string($childTag->getData())->isEqualTo($data)
			->string($otherChildTag->getData())->isEqualTo($otherData)
		;
	}

	public function test__isset()
	{
		$template = new atoum\template();

		$this->assert
			->boolean(isset($template->{uniqid()}))->isFalse()
		;

		$template->addChild($childTag = new atoum\template\tag(uniqid()));

		$this->assert
			->boolean(isset($template->{uniqid()}))->isFalse()
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
		;

		$childTag->addChild($otherChildTag = new atoum\template\tag(uniqid()));

		$this->assert
			->boolean(isset($template->{uniqid()}))->isFalse()
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
			->boolean(isset($template->{$otherChildTag->getTag()}))->isTrue()
		;

		$childTag->addChild($littleChildTag = new atoum\template\tag(uniqid()));

		$this->assert
			->boolean(isset($template->{uniqid()}))->isFalse()
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
			->boolean(isset($template->{$otherChildTag->getTag()}))->isTrue()
			->boolean(isset($template->{$littleChildTag->getTag()}))->isTrue()
		;
	}

	public function test__unset()
	{
		$template = new atoum\template();

		$template->addChild($childTag = new atoum\template\tag(uniqid()));

		$this->assert
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
			->string($childTag->getData())->isEmpty()
		;

		unset($template->{$childTag->getTag()});

		$this->assert
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
			->string($childTag->getData())->isEmpty()
		;

		$template->{$childTag->getTag()} = uniqid();

		$this->assert
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
			->string($childTag->getData())->isNotEmpty()
		;

		unset($template->{$childTag->getTag()});

		$this->assert
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
			->string($childTag->getData())->isEmpty()
		;


		$template->addChild($otherChildTag = new atoum\template\tag(uniqid()));

		$this->assert
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
			->string($childTag->getData())->isEmpty()
			->boolean(isset($template->{$otherChildTag->getTag()}))->isTrue()
			->string($otherChildTag->getData())->isEmpty()
		;

		unset($template->{$childTag->getTag()});
		unset($template->{$otherChildTag->getTag()});

		$this->assert
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
			->string($childTag->getData())->isEmpty()
			->boolean(isset($template->{$otherChildTag->getTag()}))->isTrue()
			->string($otherChildTag->getData())->isEmpty()
		;

		$template->{$childTag->getTag()} = uniqid();
		$template->{$otherChildTag->getTag()} = uniqid();

		$this->assert
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
			->string($childTag->getData())->isNotEmpty()
			->boolean(isset($template->{$otherChildTag->getTag()}))->isTrue()
			->string($otherChildTag->getData())->isNotEmpty()
		;

		unset($template->{$childTag->getTag()});

		$this->assert
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
			->string($childTag->getData())->isEmpty()
			->boolean(isset($template->{$otherChildTag->getTag()}))->isTrue()
			->string($otherChildTag->getData())->isNotEmpty()
		;

		unset($template->{$otherChildTag->getTag()});

		$this->assert
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
			->string($childTag->getData())->isEmpty()
			->boolean(isset($template->{$otherChildTag->getTag()}))->isTrue()
			->string($otherChildTag->getData())->isEmpty()
		;

		$childTag->addChild($littleChildTag = new atoum\template\tag(uniqid()));

		$this->assert
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
			->string($childTag->getData())->isEmpty()
			->boolean(isset($template->{$otherChildTag->getTag()}))->isTrue()
			->string($otherChildTag->getData())->isEmpty()
			->boolean(isset($template->{$littleChildTag->getTag()}))->isTrue()
			->string($littleChildTag->getData())->isEmpty()
		;

		$template->{$childTag->getTag()} = uniqid();
		$template->{$otherChildTag->getTag()} = uniqid();
		$template->{$littleChildTag->getTag()} = uniqid();

		$this->assert
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
			->string($childTag->getData())->isNotEmpty()
			->boolean(isset($template->{$otherChildTag->getTag()}))->isTrue()
			->string($otherChildTag->getData())->isNotEmpty()
			->boolean(isset($template->{$littleChildTag->getTag()}))->isTrue()
			->string($littleChildTag->getData())->isNotEmpty()
		;

		unset($template->{$childTag->getTag()});

		$this->assert
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
			->string($childTag->getData())->isEmpty()
			->boolean(isset($template->{$otherChildTag->getTag()}))->isTrue()
			->string($otherChildTag->getData())->isNotEmpty()
			->boolean(isset($template->{$littleChildTag->getTag()}))->isTrue()
			->string($littleChildTag->getData())->isNotEmpty()
		;

		unset($template->{$otherChildTag->getTag()});

		$this->assert
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
			->string($childTag->getData())->isEmpty()
			->boolean(isset($template->{$otherChildTag->getTag()}))->isTrue()
			->string($otherChildTag->getData())->isEmpty()
			->boolean(isset($template->{$littleChildTag->getTag()}))->isTrue()
			->string($littleChildTag->getData())->isNotEmpty()
		;

		unset($template->{$littleChildTag->getTag()});

		$this->assert
			->boolean(isset($template->{$childTag->getTag()}))->isTrue()
			->string($childTag->getData())->isEmpty()
			->boolean(isset($template->{$otherChildTag->getTag()}))->isTrue()
			->string($otherChildTag->getData())->isEmpty()
			->boolean(isset($template->{$littleChildTag->getTag()}))->isTrue()
			->string($littleChildTag->getData())->isEmpty()
		;
	}

	public function testGetRoot()
	{
		$template = new atoum\template();

		$this->assert
			->object($template->getRoot())->isIdenticalTo($template)
		;

		$template->addChild($childTemplate = new atoum\template());

		$this->assert
			->object($template->getRoot())->isIdenticalTo($template)
			->object($childTemplate->getRoot())->isIdenticalTo($template)
		;

		$childTemplate->addChild($littleChildTemplate = new atoum\template());

		$this->assert
			->object($template->getRoot())->isIdenticalTo($template)
			->object($childTemplate->getRoot())->isIdenticalTo($template)
			->object($littleChildTemplate->getRoot())->isIdenticalTo($template)
		;
	}

	public function testGetParent()
	{
		$template = new atoum\template();

		$this->assert
			->variable($template->getParent())->isNull()
		;

		$template->addChild($childTemplate = new atoum\template());

		$this->assert
			->variable($template->getParent())->isNull()
			->object($childTemplate->getParent())->isIdenticalTo($template)
		;

		$childTemplate->addChild($littleChildTemplate = new atoum\template());

		$this->assert
			->variable($template->getParent())->isNull()
			->object($childTemplate->getParent())->isIdenticalTo($template)
			->object($littleChildTemplate->getParent())->isIdenticalTo($childTemplate)
		;
	}

	public function testParentIsSet()
	{
		$template = new atoum\template();

		$this->assert
			->boolean($template->parentIsSet())->isFalse()
		;

		$childTemplate = new atoum\template();

		$this->assert
			->boolean($template->parentIsSet())->isFalse()
			->boolean($childTemplate->parentIsSet())->isFalse()
		;

		$template->addChild($childTemplate);

		$this->assert
			->boolean($template->parentIsSet())->isFalse()
			->boolean($childTemplate->parentIsSet())->isTrue()
		;

		$littleChildTemplate = new atoum\template();

		$this->assert
			->boolean($template->parentIsSet())->isFalse()
			->boolean($childTemplate->parentIsSet())->isTrue()
			->boolean($littleChildTemplate->parentIsSet())->isFalse()
		;

		$childTemplate->addChild($littleChildTemplate);

		$this->assert
			->boolean($template->parentIsSet())->isFalse()
			->boolean($childTemplate->parentIsSet())->isTrue()
			->boolean($littleChildTemplate->parentIsSet())->isTrue()
		;
	}

	public function testUnsetParent()
	{
		$template = new atoum\template();

		$this->assert
			->boolean($template->parentIsSet())->isFalse()
			->object($template->unsetParent())->isIdenticalTo($template)
			->boolean($template->parentIsSet())->isFalse()
		;

		$template->addChild($childTemplate = new atoum\template());

		$this->assert
			->boolean($template->parentIsSet())->isFalse()
			->boolean($childTemplate->parentIsSet())->isTrue()
			->object($template->unsetParent())->isIdenticalTo($template)
			->object($childTemplate->unsetParent())->isIdenticalTo($childTemplate)
			->boolean($template->parentIsSet())->isFalse()
			->boolean($childTemplate->parentIsSet())->isFalse()
		;
	}

	public function testSetParent()
	{
		$childTemplate = new atoum\template();

		$this->assert
			->boolean($childTemplate->parentIsSet())->isFalse()
			->object($childTemplate->setParent(new atoum\template()))->isIdenticalTo($childTemplate)
			->boolean($childTemplate->parentIsSet())->isTrue()
		;
	}

	public function testGetData()
	{
		$template = new atoum\template();

		$this->assert
			->string($template->getData())->isEmpty()
		;

		$template = new atoum\template($data = uniqid());

		$this->assert
			->string($template->getData())->isEqualTo($data)
		;

		$template = new atoum\template();
		$template->setData($data = uniqid());

		$this->assert
			->string($template->getData())->isEqualTo($data)
		;

		$template->addChild(new atoum\template($otherData = uniqid()));

		$this->assert
			->string($template->getData())->isEqualTo($data)
		;

		$template->build();

		$this->assert
			->string($template->getData())->isEqualTo($data . $otherData)
		;
	}

	public function testSetData()
	{
		$template = new atoum\template();

		$this->assert
			->object($template->setData($data = uniqid()))->isIdenticalTo($template)
			->string($template->getData())->isEqualTo($data)
		;
	}

	public function testAddData()
	{
		$template = new atoum\template();

		$this->assert
			->object($template->addData($data = uniqid()))->isIdenticalTo($template)
			->string($template->getData())->isEqualTo($data)
			->object($template->addData($data))->isIdenticalTo($template)
			->string($template->getData())->isEqualTo($data . $data)
			->object($template->addData($otherData = uniqid()))->isIdenticalTo($template)
			->string($template->getData())->isEqualTo($data . $data . $otherData)
		;
	}

	public function testResetData()
	{
		$template = new atoum\template();

		$this->assert
			->string($template->getData())->isEmpty()
			->object($template->resetData())->isIdenticalTo($template)
			->string($template->getData())->isEmpty()
		;

		$template = new atoum\template($data = uniqid());

		$this->assert
			->string($template->getData())->isEqualTo($data)
			->object($template->resetData())->isIdenticalTo($template)
			->string($template->getData())->isEmpty()
		;
	}

	public function testGetId()
	{
		$template = new atoum\template();

		$this->assert
			->variable($template->getId())->isNull()
		;
	}

	public function testIsRoot()
	{
		$template = new atoum\template();

		$this->assert
			->boolean($template->isRoot())->isTrue()
		;
	}

	public function testIsChild()
	{
		$template = new atoum\template();

		$childTemplate = new atoum\template();

		$this->assert
			->boolean($template->isChild($childTemplate))->isFalse()
		;

		$template->addChild($childTemplate);

		$this->assert
			->boolean($template->isChild($childTemplate))->isTrue()
		;
	}

	public function testGetTag()
	{
		$template = new atoum\template();

		$this->assert
			->variable($template->getTag())->isNull()
		;
	}

	public function testGetChildren()
	{
		$template = new atoum\template();

		$this->assert
			->array($template->getChildren())->isEmpty()
		;

		$template->addChild($childTemplate = new atoum\template());

		$this->assert
			->array($template->getChildren())->isIdenticalTo(array($childTemplate))
		;

		$template->addChild($otherChildTemplate = new atoum\template());

		$this->assert
			->array($template->getChildren())->isIdenticalTo(array($childTemplate, $otherChildTemplate))
		;

		$childTemplate->addChild($littleChildTemplate = new atoum\template());

		$this->assert
			->array($template->getChildren())->isIdenticalTo(array($childTemplate, $otherChildTemplate))
		;
	}

	public function testAddChild()
	{
		$template = new atoum\template();

		$this->assert
			->boolean($template->hasChildren())->isFalse()
			->object($template->addChild($childTemplate = new atoum\template()))->isIdenticalTo($template)
			->object($childTemplate->getParent())->isIdenticalTo($template)
			->array($template->getChildren())->isIdenticalTo(array($childTemplate))
			->object($template->addChild($childTemplate))->isIdenticalTo($template)
			->object($childTemplate->getParent())->isIdenticalTo($template)
			->array($template->getChildren())->isIdenticalTo(array($childTemplate))
		;

		$otherTemplate = new atoum\template();
		$otherTemplate->addChild($otherChildTemplate = new atoum\template());

		$this->assert
			->array($otherTemplate->getChildren())->isIdenticalTo(array($otherChildTemplate))
			->object($otherChildTemplate->getParent())->isIdenticalTo($otherTemplate)
			->object($template->addChild($otherChildTemplate))->isIdenticalTo($template)
			->array($otherTemplate->getChildren())->isEmpty()
			->object($otherChildTemplate->getParent())->isIdenticalTo($template)
			->array($template->getChildren())->isIdenticalTo(array($childTemplate, $otherChildTemplate))
		;

		$template = new atoum\template();
		$templateWithId = new atoum\template\tag(uniqid());
		$templateWithId->setId($id = uniqid());

		$templateWithSameId = clone $templateWithId;

		$this->assert
			->boolean($template->hasChildren())->isFalse()
			->object($template->addChild($templateWithId))->isIdenticalTo($template)
			->array($template->getChildren())->isIdenticalTo(array($templateWithId))
			->object($template->addChild($templateWithId))->isIdenticalTo($template)
			->array($template->getChildren())->isIdenticalTo(array($templateWithId))
			->exception(function() use ($template, $templateWithSameId) {
						$template->addChild($templateWithSameId);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Id \'' . $id . '\' is already defined')
		;
	}

	public function testDeleteChild()
	{
		$template = new atoum\template();
		$template->addChild($childTemplate = new atoum\template());

		$this->assert
			->object($childTemplate->getParent())->isIdenticalTo($template)
			->array($template->getChildren())->isIdenticalTo(array($childTemplate))
			->object($template->deleteChild($childTemplate))->isIdenticalTo($template)
			->variable($childTemplate->getParent())->isNull()
			->array($template->getChildren())->isEmpty()
		;
	}

	public function testHasChildren()
	{
		$template = new atoum\template();

		$this->assert
			->boolean($template->hasChildren())->isFalse()
		;

		$template->addChild($childTemplate = new atoum\template());

		$this->assert
			->boolean($template->hasChildren())->isTrue()
		;

		$template->deleteChild($childTemplate);

		$this->assert
			->boolean($template->hasChildren())->isFalse()
		;
	}

	public function testGetByTag()
	{
		$template = new atoum\template();

		$this->assert
			->object($iterator = $template->getByTag(uniqid()))->isInstanceOf('mageekguy\atoum\template\iterator')
			->sizeOf($iterator)->isZero()
		;

		$template->addChild($tag = new atoum\template\tag(uniqid()));

		$this->assert
			->object($iterator = $template->getByTag(uniqid()))->isInstanceOf('mageekguy\atoum\template\iterator')
			->sizeOf($iterator)->isZero()
			->object($iterator = $template->getByTag($tag->getTag()))->isInstanceOf('mageekguy\atoum\template\iterator')
			->sizeOf($iterator)->isEqualTo(1)
			->object($iterator->current())->isIdenticalTo($tag)
		;

		$template->addChild($otherTag = new atoum\template\tag($tag->getTag()));

		$this->assert
			->object($iterator = $template->getByTag(uniqid()))->isInstanceOf('mageekguy\atoum\template\iterator')
			->sizeOf($iterator)->isZero()
			->object($iterator = $template->getByTag($tag->getTag()))->isInstanceOf('mageekguy\atoum\template\iterator')
			->sizeOf($iterator)->isEqualTo(2)
			->object($iterator->current())->isIdenticalTo($tag)
			->object($iterator->next()->current())->isIdenticalTo($otherTag)
		;

		$tag->addChild($childTag = new atoum\template\tag($tag->getTag()));

		$this->assert
			->object($iterator = $template->getByTag(uniqid()))->isInstanceOf('mageekguy\atoum\template\iterator')
			->sizeOf($iterator)->isZero()
			->object($iterator = $template->getByTag($tag->getTag()))->isInstanceOf('mageekguy\atoum\template\iterator')
			->sizeOf($iterator)->isEqualTo(3)
			->object($iterator->current())->isIdenticalTo($tag)
			->object($iterator->next()->current())->isIdenticalTo($childTag)
			->object($iterator->next()->current())->isIdenticalTo($otherTag)
		;
	}

	public function testGetById()
	{
		$template = new atoum\template();

		$this->assert
			->variable($template->getById(uniqid()))->isNull()
		;

		$tag = new atoum\template\tag(uniqid());
		$template->addChild($tag->setId($id = uniqid()));

		$this->assert
			->variable($template->getById(uniqid()))->isNull()
			->object($template->getById($id))->isIdenticalTo($tag)
		;

		$childTag = new atoum\template\tag(uniqid());
		$tag->addChild($childTag->setId($childId = uniqid()));

		$this->assert
			->variable($template->getById(uniqid()))->isNull()
			->object($template->getById($id))->isIdenticalTo($tag)
			->object($tag->getById($id))->isIdenticalTo($tag)
			->object($template->getById($childId))->isIdenticalTo($childTag)
			->object($tag->getById($childId))->isIdenticalTo($childTag)
			->object($childTag->getById($childId))->isIdenticalTo($childTag)
			->variable($childTag->getById($id, false))->isNull()
		;
	}

	public function testBuild()
	{
		$template = new atoum\template();

		$this->assert
			->string($template->getData())->isEmpty()
			->boolean($template->hasChildren())->isFalse()
			->object($template->build())->isIdenticalTo($template)
			->string($template->getData())->isEmpty()
		;

		$template = new atoum\template($data = uniqid());

		$this->assert
			->string($template->getData())->isEqualTo($data)
			->boolean($template->hasChildren())->isFalse()
			->object($template->build())->isIdenticalTo($template)
			->string($template->getData())->isEqualTo($data)
			->object($template->build())->isIdenticalTo($template)
			->string($template->getData())->isEqualTo($data)
		;

		$template->addChild($childTemplate = new atoum\template($childData = uniqid()));

		$this->assert
			->string($template->getData())->isEqualTo($data)
			->string($childTemplate->getData())->isEqualTo($childData)
			->array($template->getChildren())->isIdenticalTo(array($childTemplate))
			->object($template->build())->isIdenticalTo($template)
			->string($template->getData())->isEqualTo($data . $childData)
			->string($childTemplate->getData())->isEqualTo($childData)
			->object($template->build())->isIdenticalTo($template)
			->string($template->getData())->isEqualTo($data . $childData . $childData)
		;
	}

	public function testGetChild()
	{
		$template = new atoum\template();

		$this->assert
			->variable($template->getChild(0))->isNull()
			->variable($template->getChild(rand(1, PHP_INT_MAX)))->isNull()
			->variable($template->getChild(- rand(1, PHP_INT_MAX)))->isNull()
		;

		$template->addChild($childTemplate = new atoum\template());

		$this->assert
			->variable($template->getChild(0))->isIdenticalTo($childTemplate)
			->variable($template->getChild(rand(1, PHP_INT_MAX)))->isNull()
			->variable($template->getChild(- rand(1, PHP_INT_MAX)))->isNull()
		;
	}
}
