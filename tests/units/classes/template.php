<?php

namespace mageekguy\atoum\tests\units;

use \mageekguy\atoum;

require_once(__DIR__ . '/../runner.php');

class template extends atoum\test
{
	public function test__construct()
	{
		$template = new atoum\template();

		$this->assert
			->object($template)->isInstanceOf('\iteratorAggregate')
			->string($template->getData())->isEmpty()
		;

		$template = new atoum\template($data = uniqid());

		$this->assert
			->object($template)->isInstanceOf('\iteratorAggregate')
			->string($template->getData())->isEqualTo($data)
		;
	}

	public function test__toString()
	{
		$template = new atoum\template();

		$this->assert
			->castToString($template)->isEmpty()
		;

		$template = new atoum\template($data = uniqid());

		$this->assert
			->castToString($template)->isEqualTo($data)
		;

		$template = new atoum\template();

		$this->assert
			->castToString($template->setData($data = uniqid()))->isEqualTo($data)
		;

		$template = new atoum\template();

		$this->assert
			->castToString($template->addChild(new atoum\template\data($data = uniqid())))->isEmpty()
		;

		$this->assert
			->castToString($template->build())->isEqualTo($data)
		;
	}

	public function test__get()
	{
		$template = new atoum\template();

		$this->assert
			->object($template->{uniqid()})
				->isInstanceOf('\mageekguy\atoum\template\iterator')
				->isEmpty()
		;

		$template->addChild($childTag = new atoum\template\tag(uniqid()));

		$this->assert
			->object($template->{$childTag->getTag()})->isInstanceOf('\mageekguy\atoum\template\iterator')
				->hasSize(1)
			->object($template->{$childTag->getTag()}->current())->isIdenticalTo($childTag)
		;

		$template->addChild($otherChildTag = new atoum\template\tag($childTag->getTag()));

		$this->assert
			->object($iterator = $template->{$childTag->getTag()})->isInstanceOf('\mageekguy\atoum\template\iterator')
				->hasSize(2)
			->object($iterator->current())->isIdenticalTo($childTag)
			->object($iterator->next()->current())->isIdenticalTo($otherChildTag)
		;

		$template->addChild($anotherChildTag = new atoum\template\tag(uniqid()));

		$this->assert
			->object($iterator = $template->{$childTag->getTag()})->isInstanceOf('\mageekguy\atoum\template\iterator')
				->hasSize(2)
			->object($iterator->current())->isIdenticalTo($childTag)
			->object($iterator->next()->current())->isIdenticalTo($otherChildTag)
			->object($otherIterator = $template->{$anotherChildTag->getTag()})->isInstanceOf('\mageekguy\atoum\template\iterator')
				->hasSize(1)
			->object($otherIterator->current())->isIdenticalTo($anotherChildTag)
		;

		$childTag->addChild($littleChildTag  = new atoum\template\tag($childTag->getTag()));

		$this->assert
			->object($iterator = $template->{$childTag->getTag()})->isInstanceOf('\mageekguy\atoum\template\iterator')
				->hasSize(3)
			->object($iterator->current())->isIdenticalTo($childTag)
			->object($iterator->next()->current())->isIdenticalTo($littleChildTag)
			->object($iterator->next()->current())->isIdenticalTo($otherChildTag)
			->object($otherIterator = $template->{$anotherChildTag->getTag()})->isInstanceOf('\mageekguy\atoum\template\iterator')
				->hasSize(1)
			->object($otherIterator->current())->isIdenticalTo($anotherChildTag)
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


		$this->assert
			->exception(function() use ($template, & $undefineTag) {
					$template->{$undefineTag = uniqid()} = uniqid();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Tag \'' . $undefineTag . '\' does not exist')
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

		$this->assert
			->exception(function() use ($template, & $tag) {
					unset($template->{$tag = uniqid()});
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
					->hasMessage('Tag \'' . $tag . '\' does not exist')
		;

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

		$template->addChild(new atoum\template\data($otherData = uniqid()));

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
			->array($template->getByTag(uniqid()))->isEmpty()
		;

		$template->addChild($tag = new atoum\template\tag(uniqid()));

		$this->assert
			->array($template->getByTag(uniqid()))->isEmpty()
			->array($template->getByTag($tag->getTag()))->isIdenticalTo(array($tag))
		;

		$template->addChild($otherTag = new atoum\template\tag($tag->getTag()));

		$this->assert
			->array($template->getByTag(uniqid()))->isEmpty()
			->array($template->getByTag($tag->getTag()))->isIdenticalTo(array($tag, $otherTag))
		;

		$tag->addChild($childTag = new atoum\template\tag($tag->getTag()));

		$this->assert
			->array($template->getByTag(uniqid()))->isEmpty()
			->array($template->getByTag($tag->getTag()))->isIdenticalTo(array($tag, $childTag, $otherTag))
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
		/*
		$template = new atoum\template();

		$this->assert->string($template->build())->isEmpty();

		$data = uniqid();
		$template = new atoum\template($data);

		$this->assert->string($template->build())->isEqualTo($data);

		$template = new atoum\template();
		$template->addChild(new atoum\template\data($data));

		$this->assert->string($template->build())->isEqualTo($data);
		$this->assert->string($template->build())->isEqualTo($data . $data);
		$this->assert->string($template->build())->isEqualTo($data . $data . $data);

		$template = new atoum\template($data);
		$template->addChild(new atoum\template\data($data));

		$this->assert->string($template->build())->isEqualTo($data . $data);
		$this->assert->string($template->build())->isEqualTo($data . $data . $data);
		$this->assert->string($template->build())->isEqualTo($data . $data . $data . $data);

		$template = new atoum\template();
		$tag = new atoum\template\tag(uniqid());
		$template->addChild($tag);
		$data = new atoum\template\data(uniqid());
		$tag->addChild($data);

		$this->assert->string($template->build())->isEmpty();
		$tag->build();
		$this->assert->string($template->build())->isEqualTo($data->getData());
		*/
	}

	/*
	public function testGetChild()
	{
		$template = new atoum\template();
		$this->assert->variable($template->getChild(0))->isNull();
		$this->assert->variable($template->getChild(rand(1, PHP_INT_MAX)))->isNull();

		$childData = new atoum\template\data();
		$template->addChild($childData);
		$this->assert->object($template->getChild(0))->isIdenticalTo($childData);
		$this->assert->variable($template->getChild(rand(1, PHP_INT_MAX)))->isNull();

		$childTag = new atoum\template\tag(uniqid());
		$template->addChild($childTag);
		$this->assert->object($template->getChild(0))->isIdenticalTo($childData);
		$this->assert->object($template->getChild(1))->isIdenticalTo($childTag);
		$this->assert->variable($template->getChild(rand(2, PHP_INT_MAX)))->isNull();
	}

	public function testCheckChild()
	{
		$template = new atoum\template();
		$childTag = new atoum\template\tag(uniqid());

		$this->assert->boolean($template->checkChild($childTag))->isTrue();

		$childTag->setId(uniqid());
		$this->assert->boolean($template->checkChild($childTag))->isTrue();
		$this->assert->boolean($childTag->checkChild($template))->isTrue();

		$littleChildTag = new atoum\template\tag(uniqid());
		$template->addChild($childTag);

		$this->assert->boolean($template->checkChild($littleChildTag))->isTrue();
		$this->assert->boolean($littleChildTag->checkChild($template))->isTrue();
		$this->assert->boolean($childTag->checkChild($littleChildTag))->isTrue();
		$this->assert->boolean($littleChildTag->checkChild($childTag))->isTrue();

		$littleChildTag->setId(uniqid());
		$this->assert->boolean($template->checkChild($littleChildTag))->isTrue();
		$this->assert->boolean($littleChildTag->checkChild($template))->isTrue();
		$this->assert->boolean($childTag->checkChild($littleChildTag))->isTrue();
		$this->assert->boolean($littleChildTag->checkChild($childTag))->isTrue();

		$template->deleteChild($childTag);
		$childTag->unsetId()->addChild($littleChildTag->unsetId());

		$this->assert->boolean($template->checkChild($childTag))->isTrue();
		$this->assert->boolean($childTag->checkChild($template))->isTrue();
		$this->assert->boolean($template->checkChild($childTag))->isTrue();
		$this->assert->boolean($childTag->checkChild($template))->isTrue();
		$this->assert->boolean($littleChildTag->checkChild($template))->isTrue();
		$this->assert->boolean($template->checkChild($littleChildTag))->isTrue();
	}

	public function testGetIterator()
	{
		$template = new atoum\template\tag(uniqid());
		$this->assert
			->iterator($template->getIterator())
				->isInstanceOf('ogoHtmlTemplateIterator')
				->and
				->isEmpty()
		;

		$childTag = new atoum\template\tag(uniqid());
		$template->addChild($childTag);

		$this->assert->iterator($template->getIterator())->hasSize(1);
		$this->assert->object($template->getIterator()->current())->isIdenticalTo($childTag);

		$otherChildTag = new atoum\template\tag(uniqid());
		$template->addChild($otherChildTag);
		$iterator = $template->getIterator();

		$this->assert
			->iterator($iterator)
				->hasSize(2)
				->and
				->integer($iterator->key())->isEqualTo(0)
				->and
				->object($iterator->current())->isIdenticalTo($childTag)
		;

		$iterator->next();

		$this->assert
			->integer($iterator->key())->isEqualTo(1)
			->and
			->object($iterator->current())->isIdenticalTo($otherChildTag)
		;
	}
	*/
}

?>
