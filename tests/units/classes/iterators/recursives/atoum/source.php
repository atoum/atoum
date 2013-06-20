<?php

namespace mageekguy\atoum\tests\units\iterators\recursives\atoum;

require_once __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\iterators,
	mageekguy\atoum\mock\stream
;

class source extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->hasInterface('outerIterator');
	}

	public function test__construct()
	{
		$this
			->if($sourceDirectory = stream::get())
			->and($sourceDirectory->dir_opendir = true)
			->and($sourceDirectory->dir_closedir = true)
			->and($sourceDirectory->dir_rewinddir = true)
			->and($sourceDirectory->dir_readdir = false)
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory))
			->then
				->string($iterator->getSourceDirectory())->isEqualTo($sourceDirectory)
				->variable($iterator->getPharDirectory())->isNull()
				->object($iterator->getInnerIterator())->isInstanceOf('recursiveIteratorIterator')
			->if($iterator = new iterators\recursives\atoum\source($sourceDirectory, $pharDirectory = uniqid()))
			->then
				->string($iterator->getSourceDirectory())->isEqualTo($sourceDirectory)
				->string($iterator->getPharDirectory())->isEqualTo($pharDirectory)
				->object($iterator->getInnerIterator())->isInstanceOf('recursiveIteratorIterator')
		;
	}

	public function testCurrent()
	{
		$this
			->if($sourceDirectory = stream::get())
			->and($sourceDirectory->opendir = true)
			->and($sourceDirectory->rewinddir = true)
			->and($sourceDirectory->readdir = false)
			->and($sourceDirectory->closedir = true)
			->and($file = stream::getSubStream($sourceDirectory))
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory))
			->then
				->variable($iterator->current())->isNull()
			->if($sourceDirectory->readdir[1] = $file->getBasename())
			->and($sourceDirectory->readdir[2] = false)
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory))
			->then
				->string($iterator->current())->isEqualTo($file)
			->if($sourceDirectory->readdir[1] = '.file')
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory))
			->then
				->variable($iterator->current())->isNull()
		;
	}

	public function testKey()
	{
		$this
			->if($sourceDirectory = stream::get())
			->and($sourceDirectory->opendir = true)
			->and($sourceDirectory->rewinddir = true)
			->and($sourceDirectory->readdir = false)
			->and($sourceDirectory->closedir = true)
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory))
			->then
				->variable($iterator->key())->isNull()
			->if($file = stream::getSubStream($sourceDirectory))
			->and($sourceDirectory->readdir[1] = $file->getBasename())
			->and($sourceDirectory->readdir[2] = false)
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory))
			->then
				->string($iterator->key())->isEqualTo($file)
			->if($sourceDirectory->readdir[1] = '.file')
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory))
			->then
				->variable($iterator->key())->isNull()
			->if($iterator = new iterators\recursives\atoum\source($sourceDirectory, $pharDirectory = uniqid()))
			->then
				->variable($iterator->key())->isNull()
			->if($sourceDirectory->readdir[1] = $file->getBasename())
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory, $pharDirectory = uniqid()))
			->then
				->string($iterator->key())->isEqualTo($pharDirectory . DIRECTORY_SEPARATOR . $file->getBasename())
			->if($sourceDirectory->readdir[1] = '.file')
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory, $pharDirectory = uniqid()))
			->then
				->variable($iterator->key())->isNull()
		;
	}
}
