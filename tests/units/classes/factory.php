<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require __DIR__ . '/../runner.php';

class factory extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->hasInterface('arrayAccess')
				->hasInterface('serializable')
		;
	}

	public function test__construct()
	{
		$this
			->if($factory = new atoum\factory())
			->then
				->array($factory->getBuilders())->isEmpty()
				->array($factory->getImportations())->isEmpty()
		;
	}

	public function testSerialize()
	{
		$this
			->if($factory = new atoum\factory())
			->then
				->string($factory->serialize())->isEqualTo(serialize($factory->getImportations()))
			->if($factory->import(uniqid(), uniqid()))
			->then
				->string($factory->serialize())->isEqualTo(serialize($factory->getImportations()))
		;
	}

	public function testUnserialize()
	{
		$this
			->if($factory = new atoum\factory())
			->then
				->object(unserialize(serialize($factory)))->isEqualTo($factory)
			->if($factory->import($namespace = uniqid(), $alias = uniqid()))
			->then
				->object(unserialize(serialize($factory)))->isEqualTo($factory)
			->if($factoryWithBuilder = new atoum\Factory())
			->and($factoryWithBuilder[uniqid()] = function() {})
			->and($factoryWithBuilder->import($namespace, $alias))
			->then
				->object(unserialize(serialize($factoryWithBuilder)))->isEqualTo($factory)
		;
	}

	public function testBuild()
	{
		$this
			->if($factory = new atoum\factory())
			->then
				->object($arrayIterator = $factory->build('arrayIterator'))->isEqualTo(new \arrayIterator())
				->object($arrayIterator = $factory->build('arrayIterator', array()))->isEqualTo(new \arrayIterator())
				->object($arrayIterator = $factory->build('arrayIterator', array(array())))->isEqualTo(new \arrayIterator())
				->object($arrayIterator = $factory->build('arrayIterator', array($data = array(uniqid(), uniqid(), uniqid()))))->isEqualTo(new \arrayIterator($data))
				->exception(function() use ($factory, & $class) { $factory->build($class = uniqid()); })
					->isInstanceOf('mageekguy\atoum\factory\exception')
					->hasMessage('Class \'' . $class . '\' does not exist')
			->if($factory->setBuilder('arrayIterator', function() use (& $return) { return ($return = new \arrayIterator); }))
				->object($arrayIterator = $factory->build('arrayIterator'))->isIdenticalTo($return)
				->object($arrayIterator = $factory->build('arrayIterator', array()))->isIdenticalTo($return)
				->object($arrayIterator = $factory->build('arrayIterator', array(array())))->isIdenticalTo($return)
				->object($arrayIterator = $factory->build('arrayIterator', array($data = array(uniqid(), uniqid(), uniqid()))))->isIdenticalTo($return)
			->if($factory->setBuilder('arrayIterator', function() use (& $return) { return ($return = uniqid()); }))
			->then
				->string($factory->build('arrayIterator'))->isEqualTo($return)
			->if($factory->import('mageekguy\atoum'))
			->then
				->object($factory->build('atoum\adapter'))->isEqualTo(new atoum\adapter())
			->if($factory->import('mageekguy\atoum', 'foo'))
			->then
				->object($factory->build('foo\adapter'))->isEqualTo(new atoum\adapter())
			->if($factory->import('mageekguy\atoum\adapter'))
			->then
				->object($factory->build('adapter'))->isEqualTo(new atoum\adapter())
			->if($factory->import('mageekguy\atoum\adapter', 'bar'))
			->then
				->object($factory->build('bar'))->isEqualTo(new atoum\adapter())
		;
	}

	public function testSetBuilder()
	{
		$this
			->if($factory = new atoum\factory())
			->and($arrayIterator = new \arrayIterator())
			->then
				->object($factory->setBuilder('arrayIterator', function() use ($arrayIterator) { return $arrayIterator; }))->isIdenticalTo($factory)
				->boolean($factory->builderIsSet('arrayIterator'))->isTrue()
				->object($factory->build('arrayIterator'))->isIdenticalTo($arrayIterator)
		;
	}

	public function testOffsetSet()
	{
		$this
			->if($factory = new atoum\factory())
			->and($arrayIterator = new \arrayIterator())
			->and($factory['arrayIterator'] = function() use ($arrayIterator) { return $arrayIterator; })
			->then
				->boolean($factory->builderIsSet('arrayIterator'))->isTrue()
				->object($factory->build('arrayIterator'))->isIdenticalTo($arrayIterator)
			->if($factory['arrayIterator'] = $arrayIterator)
			->then
				->boolean($factory->builderIsSet('arrayIterator'))->isTrue()
				->object($factory->build('arrayIterator'))->isIdenticalTo($arrayIterator)
		;
	}

	public function testOffsetGet()
	{
		$this
			->if($factory = new atoum\factory())
			->then
				->object($factory['arrayIterator'])->isInstanceOf('closure')
			->if($factory->setBuilder('arrayIterator', $closure = function() {}))
			->then
				->object($factory['arrayIterator'])->isIdenticalTo($closure)
		;
	}

	public function testOffsetExists()
	{
		$this
			->if($factory = new atoum\factory())
			->then
				->boolean(isset($factory[uniqid()]))->isFalse()
			->if($factory->setBuilder('arrayIterator', function() { return new \arrayIterator(array()); }))
			->then
				->boolean(isset($factory['arrayIterator']))->isTrue()
				->boolean(isset($factory[uniqid()]))->isFalse()
		;
	}

	public function testOffsetUnset()
	{
		$this
			->if($factory = new atoum\factory())
			->then
				->object($factory->offsetUnset(uniqid()))->isIdenticalTo($factory)
			->if($factory->setBuilder('arrayIterator', function() { return new \arrayIterator(array()); }))
			->then
				->object($factory->offsetUnset('arrayIterator'))->isIdenticalTo($factory)
				->boolean($factory->builderIsSet('arrayIterator'))->isFalse()
		;
	}

	public function testBuilderIsSet()
	{
		$this
			->if($factory = new atoum\factory())
			->then
				->boolean($factory->builderIsSet(uniqid()))->isFalse()
			->if($factory->setBuilder('arrayIterator', function() { return new \arrayIterator(array()); }))
			->then
				->boolean($factory->builderIsSet('arrayIterator'))->isTrue()
				->boolean($factory->builderIsSet(uniqid()))->isFalse()
		;
	}

	public function testUnsetBuilder()
	{
		$this
			->if($factory = new atoum\factory())
			->then
				->object($factory->unsetBuilder(uniqid()))->isIdenticalTo($factory)
			->if($factory->setBuilder('arrayIterator', function() { return new \arrayIterator(array()); }))
			->then
				->object($factory->unsetBuilder('arrayIterator'))->isIdenticalTo($factory)
				->boolean($factory->builderIsSet('arrayIterator'))->isFalse()
		;
	}

	public function testReturnWhenBuild()
	{
		$this
			->if($factory = new atoum\factory())
			->and($arrayIterator = new \arrayIterator())
			->then
				->object($factory->returnWhenBuild('arrayIterator', $arrayIterator))->isIdenticalTo($factory)
				->boolean($factory->builderIsSet('arrayIterator'))->isTrue()
				->object($factory->build('arrayIterator'))->isIdenticalTo($arrayIterator)
		;
	}

	public function testGetBuilder()
	{
		$this
			->if($factory = new atoum\factory())
			->then
				->variable($factory->getBuilder('arrayIterator'))->isNull()
			->if($factory->setBuilder('arrayIterator', $closure = function() {}))
			->then
				->object($factory->getBuilder('arrayIterator'))->isIdenticalTo($closure)
		;
	}

	public function testImport()
	{
		$this
			->if($factory = new atoum\factory())
			->then
				->object($factory->import('foo'))->isIdenticalTo($factory)
				->array($factory->getImportations())->isEqualTo(array('foo' => 'foo'))
				->object($factory->import('\foo'))->isIdenticalTo($factory)
				->array($factory->getImportations())->isEqualTo(array('foo' => 'foo'))
				->object($factory->import('foo', 'bar'))->isIdenticalTo($factory)
				->array($factory->getImportations())->isEqualTo(array('foo' => 'foo', 'bar' => 'foo'))
				->object($factory->import('foo\bar', 'truc'))->isIdenticalTo($factory)
				->array($factory->getImportations())->isEqualTo(array('foo' => 'foo', 'bar' => 'foo', 'truc' => 'foo\bar'))
				->object($factory->import('foo\bar\toto', 'tutu'))->isIdenticalTo($factory)
				->array($factory->getImportations())->isEqualTo(array('foo' => 'foo', 'bar' => 'foo', 'truc' => 'foo\bar', 'tutu' => 'foo\bar\toto'))
				->exception(function() use ($factory) { $factory->import('foo\bar\tutu'); })
					->isInstanceOf('mageekguy\atoum\factory\exception')
					->hasMessage('Unable to use \'foo\bar\tutu\' as \'tutu\' because the name is already in use')
		;
	}

	public function testResetImportations()
	{
		$this
			->if($factory = new atoum\factory())
			->then
				->object($factory->resetImportations())->isIdenticalTo($factory)
				->array($factory->getImportations())->isEmpty()
			->if($factory->import($this, uniqid()))
			->then
				->object($factory->resetImportations())->isIdenticalTo($factory)
				->array($factory->getImportations())->isEmpty()
			->if($factory->import($this, uniqid(), uniqid()))
			->then
				->object($factory->resetImportations())->isIdenticalTo($factory)
				->array($factory->getImportations())->isEmpty()
		;
	}
}
