<?php

namespace mageekguy\atoum\tests\units\template;

use
	mageekguy\atoum,
	mageekguy\atoum\template
;

require_once __DIR__ . '/../../runner.php';

class iterator extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->hasInterface('Countable')
				->hasInterface('Iterator')
		;
	}

	public function test__get()
	{
		$this
			->if($iterator = new template\iterator())
			->then
				->object($innerIterator = $iterator->{uniqid()})->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($innerIterator)->isZero()
			->if($template = new atoum\template())
			->and($template->addChild($tag = new template\tag(uniqid())))
			->and($iterator->addTag($tag->getTag(), $template))
			->and($tag->addChild($childTag = new template\tag($tag->getTag())))
			->then
				->object($innerIterator = $iterator->{uniqid()})->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($innerIterator)->isZero()
				->object($innerIterator = $iterator->{$childTag->getTag()})->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($innerIterator)->isEqualTo(1)
			->if($childTag->addChild($littleChildTag = new template\tag(uniqid())))
			->then
				->object($innerIterator = $iterator->{uniqid()})->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($innerIterator)->isZero()
				->object($innerIterator = $iterator->{$childTag->getTag()})->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($innerIterator)->isEqualTo(1)
				->object($innerIterator = $iterator->{$childTag->getTag()}->{$littleChildTag->getTag()})->isInstanceOf('mageekguy\atoum\template\iterator')
				->sizeOf($innerIterator)->isEqualTo(1)
		;
	}

	public function test__set()
	{
		$this
			->if($iterator = new template\iterator())
			->and($template = new atoum\template())
			->and($template->addChild($tag = new template\tag(uniqid())))
			->and($tag->addChild($childTag = new template\tag(uniqid())))
			->and($iterator->addTag($tag->getTag(), $template))
			->and($iterator->{$childTag->getTag()} = $data = uniqid())
			->then
				->string($childTag->getData())->isEqualTo($data)
				->object($iterator->__set($childTag->getTag(), $data = uniqid()))->isIdenticalTo($iterator)
				->string($childTag->getData())->isEqualTo($data)
		;
	}

	public function test__unset()
	{
		$this
			->if($iterator = new template\iterator())
			->and($template = new atoum\template())
			->and($template->addChild($tag = new template\tag(uniqid())))
			->and($tag->addChild($childTag = new template\tag(uniqid())))
			->and($iterator->addTag($tag->getTag(), $template))
			->and($iterator->{$childTag->getTag()} = $data = uniqid())
			->then
				->string($childTag->getData())->isNotEmpty()
		;

		unset($iterator->{$childTag->getTag()});

		$this
			->string($childTag->getData())->isEmpty()
			->if($iterator->{$childTag->getTag()} = uniqid())
			->then
				->string($childTag->getData())->isNotEmpty()
				->object($iterator->__unset($childTag->getTag()))->isIdenticalTo($iterator)
				->string($childTag->getData())->isEmpty()
		;
	}

	public function test__call()
	{
		$this
			->if($iterator = new template\iterator())
			->and($template = new atoum\template())
			->and($template->addChild($tag = new \mock\mageekguy\atoum\template\tag(uniqid())))
			->and($tag->getMockController()->build = function() {})
			->and($iterator->addTag($tag->getTag(), $template))
			->then
				->object($iterator->build())->isIdenticalTo($iterator)
				->mock($tag)->call('build')->withArguments(array())->once()
				->object($iterator->build($tags = array(uniqid() => uniqid())))->isIdenticalTo($iterator)
				->mock($tag)->call('build')->withArguments($tags)->once()
		;
	}

	public function testAddTemplate()
	{
		$this
			->if($iterator = new template\iterator())
			->and($template = new atoum\template())
			->then
				->object($iterator->addTag(uniqid(), $template))->isIdenticalTo($iterator)
				->sizeOf($iterator)->isZero()
			->if($template->addChild($tag = new template\tag(uniqid())))
			->then
				->object($iterator->addTag(uniqid(), $template))->isIdenticalTo($iterator)
				->sizeOf($iterator)->isZero()
				->object($iterator->addTag($tag->getTag(), $template))->isIdenticalTo($iterator)
				->sizeOf($iterator)->isEqualTo(1)
			->if($tag->addChild($childTag = new template\tag($tag->getTag())))
			->and($iterator = new template\iterator())
			->then
				->object($iterator->addTag(uniqid(), $template))->isIdenticalTo($iterator)
				->sizeOf($iterator)->isZero()
				->object($iterator->addTag($tag->getTag(), $template))->isIdenticalTo($iterator)
				->sizeOf($iterator)->isEqualTo(2)
		;
	}

	public function testValid()
	{
		$this
			->if($iterator = new template\iterator())
			->then
				->boolean($iterator->valid())->isFalse()
			->if($template = new atoum\template())
			->and($template->addChild($tag = new template\tag(uniqid())))
			->and($iterator->addTag($tag->getTag(), $template))
			->then
				->boolean($iterator->valid())->isTrue()
			->if($iterator->next())
			->then
				->boolean($iterator->valid())->isFalse()
		;
	}

	public function testRewind()
	{
		$this
			->if($iterator = new template\iterator())
			->then
				->object($iterator->rewind())->isIdenticalTo($iterator)
			->if($template = new atoum\template())
			->and($template->addChild($tag = new template\tag(uniqid())))
			->and($template->addChild(new template\tag($tag->getTag())))
			->and($iterator->addTag($tag->getTag(), $template))
			->and($iterator->next())
			->then
				->boolean($iterator->valid())->isTrue()
				->integer($iterator->key())->isEqualTo(1)
				->object($iterator->rewind())->isIdenticalTo($iterator)
				->boolean($iterator->valid())->isTrue()
				->integer($iterator->key())->isZero()
			->if($iterator->next()->next())
			->then
				->boolean($iterator->valid())->isFalse()
				->variable($iterator->key())->isNull()
				->object($iterator->rewind())->isIdenticalTo($iterator)
				->boolean($iterator->valid())->isTrue()
				->integer($iterator->key())->isZero()
		;
	}

	public function testKey()
	{
		$this
			->if($iterator = new template\iterator())
			->then
				->boolean($iterator->valid())->isFalse()
				->variable($iterator->key())->isNull()
			->if($template = new atoum\template())
			->and($template->addChild($tag = new template\tag(uniqid())))
			->and($iterator->addTag($tag->getTag(), $template))
			->then
				->boolean($iterator->valid())->isTrue()
				->integer($iterator->key())->isZero()
			->if($iterator->next())
			->then
				->boolean($iterator->valid())->isFalse()
				->variable($iterator->key())->isNull()
		;
	}

	public function testCurrent()
	{
		$this
			->if($iterator = new template\iterator())
			->then
				->boolean($iterator->valid())->isFalse()
				->variable($iterator->current())->isNull()
			->if($template = new atoum\template())
			->and($template->addChild($tag = new template\tag(uniqid())))
			->and($iterator->addTag($tag->getTag(), $template))
			->then
				->boolean($iterator->valid())->isTrue()
				->object($iterator->current())->isIdenticalTo($tag)
			->if($iterator->next())
			->then
				->boolean($iterator->valid())->isFalse()
				->variable($iterator->current())->isNull()
		;
	}
}
