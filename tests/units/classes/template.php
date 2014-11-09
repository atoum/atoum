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
		$this
			->if($this->newTestedInstance)
			->then
				->string($this->testedInstance->getData())->isEmpty()
			->if($this->newTestedInstance($data = uniqid()))
			->then
				->string($this->testedInstance->getData())->isEqualTo($data)
		;
	}

	public function test__toString()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->string($this->testedInstance->getData())->isEmpty()
				->boolean($this->testedInstance->hasChildren())->isFalse()
				->variable($this->testedInstance->getId())->isNull()
				->variable($this->testedInstance->getTag())->isNull()
			->if($this->newTestedInstance($data = uniqid()))
			->then
				->string($this->testedInstance->getData())->isEqualTo($data)
				->boolean($this->testedInstance->hasChildren())->isFalse()
				->variable($this->testedInstance->getId())->isNull()
				->variable($this->testedInstance->getTag())->isNull()
		;
	}

	public function test__get()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($iterator = $this->testedInstance->{uniqid()})->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($iterator)->isZero()
			->if($this->testedInstance->addChild($childTag = new atoum\template\tag(uniqid())))
			->then
				->object($iterator = $this->testedInstance->{$childTag->getTag()})->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($iterator)->isEqualTo(1)
				->object($iterator->current())->isIdenticalTo($childTag)
			->if($this->testedInstance->addChild($otherChildTag = new atoum\template\tag($childTag->getTag())))
			->then
				->object($iterator = $this->testedInstance->{$childTag->getTag()})->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($iterator)->isEqualTo(2)
				->object($iterator->current())->isIdenticalTo($childTag)
				->object($iterator->next()->current())->isIdenticalTo($otherChildTag)
			->if($this->testedInstance->addChild($anotherChildTag = new atoum\template\tag(uniqid())))
			->then
				->object($iterator = $this->testedInstance->{$childTag->getTag()})->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($iterator)->isEqualTo(2)
				->object($iterator->current())->isIdenticalTo($childTag)
				->object($iterator->next()->current())->isIdenticalTo($otherChildTag)
				->object($iterator = $this->testedInstance->{$anotherChildTag->getTag()})->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($iterator)->isEqualTo(1)
				->object($iterator->current())->isIdenticalTo($anotherChildTag)
			->if($childTag->addChild($littleChildTag  = new atoum\template\tag($childTag->getTag())))
			->then
				->object($iterator = $this->testedInstance->{$childTag->getTag()})->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($iterator)->isEqualTo(3)
				->object($iterator->current())->isIdenticalTo($childTag)
				->object($iterator->next()->current())->isIdenticalTo($littleChildTag)
				->object($iterator->next()->current())->isIdenticalTo($otherChildTag)
				->object($iterator = $this->testedInstance->{$anotherChildTag->getTag()})->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($iterator)->isEqualTo(1)
				->object($iterator->current())->isIdenticalTo($anotherChildTag)
		;
	}

	public function test__set()
	{
		$this
			->if(
				$this->newTestedInstance,
				$this->testedInstance
					->addChild($tag  = new atoum\template\tag(uniqid()))
					->{$tag->getTag()} = $data = uniqid()
			)
			->then
				->string($tag->getData())->isEqualTo($data)
			->if(
				$tag->addChild($childTag  = new atoum\template\tag($tag->getTag())),
				$this->testedInstance->{$tag->getTag()} = $data
			)
			->then
				->string($tag->getData())->isEqualTo($data)
				->string($childTag->getData())->isEqualTo($data)
			->if(
				$tag->addChild($otherChildTag = new atoum\template\tag(uniqid())),
				$this->testedInstance->{$otherChildTag->getTag()} = $otherData = uniqid()
			)
			->then
				->string($tag->getData())->isEqualTo($data)
				->string($childTag->getData())->isEqualTo($data)
				->string($otherChildTag->getData())->isEqualTo($otherData)
		;
	}

	public function test__isset()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->boolean(isset($this->testedInstance->{uniqid()}))->isFalse()
			->if($this->testedInstance->addChild($childTag = new atoum\template\tag(uniqid())))
			->then
				->boolean(isset($this->testedInstance->{uniqid()}))->isFalse()
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
			->if($childTag->addChild($otherChildTag = new atoum\template\tag(uniqid())))
			->then
				->boolean(isset($this->testedInstance->{uniqid()}))->isFalse()
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
				->boolean(isset($this->testedInstance->{$otherChildTag->getTag()}))->isTrue()
			->if($childTag->addChild($littleChildTag = new atoum\template\tag(uniqid())))
			->then
				->boolean(isset($this->testedInstance->{uniqid()}))->isFalse()
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
				->boolean(isset($this->testedInstance->{$otherChildTag->getTag()}))->isTrue()
				->boolean(isset($this->testedInstance->{$littleChildTag->getTag()}))->isTrue()
		;
	}

	public function test__unset()
	{
		$this
			->if(
				$template = $this->newTestedInstance,
				$this->testedInstance->addChild($childTag = new atoum\template\tag(uniqid()))
			)
			->then
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
				->string($childTag->getData())->isEmpty()
			->when(function() use ($template, $childTag) {
					unset($template->{$childTag->getTag()});
				}
			)
			->then
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
				->string($childTag->getData())->isEmpty()
			->if($this->testedInstance->{$childTag->getTag()} = uniqid())
			->then
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
				->string($childTag->getData())->isNotEmpty()
			->when(function() use ($template, $childTag) {
					unset($template->{$childTag->getTag()});
				}
			)
			->then
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
				->string($childTag->getData())->isEmpty()
			->if($this->testedInstance->addChild($otherChildTag = new atoum\template\tag(uniqid())))
			->then
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
				->string($childTag->getData())->isEmpty()
				->boolean(isset($this->testedInstance->{$otherChildTag->getTag()}))->isTrue()
				->string($otherChildTag->getData())->isEmpty()
			->when(function() use ($template, $childTag, $otherChildTag) {
					unset($template->{$childTag->getTag()});
					unset($template->{$otherChildTag->getTag()});
				}
			)
			->then
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
				->string($childTag->getData())->isEmpty()
				->boolean(isset($this->testedInstance->{$otherChildTag->getTag()}))->isTrue()
				->string($otherChildTag->getData())->isEmpty()
			->if(
				$this->testedInstance->{$childTag->getTag()} = uniqid(),
				$this->testedInstance->{$otherChildTag->getTag()} = uniqid()
			)
			->then
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
				->string($childTag->getData())->isNotEmpty()
				->boolean(isset($this->testedInstance->{$otherChildTag->getTag()}))->isTrue()
				->string($otherChildTag->getData())->isNotEmpty()
			->when(function() use ($template, $childTag) {
					unset($template->{$childTag->getTag()});
				}
			)
			->then
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
				->string($childTag->getData())->isEmpty()
				->boolean(isset($this->testedInstance->{$otherChildTag->getTag()}))->isTrue()
				->string($otherChildTag->getData())->isNotEmpty()
			->when(function() use ($template, $otherChildTag) {
					unset($template->{$otherChildTag->getTag()});
				}
			)
			->then
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
				->string($childTag->getData())->isEmpty()
				->boolean(isset($this->testedInstance->{$otherChildTag->getTag()}))->isTrue()
				->string($otherChildTag->getData())->isEmpty()
			->if($childTag->addChild($littleChildTag = new atoum\template\tag(uniqid())))
			->then
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
				->string($childTag->getData())->isEmpty()
				->boolean(isset($this->testedInstance->{$otherChildTag->getTag()}))->isTrue()
				->string($otherChildTag->getData())->isEmpty()
				->boolean(isset($this->testedInstance->{$littleChildTag->getTag()}))->isTrue()
				->string($littleChildTag->getData())->isEmpty()
			->if(
				$this->testedInstance->{$childTag->getTag()} = uniqid(),
				$this->testedInstance->{$otherChildTag->getTag()} = uniqid(),
				$this->testedInstance->{$littleChildTag->getTag()} = uniqid()
			)
			->then
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
				->string($childTag->getData())->isNotEmpty()
				->boolean(isset($this->testedInstance->{$otherChildTag->getTag()}))->isTrue()
				->string($otherChildTag->getData())->isNotEmpty()
				->boolean(isset($this->testedInstance->{$littleChildTag->getTag()}))->isTrue()
				->string($littleChildTag->getData())->isNotEmpty()
			->when(function() use ($template, $childTag) {
					unset($template->{$childTag->getTag()});
				}
			)
			->then
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
				->string($childTag->getData())->isEmpty()
				->boolean(isset($this->testedInstance->{$otherChildTag->getTag()}))->isTrue()
				->string($otherChildTag->getData())->isNotEmpty()
				->boolean(isset($this->testedInstance->{$littleChildTag->getTag()}))->isTrue()
				->string($littleChildTag->getData())->isNotEmpty()
			->when(function() use ($template, $otherChildTag) {
					unset($template->{$otherChildTag->getTag()});
				}
			)
			->then
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
				->string($childTag->getData())->isEmpty()
				->boolean(isset($this->testedInstance->{$otherChildTag->getTag()}))->isTrue()
				->string($otherChildTag->getData())->isEmpty()
				->boolean(isset($this->testedInstance->{$littleChildTag->getTag()}))->isTrue()
				->string($littleChildTag->getData())->isNotEmpty()
			->when(function() use ($template, $littleChildTag) {
					unset($template->{$littleChildTag->getTag()});
				}
			)
			->then
				->boolean(isset($this->testedInstance->{$childTag->getTag()}))->isTrue()
				->string($childTag->getData())->isEmpty()
				->boolean(isset($this->testedInstance->{$otherChildTag->getTag()}))->isTrue()
				->string($otherChildTag->getData())->isEmpty()
				->boolean(isset($this->testedInstance->{$littleChildTag->getTag()}))->isTrue()
				->string($littleChildTag->getData())->isEmpty()
		;
	}

	public function testGetRoot()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->getRoot())->isTestedInstance
			->if($this->testedInstance->addChild($childTemplate = new atoum\template()))
			->then
				->object($this->testedInstance->getRoot())->isTestedInstance
				->object($childTemplate->getRoot())->isTestedInstance
			->if($childTemplate->addChild($littleChildTemplate = new atoum\template()))
			->then
				->object($this->testedInstance->getRoot())->isTestedInstance
				->object($childTemplate->getRoot())->isTestedInstance
				->object($littleChildTemplate->getRoot())->isTestedInstance
		;
	}

	public function testGetParent()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getParent())->isNull()
			->if($this->testedInstance->addChild($childTemplate = new atoum\template()))
			->then
				->variable($this->testedInstance->getParent())->isNull()
				->object($childTemplate->getParent())->isTestedInstance
			->if($childTemplate->addChild($littleChildTemplate = new atoum\template()))
			->then
				->variable($this->testedInstance->getParent())->isNull()
				->object($childTemplate->getParent())->isTestedInstance
				->object($littleChildTemplate->getParent())->isIdenticalTo($childTemplate)
		;
	}

	public function testParentIsSet()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->boolean($this->testedInstance->parentIsSet())->isFalse()
			->if($childTemplate = new atoum\template())
			->then
				->boolean($this->testedInstance->parentIsSet())->isFalse()
				->boolean($childTemplate->parentIsSet())->isFalse()
			->if($this->testedInstance->addChild($childTemplate))
			->then
				->boolean($this->testedInstance->parentIsSet())->isFalse()
				->boolean($childTemplate->parentIsSet())->isTrue()
			->if($littleChildTemplate = new atoum\template())
			->then
				->boolean($this->testedInstance->parentIsSet())->isFalse()
				->boolean($childTemplate->parentIsSet())->isTrue()
				->boolean($littleChildTemplate->parentIsSet())->isFalse()
			->if($childTemplate->addChild($littleChildTemplate))
			->then
				->boolean($this->testedInstance->parentIsSet())->isFalse()
				->boolean($childTemplate->parentIsSet())->isTrue()
				->boolean($littleChildTemplate->parentIsSet())->isTrue()
		;
	}

	public function testUnsetParent()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->boolean($this->testedInstance->parentIsSet())->isFalse()
				->object($this->testedInstance->unsetParent())->isTestedInstance
				->boolean($this->testedInstance->parentIsSet())->isFalse()
			->if($this->testedInstance->addChild($childTemplate = new atoum\template()))
			->then
				->boolean($this->testedInstance->parentIsSet())->isFalse()
				->boolean($childTemplate->parentIsSet())->isTrue()
				->object($this->testedInstance->unsetParent())->isTestedInstance
				->object($childTemplate->unsetParent())->isIdenticalTo($childTemplate)
				->boolean($this->testedInstance->parentIsSet())->isFalse()
				->boolean($childTemplate->parentIsSet())->isFalse()
		;
	}

	public function testSetParent()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->boolean($this->testedInstance->parentIsSet())->isFalse()
				->object($this->testedInstance->setParent(new atoum\template()))->isTestedInstance
				->boolean($this->testedInstance->parentIsSet())->isTrue()
		;
	}

	public function testGetData()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->string($this->testedInstance->getData())->isEmpty()
			->if($this->newTestedInstance($data = uniqid()))
			->then
				->string($this->testedInstance->getData())->isEqualTo($data)
			->if(
				$this->newTestedInstance,
				$this->testedInstance->setData($data = uniqid())
			)
			->then
				->string($this->testedInstance->getData())->isEqualTo($data)
			->if($this->testedInstance->addChild(new atoum\template($otherData = uniqid())))
			->then
				->string($this->testedInstance->getData())->isEqualTo($data)
			->if($this->testedInstance->build())
			->then
				->string($this->testedInstance->getData())->isEqualTo($data . $otherData)
		;
	}

	public function testSetData()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setData($data = uniqid()))->isTestedInstance
				->string($this->testedInstance->getData())->isEqualTo($data)
		;
	}

	public function testAddData()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->addData($data = uniqid()))->isTestedInstance
				->string($this->testedInstance->getData())->isEqualTo($data)
				->object($this->testedInstance->addData($data))->isTestedInstance
				->string($this->testedInstance->getData())->isEqualTo($data . $data)
				->object($this->testedInstance->addData($otherData = uniqid()))->isTestedInstance
				->string($this->testedInstance->getData())->isEqualTo($data . $data . $otherData)
		;
	}

	public function testResetData()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->string($this->testedInstance->getData())->isEmpty()
				->object($this->testedInstance->resetData())->isTestedInstance
				->string($this->testedInstance->getData())->isEmpty()
			->if($this->newTestedInstance($data = uniqid()))
			->then
				->string($this->testedInstance->getData())->isEqualTo($data)
				->object($this->testedInstance->resetData())->isTestedInstance
				->string($this->testedInstance->getData())->isEmpty()
		;
	}

	public function testGetId()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getId())->isNull()
		;
	}

	public function testIsRoot()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->boolean($this->testedInstance->isRoot())->isTrue()
		;
	}

	public function testIsChild()
	{
		$this
			->if(
				$this->newTestedInstance,
				$childTemplate = new atoum\template()
			)
			->then
				->boolean($this->testedInstance->isChild($childTemplate))->isFalse()
			->if($this->testedInstance->addChild($childTemplate))
			->then
				->boolean($this->testedInstance->isChild($childTemplate))->isTrue()
		;
	}

	public function testGetTag()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getTag())->isNull()
		;
	}

	public function testGetChildren()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->array($this->testedInstance->getChildren())->isEmpty()
			->if($this->testedInstance->addChild($childTemplate = new atoum\template()))
			->then
				->array($this->testedInstance->getChildren())->isIdenticalTo(array($childTemplate))
			->if($this->testedInstance->addChild($otherChildTemplate = new atoum\template()))
			->then
				->array($this->testedInstance->getChildren())->isIdenticalTo(array($childTemplate, $otherChildTemplate))
			->if($childTemplate->addChild($littleChildTemplate = new atoum\template()))
			->then
				->array($this->testedInstance->getChildren())->isIdenticalTo(array($childTemplate, $otherChildTemplate))
		;
	}

	public function testAddChild()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->boolean($this->testedInstance->hasChildren())->isFalse()
				->object($this->testedInstance->addChild($childTemplate = new atoum\template()))->isTestedInstance
				->object($childTemplate->getParent())->isTestedInstance
				->array($this->testedInstance->getChildren())->isIdenticalTo(array($childTemplate))
				->object($this->testedInstance->addChild($childTemplate))->isTestedInstance
				->object($childTemplate->getParent())->isTestedInstance
				->array($this->testedInstance->getChildren())->isIdenticalTo(array($childTemplate))
			->if(
				$otherTemplate = new atoum\template(),
				$otherTemplate->addChild($otherChildTemplate = new atoum\template())
			)
			->then
				->array($otherTemplate->getChildren())->isIdenticalTo(array($otherChildTemplate))
				->object($otherChildTemplate->getParent())->isIdenticalTo($otherTemplate)
				->object($this->testedInstance->addChild($otherChildTemplate))->isTestedInstance
				->array($otherTemplate->getChildren())->isEmpty()
				->object($otherChildTemplate->getParent())->isTestedInstance
				->array($this->testedInstance->getChildren())->isIdenticalTo(array($childTemplate, $otherChildTemplate))
			->if(
				$this->newTestedInstance,
				$templateWithId = new atoum\template\tag(uniqid()),
				$templateWithId->setId($id = uniqid()),
				$templateWithSameId = clone $templateWithId
			)
			->then
				->boolean($this->testedInstance->hasChildren())->isFalse()
				->object($this->testedInstance->addChild($templateWithId))->isTestedInstance
				->array($this->testedInstance->getChildren())->isIdenticalTo(array($templateWithId))
				->object($this->testedInstance->addChild($templateWithId))->isTestedInstance
				->array($this->testedInstance->getChildren())->isIdenticalTo(array($templateWithId))
				->exception(function($test) use ($templateWithSameId) {
							$test->testedInstance->addChild($templateWithSameId);
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Id \'' . $id . '\' is already defined')
			;
	}

	public function testDeleteChild()
	{
		$this
			->if(
				$this->newTestedInstance,
				$this->testedInstance->addChild($childTemplate = new atoum\template())
			)
			->then
				->object($childTemplate->getParent())->isTestedInstance
				->array($this->testedInstance->getChildren())->isIdenticalTo(array($childTemplate))
				->object($this->testedInstance->deleteChild($childTemplate))->isTestedInstance
				->variable($childTemplate->getParent())->isNull()
				->array($this->testedInstance->getChildren())->isEmpty()
		;
	}

	public function testHasChildren()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->boolean($this->testedInstance->hasChildren())->isFalse()
			->if($this->testedInstance->addChild($childTemplate = new atoum\template()))
			->then
				->boolean($this->testedInstance->hasChildren())->isTrue()
			->if($this->testedInstance->deleteChild($childTemplate))
			->then
				->boolean($this->testedInstance->hasChildren())->isFalse()
		;
	}

	public function testGetByTag()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($iterator = $this->testedInstance->getByTag(uniqid()))->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($iterator)->isZero()
			->if($this->testedInstance->addChild($tag = new atoum\template\tag(uniqid())))
			->then
				->object($iterator = $this->testedInstance->getByTag(uniqid()))->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($iterator)->isZero()
				->object($iterator = $this->testedInstance->getByTag($tag->getTag()))->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($iterator)->isEqualTo(1)
				->object($iterator->current())->isIdenticalTo($tag)
			->if($this->testedInstance->addChild($otherTag = new atoum\template\tag($tag->getTag())))
			->then
				->object($iterator = $this->testedInstance->getByTag(uniqid()))->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($iterator)->isZero()
				->object($iterator = $this->testedInstance->getByTag($tag->getTag()))->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($iterator)->isEqualTo(2)
				->object($iterator->current())->isIdenticalTo($tag)
				->object($iterator->next()->current())->isIdenticalTo($otherTag)
			->if($tag->addChild($childTag = new atoum\template\tag($tag->getTag())))
			->then
				->object($iterator = $this->testedInstance->getByTag(uniqid()))->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($iterator)->isZero()
				->object($iterator = $this->testedInstance->getByTag($tag->getTag()))->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($iterator)->isEqualTo(3)
				->object($iterator->current())->isIdenticalTo($tag)
				->object($iterator->next()->current())->isIdenticalTo($childTag)
				->object($iterator->next()->current())->isIdenticalTo($otherTag)
		;
	}

	public function testGetById()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getById(uniqid()))->isNull()
			->if(
				$tag = new atoum\template\tag(uniqid()),
				$this->testedInstance->addChild($tag->setId($id = uniqid()))
			)
			->then
				->variable($this->testedInstance->getById(uniqid()))->isNull()
				->object($this->testedInstance->getById($id))->isIdenticalTo($tag)
			->if(
				$childTag = new atoum\template\tag(uniqid()),
				$tag->addChild($childTag->setId($childId = uniqid()))
			)
			->then
				->variable($this->testedInstance->getById(uniqid()))->isNull()
				->object($this->testedInstance->getById($id))->isIdenticalTo($tag)
				->object($tag->getById($id))->isIdenticalTo($tag)
				->object($this->testedInstance->getById($childId))->isIdenticalTo($childTag)
				->object($tag->getById($childId))->isIdenticalTo($childTag)
				->object($childTag->getById($childId))->isIdenticalTo($childTag)
				->variable($childTag->getById($id, false))->isNull()
		;
	}

	public function testBuild()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->string($this->testedInstance->getData())->isEmpty()
				->boolean($this->testedInstance->hasChildren())->isFalse()
				->object($this->testedInstance->build())->isTestedInstance
				->string($this->testedInstance->getData())->isEmpty()
			->if($this->newTestedInstance($data = uniqid()))
			->then
				->string($this->testedInstance->getData())->isEqualTo($data)
				->boolean($this->testedInstance->hasChildren())->isFalse()
				->object($this->testedInstance->build())->isTestedInstance
				->string($this->testedInstance->getData())->isEqualTo($data)
				->object($this->testedInstance->build())->isTestedInstance
				->string($this->testedInstance->getData())->isEqualTo($data)
			->if($this->testedInstance->addChild($childTemplate = new atoum\template($childData = uniqid())))
			->then
				->string($this->testedInstance->getData())->isEqualTo($data)
				->string($childTemplate->getData())->isEqualTo($childData)
				->array($this->testedInstance->getChildren())->isIdenticalTo(array($childTemplate))
				->object($this->testedInstance->build())->isTestedInstance
				->string($this->testedInstance->getData())->isEqualTo($data . $childData)
				->string($childTemplate->getData())->isEqualTo($childData)
				->object($this->testedInstance->build())->isTestedInstance
				->string($this->testedInstance->getData())->isEqualTo($data . $childData . $childData)
		;
	}

	public function testGetChild()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getChild(0))->isNull()
				->variable($this->testedInstance->getChild(rand(1, PHP_INT_MAX)))->isNull()
				->variable($this->testedInstance->getChild(- rand(1, PHP_INT_MAX)))->isNull()
			->if($this->testedInstance->addChild($childTemplate = new atoum\template()))
			->then
				->variable($this->testedInstance->getChild(0))->isIdenticalTo($childTemplate)
				->variable($this->testedInstance->getChild(rand(1, PHP_INT_MAX)))->isNull()
				->variable($this->testedInstance->getChild(- rand(1, PHP_INT_MAX)))->isNull()
		;
	}
}
