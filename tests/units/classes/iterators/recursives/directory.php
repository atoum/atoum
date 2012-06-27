<?php

namespace mageekguy\atoum\tests\units\iterators\recursives;

require_once __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\iterators\filters,
	mageekguy\atoum\iterators\recursives
;

class directory extends atoum\test
{
	public function beforeTestMethod($method)
	{
		$this->mockGenerator->shunt('__construct')->generate('recursiveDirectoryIterator');
	}

	public function test_class()
	{
		$this->testedClass->hasInterface('iteratorAggregate');
	}

	public function test__construct()
	{
		$this
			->if($iterator = new recursives\directory())
				->variable($iterator->getPath())->isNull()
				->boolean($iterator->dotsAreAccepted())->isFalse()
				->array($iterator->getAcceptedExtensions())->isEqualTo(array('php'))
				->object($iterator->getFactory())->isEqualTo(new atoum\factory())
			->if($iterator = new recursives\directory($path = uniqid(), $factory = new atoum\factory()))
			->then
				->string($iterator->getPath())->isEqualTo($path)
				->boolean($iterator->dotsAreAccepted())->isFalse()
				->array($iterator->getAcceptedExtensions())->isEqualTo(array('php'))
				->object($iterator->getFactory())->isIdenticalTo($factory)
		;
	}

	public function testSetPath()
	{
		$this
			->if($iterator = new recursives\directory(uniqid()))
			->then
				->object($iterator->setPath($path = uniqid()))->isIdenticalTo($iterator)
				->string($iterator->getPath())->isEqualTo($path)
		;
	}

	public function testSetFactory()
	{
		$this
			->if($iterator = new recursives\directory())
			->then
				->object($iterator->setFactory($factory = new atoum\factory()))->isIdenticalTo($iterator)
				->object($iterator->getFactory())->isIdenticalTo($factory)
		;
	}

	public function testAcceptExtensions()
	{
		$this
			->if($iterator = new recursives\directory(uniqid()))
			->then
				->object($iterator->acceptExtensions($extensions = array(uniqid())))->isIdenticalTo($iterator)
				->array($iterator->getAcceptedExtensions())->isEqualTo($extensions)
				->object($iterator->acceptExtensions($extensions = array('.' . ($extension = uniqid()))))->isIdenticalTo($iterator)
				->array($iterator->getAcceptedExtensions())->isEqualTo(array($extension))
		;
	}

	public function testAcceptAllExtensions()
	{
		$this
			->if($iterator = new recursives\directory(uniqid()))
			->then
				->object($iterator->acceptAllExtensions())->isIdenticalTo($iterator)
				->array($iterator->getAcceptedExtensions())->isEmpty()
		;
	}

	public function testRefuseExtension()
	{
		$this
			->if($iterator = new recursives\directory(uniqid()))
			->then
				->object($iterator->refuseExtension('php'))->isIdenticalTo($iterator)
				->array($iterator->getAcceptedExtensions())->isEmpty()
			->if($iterator->acceptExtensions(array('php', 'txt', 'xml')))
			->then
				->object($iterator->refuseExtension('txt'))->isIdenticalTo($iterator)
				->array($iterator->getAcceptedExtensions())->isEqualTo(array('php', 'xml'))
		;
	}

	public function testAcceptDots()
	{
		$this
			->if($iterator = new recursives\directory(uniqid()))
			->then
				->object($iterator->acceptDots())->isIdenticalTo($iterator)
				->boolean($iterator->dotsAreAccepted())->isTrue()
		;
	}

	public function testRefuseDots()
	{
		$this
			->if($iterator = new recursives\directory(uniqid()))
			->then
				->object($iterator->refuseDots())->isIdenticalTo($iterator)
				->boolean($iterator->dotsAreAccepted())->isFalse()
		;
	}

	public function testGetIterator()
	{
		$this
			->if($iterator = new \mock\mageekguy\atoum\iterators\recursives\directory())
			->then
				->exception(function() use ($iterator) {
						$iterator->getIterator();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Path is undefined')
			->if($factory = new \mock\mageekguy\atoum\factory())
			->and($factory->setBuilder('recursiveDirectoryIterator', function($path) use (& $recursiveDirectoryIterator) { return ($recursiveDirectoryIterator = new \mock\recursiveDirectoryIterator($path)); }))
			->and($iterator = new recursives\directory($path = uniqid(), $factory))
			->then
				->object($filterIterator = $iterator->getIterator())->isInstanceOf('mageekguy\atoum\iterators\filters\recursives\extension')
				->object($filterIterator->getInnerIterator())->isInstanceOf('mageekguy\atoum\iterators\filters\recursives\dot')
				->object($filterIterator->getInnerIterator()->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
				->mock($factory)->call('build')
					->withArguments('recursiveDirectoryIterator', array($path))->once()
					->withArguments('mageekguy\atoum\iterators\filters\recursives\dot', array($recursiveDirectoryIterator))->once()
					->withArguments('mageekguy\atoum\iterators\filters\recursives\extension', array(new atoum\iterators\filters\recursives\dot($recursiveDirectoryIterator), array('php')))->once()
			->if($iterator->acceptDots())
			->then
				->object($filterIterator = $iterator->getIterator())->isInstanceOf('mageekguy\atoum\iterators\filters\recursives\extension')
				->object($filterIterator->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
				->mock($factory)->call('build')
					->withArguments('recursiveDirectoryIterator', array($path))->exactly(2)
					->withArguments('mageekguy\atoum\iterators\filters\recursives\extension', array($recursiveDirectoryIterator, array('php')))->once()
			->if($iterator->refuseDots())
			->and($iterator->acceptExtensions(array()))
			->then
				->object($filterIterator = $iterator->getIterator())->isInstanceOf('mageekguy\atoum\iterators\filters\recursives\dot')
				->object($filterIterator->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
				->mock($factory)->call('build')
					->withArguments('recursiveDirectoryIterator', array($path))->exactly(3)
			->if($iterator->acceptDots())
			->and($iterator->acceptExtensions(array()))
			->then
				->object($iterator->getIterator())->isIdenticalTo($recursiveDirectoryIterator)
				->mock($factory)->call('build')
					->withArguments('recursiveDirectoryIterator', array($path))->exactly(4)
			->if($iterator = new recursives\directory(null, $factory))
			->then
				->object($filterIterator = $iterator->getIterator($path = uniqid()))->isInstanceOf('mageekguy\atoum\iterators\filters\recursives\extension')
				->object($filterIterator->getInnerIterator())->isInstanceOf('mageekguy\atoum\iterators\filters\recursives\dot')
				->object($filterIterator->getInnerIterator()->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
				->mock($factory)->call('build')
					->withArguments('recursiveDirectoryIterator', array($path))->once()
					->withArguments('mageekguy\atoum\iterators\filters\recursives\dot', array($recursiveDirectoryIterator))->once()
					->withArguments('mageekguy\atoum\iterators\filters\recursives\extension', array(new atoum\iterators\filters\recursives\dot($recursiveDirectoryIterator), array('php')))->exactly(2)
			->if($iterator->acceptDots())
			->then
				->object($filterIterator = $iterator->getIterator($path = uniqid()))->isInstanceOf('mageekguy\atoum\iterators\filters\recursives\extension')
				->object($filterIterator->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
				->mock($factory)->call('build')
					->withArguments('recursiveDirectoryIterator', array($path))->once()
					->withArguments('mageekguy\atoum\iterators\filters\recursives\extension', array($recursiveDirectoryIterator, array('php')))->once()
			->if($iterator->refuseDots())
			->and($iterator->acceptExtensions(array()))
			->then
				->object($filterIterator = $iterator->getIterator($path = uniqid()))->isInstanceOf('mageekguy\atoum\iterators\filters\recursives\dot')
				->object($filterIterator->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
				->mock($factory)->call('build')
					->withArguments('recursiveDirectoryIterator', array($path))->once()
			->if($iterator->acceptDots())
			->and($iterator->acceptExtensions(array()))
			->then
				->object($iterator->getIterator($path = uniqid()))->isIdenticalTo($recursiveDirectoryIterator)
				->mock($factory)->call('build')
					->withArguments('recursiveDirectoryIterator', array($path))->once()
		;
	}
}
