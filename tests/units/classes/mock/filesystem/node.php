<?php
namespace mageekguy\atoum\tests\units\mock\filesystem;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\filesystem\node as testedClass
;

require_once __DIR__ . '/../../runner.php';

use mageekguy\atoum\mock\stream;

class node extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($object = new \mock\mageekguy\atoum\mock\filesystem\node())
			->then
				->string($object->getName())->isNotEmpty()
				->variable($object->getParent())->isNull()
				->object($object->getStream())->isInstanceOf('\\mageekguy\\atoum\\mock\\stream\\controller')
			->if($object = new \mock\mageekguy\atoum\mock\filesystem\node($name = uniqid()))
			->then
				->string($object->getName())->isEqualTo($name)
				->variable($object->getParent())->isNull()
				->object($object->getStream())->isInstanceOf('\\mageekguy\\atoum\\mock\\stream\\controller')
			->if($this->mockGenerator->shunt('__construct'))
			->and($node = new \mock\mageekguy\atoum\mock\filesystem\node())
			->and($node->getMockController()->getStream = stream::get())
			->and($object = new \mock\mageekguy\atoum\mock\filesystem\node($name, $node))
			->then
				->string($object->getName())->isIdenticalTo($name)
				->object($object->getParent())->isIdenticalTo($node)
				->object($object->getStream())->isInstanceOf('\\mageekguy\\atoum\\mock\\stream\\controller')
			->if($object = new \mock\mageekguy\atoum\mock\filesystem\node($name, $node))
			->then
				->string($object->getName())->isIdenticalTo($name)
				->object($object->getParent())->isIdenticalTo($node)
		;
	}

	public function testGetName()
	{
		$this
			->if($object = new \mock\mageekguy\atoum\mock\filesystem\node($name = uniqid()))
			->then
				->string($object->getName())->isEqualTo($name)
		;
	}

	public function testGetStream()
	{
		$this
			->if($object = new \mock\mageekguy\atoum\mock\filesystem\node())
			->then
				->object($object->getStream())->isInstanceOf('\\mageekguy\\atoum\\mock\\stream\\controller')
		;
	}

	public function testGetParent()
	{
		$this
			->if($object = new \mock\mageekguy\atoum\mock\filesystem\node())
			->then
				->variable($object->getParent())->isNull()
			->if($this->mockGenerator->shunt('__construct'))
			->and($node = new \mock\mageekguy\atoum\mock\filesystem\node())
			->and($node->getMockController()->getStream = stream::get())
			->and($object = new \mock\mageekguy\atoum\mock\filesystem\node(uniqid(), $node))
			->then
				->object($object->getParent())->isIdenticalTo($node)
		;
	}

	public function testCreate()
	{
		$this
			->if($object = new \mock\mageekguy\atoum\mock\filesystem\node())
			->and($reference = null)
			->then
				->variable($object->create($reference))->isNull()
				->object($reference)->isIdenticalTo($object)
			->if($object = new \mock\mageekguy\atoum\mock\filesystem\node(uniqid(), $parent = new \mock\mageekguy\atoum\mock\filesystem\node()))
			->then
				->object($object->create($reference))->isIdenticalTo($parent)
		;
	}

	public function test__call()
	{
		$this
			->if($object = new \mock\mageekguy\atoum\mock\filesystem\node($name = uniqid()))
			->and($object->getMockController()->getStream = $stream = new \mock\mageekguy\atoum\mock\stream\controller($name))
			->and($stream->getMockController()->invoke = function() {})
			->then
				->variable($object->foo())
				->mock($stream)
					->call('invoke')->withArguments('foo', array())->once()
				->variable($object->bar($firstArg = uniqid(), $secondArg = uniqid()))
				->mock($stream)
					->call('invoke')->withArguments('bar', array($firstArg, $secondArg))->once()
		;
	}

	public function test__get()
	{
		$this
			->if($object = new \mock\mageekguy\atoum\mock\filesystem\node($name = uniqid()))
			->and($object->getMockController()->getStream = $stream = new \mock\mageekguy\atoum\mock\stream\controller($name))
			->and($stream->getMockController()->__get = function() {})
			->then
				->variable($object->foo)
				->mock($stream)
					->call('__get')->withArguments('foo')->once()
		;
	}

	public function test__set()
	{
		$this
			->if($object = new \mock\mageekguy\atoum\mock\filesystem\node($name = uniqid()))
			->and($object->getMockController()->getStream = $stream = new \mock\mageekguy\atoum\mock\stream\controller($name))
			->and($stream->getMockController()->__set = function() {})
			->then
				->variable($object->foo = $arg = uniqid())
				->mock($stream)
					->call('__set')->withArguments('foo', $arg)->once()
		;
	}

	public function test__isset()
	{
		$this
			->if($object = new \mock\mageekguy\atoum\mock\filesystem\node($name = uniqid()))
			->and($object->getMockController()->getStream = $stream = new \mock\mageekguy\atoum\mock\stream\controller($name))
			->and($stream->getMockController()->__isset = function() {})
			->then
				->boolean(isset($object->foo))->isFalse()
				->mock($stream)
					->call('__isset')->withArguments('foo')->once()
		;
	}

	public function test__toString()
	{
		$this
			->if($object = new \mock\mageekguy\atoum\mock\filesystem\node($name = uniqid()))
			->then
				->castToString($object)->isEqualTo('atoum://' . $name)
		;
	}
}
