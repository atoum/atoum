<?php

namespace mageekguy\atoum\tests\units\iterators\recursives\directory;

require_once __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\iterators\filters,
	mageekguy\atoum\iterators\recursives\directory\factory as testedClass
;

class factory extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->implements('iteratorAggregate');
	}

	public function test__construct()
	{
		$this
			->if($iterator = new testedClass())
				->boolean($iterator->dotsAreAccepted())->isFalse()
				->array($iterator->getAcceptedExtensions())->isEqualTo(array('php'))
				->object($iteratorFactory = $iterator->getIteratorFactory())->isInstanceOf('closure')
				->object($defaultIterator = $iteratorFactory(__DIR__))->isEqualTo(new \recursiveDirectoryIterator(__DIR__))
				->object($dotFilterFactory = $iterator->getDotFilterFactory())->isInstanceOf('closure')
				->object($dotFilterFactory($defaultIterator))->isEqualTo(new filters\recursives\dot($defaultIterator))
				->object($extensionFilterIterator = $iterator->getExtensionFilterFactory())->isInstanceOf('closure')
				->object($extensionFilterIterator($defaultIterator, $extensions = array('foo')))->isEqualTo(new filters\recursives\extension($defaultIterator, $extensions))

			->if($iterator = new testedClass($iteratorFactory = function() {}, $dotFilterFactory = function() {}, $extensionFilterFactory = function() {}))
			->then
				->boolean($iterator->dotsAreAccepted())->isFalse()
				->array($iterator->getAcceptedExtensions())->isEqualTo(array('php'))
				->object($iterator->getIteratorFactory())->isIdenticalTo($iteratorFactory)
				->object($iterator->getDotFilterFactory())->isIdenticalTo($dotFilterFactory)
				->object($iterator->getExtensionFilterFactory())->isIdenticalTo($extensionFilterFactory)
		;
	}

	public function testSetIteratorFactory()
	{
		$this
			->if($iterator = new testedClass())
			->then
				->object($iterator->setIteratorFactory($factory = function() {}))->isIdenticalTo($iterator)
				->object($iterator->getIteratorFactory())->isIdenticalTo($factory)
				->object($iterator->setIteratorFactory())->isIdenticalTo($iterator)
				->object($defaultFactory = $iterator->getIteratorFactory())
					->isInstanceOf('closure')
					->isNotIdenticalTo($factory)
				->object($defaultFactory(__DIR__))->isEqualTo(new \recursiveDirectoryIterator(__DIR__))
		;
	}

	public function testSetDotFilterFactory()
	{
		$this
			->if($iterator = new testedClass())
			->then
				->object($iterator->setDotFilterFactory($factory = function() {}))->isIdenticalTo($iterator)
				->object($iterator->getDotFilterFactory())->isIdenticalTo($factory)
				->object($iterator->setDotFilterFactory())->isIdenticalTo($iterator)
				->object($defaultFactory = $iterator->getDotFilterFactory())
					->isInstanceOf('closure')
					->isNotIdenticalTo($factory)
				->object($defaultFactory($iterator = new \recursiveDirectoryIterator(__DIR__)))->isEqualTo(new filters\recursives\dot($iterator))
		;
	}

	public function testSetExtensionFilterFactory()
	{
		$this
			->if($iterator = new testedClass())
			->then
				->object($iterator->setExtensionFilterFactory($factory = function() {}))->isIdenticalTo($iterator)
				->object($iterator->getExtensionFilterFactory())->isIdenticalTo($factory)
				->object($iterator->setExtensionFilterFactory())->isIdenticalTo($iterator)
				->object($defaultFactory = $iterator->getExtensionFilterFactory())
					->isInstanceOf('closure')
					->isNotIdenticalTo($factory)
				->object($defaultFactory($iterator = new \recursiveDirectoryIterator(__DIR__), $extensions = array('foo')))->isEqualTo(new filters\recursives\extension($iterator, $extensions))
		;
	}

	public function testAcceptExtensions()
	{
		$this
			->if($iterator = new testedClass())
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
			->if($iterator = new testedClass())
			->then
				->object($iterator->acceptAllExtensions())->isIdenticalTo($iterator)
				->array($iterator->getAcceptedExtensions())->isEmpty()
		;
	}

	public function testRefuseExtension()
	{
		$this
			->if($iterator = new testedClass())
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
			->if($iterator = new testedClass())
			->then
				->object($iterator->acceptDots())->isIdenticalTo($iterator)
				->boolean($iterator->dotsAreAccepted())->isTrue()
		;
	}

	public function testRefuseDots()
	{
		$this
			->if($iterator = new testedClass())
			->then
				->object($iterator->refuseDots())->isIdenticalTo($iterator)
				->boolean($iterator->dotsAreAccepted())->isFalse()
		;
	}

	public function testGetIterator()
	{
		$this
			->mockGenerator
				->shunt('__construct')
				->generate('recursiveDirectoryIterator')
			->if($iterator = new testedClass())
			->and($iterator->setIteratorFactory(function($path) use (& $recursiveDirectoryIterator) { return ($recursiveDirectoryIterator = new \mock\recursiveDirectoryIterator($path)); }))
			->and($iterator->setDotFilterFactory(function($iterator) use (& $dotFilterIterator) { return ($dotFilterIterator = new filters\recursives\dot($iterator)); }))
			->and($iterator->setExtensionFilterFactory(function($iterator, $extensions) use (& $extensionFilterIterator) { return ($extensionFilterIterator = new filters\recursives\extension($iterator, $extensions)); }))
			->then
				->object($filterIterator = $iterator->getIterator($path = uniqid()))->isIdenticalTo($extensionFilterIterator)
				->object($filterIterator->getInnerIterator())->isIdenticalTo($dotFilterIterator)
				->object($filterIterator->getInnerIterator()->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
				->mock($filterIterator->getInnerIterator()->getInnerIterator())
					->call('__construct')->withArguments($path)->once()
			->if($iterator->acceptDots())
			->then
				->object($filterIterator = $iterator->getIterator($path = uniqid()))->isIdenticalTo($extensionFilterIterator)
				->object($filterIterator->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
				->mock($filterIterator->getInnerIterator())
					->call('__construct')->withArguments($path)->once()
			->if($iterator->refuseDots())
			->and($iterator->acceptExtensions(array()))
			->then
				->object($filterIterator = $iterator->getIterator($path = uniqid()))->isIdenticalTo($dotFilterIterator)
				->object($filterIterator->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
				->mock($filterIterator->getInnerIterator())
					->call('__construct')->withArguments($path)->once()
			->if($iterator->acceptDots())
			->and($iterator->acceptExtensions(array()))
			->then
				->object($filterIterator = $iterator->getIterator($path = uniqid()))->isIdenticalTo($recursiveDirectoryIterator)
				->mock($filterIterator)
					->call('__construct')->withArguments($path)->once()
		;
	}
}
