<?php

namespace mageekguy\atoum\tests\units;

use \mageekguy\atoum;

require_once(__DIR__ . '/../../runner.php');

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
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('There is no tag \'' . $undefineTag . '\' defined')
		;
	}

	/*
	public function test__isset()
	{
		$template = new atoum\template();
		$this->assert->boolean(isset($template->{uniqid()}))->isFalse();

		$tag = new atoum\template\tag(uniqid());
		$template->addChild($tag);
		$this->assert->boolean(isset($template->{$tag->getTag()}))->isTrue();

		$childTag = new atoum\template\tag(uniqid());
		$tag->addChild($childTag);
		$this->assert->boolean(isset($template->{$tag->getTag()}))->isTrue();
		$this->assert->boolean(isset($template->{$childTag->getTag()}))->isTrue();

		$littleChildTag = new atoum\template\tag(uniqid());
		$childTag->addChild($littleChildTag);
		$this->assert->boolean(isset($template->{$tag->getTag()}))->isTrue();
		$this->assert->boolean(isset($template->{$childTag->getTag()}))->isTrue();
		$this->assert->boolean(isset($template->{$littleChildTag->getTag()}))->isTrue();
	}

	public function test__unset()
	{
		$template = new atoum\template();
		$tag = uniqid();

		unset($template->{$tag});

		$this->assert->error(E_USER_WARNING, 'There is no tag \'' . $tag . '\' defined')->exists();

		$tag = new atoum\template\tag(uniqid());
		$template->addChild($tag);
		$template->{$tag->getTag()} = uniqid();

		unset($template->{$tag->getTag()});

		$this->assert->string($tag->getData())->isEmpty();

		$childTag = new atoum\template\tag($tag->getTag());
		$tag->addChild($childTag);
		$template->{$tag->getTag()} = uniqid();

		unset($template->{$tag->getTag()});

		$this->assert->string($tag->getData())->isEmpty();
		$this->assert->string($childTag->getData())->isEmpty();

		$otherChildTag = new atoum\template\tag(uniqid());
		$tag->addChild($otherChildTag);
		$template->{$tag->getTag()} = uniqid();
		$data = uniqid();
		$template->{$otherChildTag->getTag()} = $data;

		unset($template->{$tag->getTag()});

		$this->assert->string($tag->getData())->isEmpty();
		$this->assert->string($childTag->getData())->isEmpty();
		$this->assert->string($otherChildTag->getData())->isEqualTo($data);

		$littleChildTag = new atoum\template\tag($tag->getTag());
		$childTag->addChild($littleChildTag);
		$template->{$tag->getTag()} = uniqid();

		unset($template->{$tag->getTag()});

		$this->assert->string($tag->getData())->isEmpty();
		$this->assert->string($childTag->getData())->isEmpty();
		$this->assert->string($littleChildTag->getData())->isEmpty();
		$this->assert->string($otherChildTag->getData())->isEqualTo($data);
	}

	public function testGetRoot()
	{
		$template = new atoum\template();
		$this->assert->object($template->getRoot())->isIdenticalTo($template);
	}

	public function testGetParent()
	{
		$template = new atoum\template();
		$this->assert->variable($template->getParent())->isNull();
	}

	public function testParentIsSet()
	{
		$template = new atoum\template();
		$this->assert->boolean($template->parentIsSet())->isFalse();
	}

	public function testUnsetParent()
	{
		$template = new atoum\template();
		$this->assert->object($template->unsetParent())->isIdenticalTo($template);
	}

	public function testSetParent()
	{
		$template = new atoum\template();
		$template->setParent(new atoum\template());
		$this->assert->error(E_USER_ERROR, 'Object of class \'template\root\' can not have any parent')->exists();
	}

	public function testGetData()
	{
		$template = new atoum\template();
		$this->assert->string($template->getData())->isEmpty();

		$data = uniqid();
		$template = new atoum\template($data);
		$this->assert->string($template->getData())->isEqualTo($data);

		$otherData = uniqid();
		$template->addChild(new atoum\template\data($otherData));
		$this->assert->string($template->getData())->isEqualTo($data);
		$template->build();
		$this->assert->string($template->getData())->isEqualTo($data . $otherData);
	}

	public function testSetData()
	{
		$template = new atoum\template();
		$data = uniqid();
		$this->assert->object($template->setData($data))->isIdenticalTo($template);
		$this->assert->string($template->getData())->isEqualTo($data);

		$otherData = uniqid();
		$template->addChild(new atoum\template\data($otherData));
		$this->assert->string($template->getData())->isEqualTo($data);
		$template->build();
		$this->assert->string($template->getData())->isEqualTo($data . $otherData);
	}

	public function testAddData()
	{
		$template = new atoum\template();

		$data = uniqid();
		$this->assert->object($template->addData($data))->isIdenticalTo($template);
		$this->assert->string($template->getData())->isEqualTo($data);

		$otherData = uniqid();
		$this->assert->object($template->addData($otherData))->isIdenticalTo($template);
		$this->assert->string($template->getData())->isEqualTo($data . $otherData);
	}

	public function testResetData()
	{
		$template = new atoum\template();
		$this->assert->object($template->resetData())->isIdenticalTo($template);
		$this->assert->string($template->getData())->isEmpty();

		$template = new atoum\template(uniqid());
		$this->assert->object($template->resetData())->isIdenticalTo($template);
		$this->assert->string($template->getData())->isEmpty();

		$template->setData(uniqid());
		$this->assert->object($template->resetData())->isIdenticalTo($template);
		$this->assert->string($template->getData())->isEmpty();

		$template->addChild(new atoum\template\data(uniqid()))->build();
		$this->assert->object($template->resetData())->isIdenticalTo($template);
		$this->assert->string($template->getData())->isEmpty();
	}

	public function testGetId()
	{
		$template = new atoum\template();
		$this->assert->variable($template->getId())->isNull();
	}

	public function testGetTag()
	{
		$template = new atoum\template();
		$this->assert->variable($template->getTag())->isNull();
	}

	public function testGetChildren()
	{
		$template = new atoum\template();
		$this->assert->array($template->getChildren())->isEmpty();

		$tag = new atoum\template\tag(uniqid());
		$template->addChild($tag);

		$this->assert->array($template->getChildren())->isEqualTo(array($tag));

		$childTag = new atoum\template\tag(uniqid());
		$tag->addChild($childTag);

		$this->assert->array($template->getChildren())->isEqualTo(array($tag));

		$otherTag = new atoum\template\tag(uniqid());
		$template->addChild($otherTag);

		$this->assert->array($template->getChildren())->isEqualTo(array($tag, $otherTag));
	}

	public function testAddChild()
	{
		$template = new atoum\template();

		# Add data whit no parent
		$data = new atoum\template\data(uniqid());
		$this->assert->object($template->addChild($data))->isIdenticalTo($template);
		$this->assert->object($data->getParent())->isIdenticalTo($template);
		$this->assert->array($template->getChildren())->isEqualTo(array($data));

		# Add same data twice
		$this->assert->object($template->addChild($data))->isIdenticalTo($template);
		$this->assert->object($data->getParent())->isIdenticalTo($template);
		$this->assert->array($template->getChildren())->isEqualTo(array($data));

		# Add data with an other root
		$otherRoot = new atoum\template();
		$otherData = new atoum\template\data(uniqid());
		$otherRoot->addChild($otherData);
		$this->assert->object($template->addChild($otherData))->isIdenticalTo($template);
		$this->assert->object($otherData->getParent())->isIdenticalTo($template);
		$this->assert->array($template->getChildren())->isEqualTo(array($data, $otherData));
		$this->assert->array($otherRoot->getChildren())->isEqualTo(array());

		# Add tag with no parent
		$tag = new atoum\template\tag(uniqid());
		$this->assert->object($template->addChild($tag))->isIdenticalTo($template);
		$this->assert->object($tag->getParent())->isIdenticalTo($template);
		$this->assert->array($template->getChildren())->isEqualTo(array($data, $otherData, $tag));

		# Add tag with a parent
		$parentTag = new atoum\template\tag(uniqid());
		$childTag = new atoum\template\tag(uniqid());
		$parentTag->addChild($childTag);
		$this->assert->object($template->addChild($childTag))->isIdenticalTo($template);
		$this->assert->object($childTag->getParent())->isIdenticalTo($template);
		$this->assert->array($template->getChildren())->isEqualTo(array($data, $otherData, $tag, $childTag));
		$this->assert->array($parentTag->getChildren())->isEqualTo(array());

		# Add tag with an other root
		$otherTag = new atoum\template\tag(uniqid());
		$otherRoot->addChild($otherTag);
		$this->assert->object($template->addChild($otherTag))->isIdenticalTo($template);
		$this->assert->array($template->getChildren())->isEqualTo(array($data, $otherData, $tag, $childTag, $otherTag));
		$this->assert->array($template->getByTag($otherTag->getTag()))->isEqualTo(array($otherTag));
		$this->assert->array($otherRoot->getChildren())->isEmpty();
		$this->assert->array($otherRoot->getByTag($otherTag->getTag()))->isEmpty();
		$this->assert->object($otherTag->getParent())->isIdenticalTo($template);
	}

	public function testDeleteChild()
	{
		$template = new atoum\template();

		# Delete data
		$data = new atoum\template\data(uniqid());
		$template->addChild($data);
		$this->assert->object($template->deleteChild($data))->isIdenticalTo($template);
		$this->assert->variable($data->getParent())->isNull();
		$this->assert->array($template->getChildren())->isEmpty();

		# Delete tag
		$tag = new atoum\template\tag(uniqid());
		$template->addChild($tag);
		$this->assert->object($template->deleteChild($tag))->isIdenticalTo($template);
		$this->assert->variable($tag->getParent())->isNull();
		$this->assert->array($template->getChildren())->isEmpty();
	}

	public function testHasChildren()
	{
		$template = new atoum\template();
		$this->assert->boolean($template->hasChildren())->isFalse();

		$template->addChild(new atoum\template\data());
		$this->assert->boolean($template->hasChildren())->isTrue();

		$template->deleteChild($template->getChild(0));
		$this->assert->boolean($template->hasChildren())->isFalse();
	}

	public function testGetByTag()
	{
		$template = new atoum\template();

		$this->assert->array($template->getByTag(uniqid()))->isEmpty();

		$tag = new atoum\template\tag(uniqid());
		$template->addChild($tag);
		$this->assert->array($template->getByTag($tag->getTag()))->isEqualTo(array($tag));

		$otherTag = new atoum\template\tag($tag->getTag());
		$template->addChild($otherTag);
		$this->assert->array($template->getByTag($tag->getTag()))->isEqualTo(array($tag, $otherTag));

		$childTag = new atoum\template\tag($tag->getTag());
		$tag->addChild($childTag);
		$this->assert->array($template->getByTag($tag->getTag()))->isEqualTo(array($tag, $childTag, $otherTag));

		$littleChildTag = new atoum\template\tag($tag->getTag());
		$childTag->addChild($littleChildTag);
		$this->assert->array($template->getByTag($tag->getTag()))->isEqualTo(array($tag, $childTag, $littleChildTag, $otherTag));

		$antoherTag = new atoum\template\tag(uniqid());
		$template->addChild($antoherTag);
		$this->assert->array($template->getByTag($tag->getTag()))->isEqualTo(array($tag, $childTag, $littleChildTag, $otherTag));
	}

	public function testGetById()
	{
		$template = new atoum\template();

		$this->assert->variable($template->getById(uniqid()))->isNull();

		$tag = new atoum\template\tag(uniqid());
		$id = uniqid();
		$tag->setId($id);
		$template->addChild($tag);

		$this->assert->object($template->getById($id))->isIdenticalTo($tag);

		$otherTag = new atoum\template\tag(uniqid());
		$template->addChild($otherTag);

		$this->assert->object($template->getById($id))->isIdenticalTo($tag);

		$childTag = new atoum\template\tag(uniqid());
		$id = uniqid();
		$childTag->setId($id);
		$tag->addChild($childTag);

		$this->assert->object($template->getById($id))->isIdenticalTo($childTag);
		$this->assert->variable($template->getById(uniqid()))->isNull();
	}

	public function testBuild()
	{
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
	}

	public function testIsRoot()
	{
		$template = new atoum\template();
		$this->assert->boolean($template->isRoot())->isTrue();
	}

	public function testIsChild()
	{
		$template = new atoum\template();
		$data = new atoum\template\data();

		$this->assert->boolean($template->isChild($data))->isFalse();

		$template->addChild($data);
		$this->assert->boolean($template->isChild($data))->isTrue();

		$template->deleteChild($template->getChild(0));
		$this->assert->boolean($template->isChild($data))->isFalse();
	}

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
