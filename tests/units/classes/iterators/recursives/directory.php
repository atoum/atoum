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
				->object($iterator->getDependencies())->isInstanceOf('mageekguy\atoum\dependencies')
			->if($iterator = new recursives\directory($path = uniqid(), $dependencies = new atoum\dependencies()))
			->then
				->string($iterator->getPath())->isEqualTo($path)
				->boolean($iterator->dotsAreAccepted())->isFalse()
				->array($iterator->getAcceptedExtensions())->isEqualTo(array('php'))
				->object($iterator->getDependencies())->isIdenticalTo($dependencies)
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

	public function testSetDependencies()
	{
		$this
			->if($iterator = new recursives\directory())
			->then
				->object($iterator->setDependencies($dependencies = new atoum\dependencies()))->isIdenticalTo($iterator)
				->object($iterator->getDependencies())->isIdenticalTo($dependencies)
				->object($dependencies['iterator'](array('directory' => __DIR__)))->isInstanceOf('recursiveDirectoryIterator')
				->string($dependencies['iterator'](array('directory' => __DIR__))->getPath())->isEqualTo(__DIR__)
				->object($dependencies['filters\dot'](array('iterator' => $iterator = new \recursiveDirectoryIterator(__DIR__))))->isEqualTo(new filters\recursives\dot($iterator))
				->object($dependencies['filters\extension'](array('iterator' => $iterator = new \recursiveDirectoryIterator(__DIR__), 'extensions' => $extensions = array('php'))))->isEqualTo(new filters\recursives\extension($iterator, $extensions))
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
			->if($dependencies = new atoum\dependencies())
			->and($dependencies['iterator'] = function($dependencies) use (& $recursiveDirectoryIterator) { return ($recursiveDirectoryIterator = new \mock\recursiveDirectoryIterator($dependencies['directory']())); })
			->and($dependencies['filters\dot'] = function($dependencies) use (& $dotFilterIterator) { return ($dotFilterIterator = new filters\recursives\dot($dependencies['iterator']())); })
			->and($dependencies['filters\extension'] = function($dependencies) use (& $extensionFilterIterator) { return ($extensionFilterIterator = new filters\recursives\extension($dependencies['iterator'](), $dependencies['extensions']())); })
			->and($iterator = new recursives\directory($path = uniqid(), $dependencies))
			->then
				->object($filterIterator = $iterator->getIterator())->isIdenticalTo($extensionFilterIterator)
				->object($filterIterator->getInnerIterator())->isIdenticalTo($dotFilterIterator)
				->object($filterIterator->getInnerIterator()->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
				->mock($filterIterator->getInnerIterator()->getInnerIterator())
					->call('__construct')->withArguments($path)->once()
			->if($iterator->acceptDots())
			->then
				->object($filterIterator = $iterator->getIterator())->isIdenticalTo($extensionFilterIterator)
				->object($filterIterator->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
				->mock($filterIterator->getInnerIterator())
					->call('__construct')->withArguments($path)->once()
			->stop()
			->if($iterator->refuseDots())
			->and($iterator->acceptExtensions(array()))
			->then
				->object($filterIterator = $iterator->getIterator())->isIdenticalTo($dotFilterIterator)
				->object($filterIterator->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
				->mock($filterIterator->getInnerIterator())
					->call('__construct')->withArguments($path)->once()
			->if($iterator->acceptDots())
			->and($iterator->acceptExtensions(array()))
			->then
				->object($filterIterator = $iterator->getIterator())->isIdenticalTo($recursiveDirectoryIterator)
				->mock($filterIterator)
					->call('__construct')->withArguments($path)->once()
			->if($iterator = new recursives\directory(null, $dependencies))
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
				->mock($filterIterator->getInnerIterator()->getInnerIterator())
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
