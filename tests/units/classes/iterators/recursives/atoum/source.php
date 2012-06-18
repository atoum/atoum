<?php

namespace mageekguy\atoum\tests\units\iterators\recursives\atoum;

require_once __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\iterators
;

class source extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->hasInterface('outerIterator');
	}

	public function test__construct()
	{
		$sourceDirectory = mock\stream::get('sourceDirectory');
		$sourceDirectory->dir_opendir = true;
		$sourceDirectory->dir_closedir = true;
		$sourceDirectory->dir_rewinddir = true;
		$sourceDirectory->dir_readdir = false;

		$this
			->if($iterator = new iterators\recursives\atoum\source($sourceDirectory))
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
		$sourceDirectory = mock\stream::get('sourceDirectory');
		$sourceDirectory->opendir = true;
		$sourceDirectory->rewinddir = true;
		$sourceDirectory->readdir = false;
		$sourceDirectory->closedir = true;

		$file = mock\stream::get($sourceDirectory . '/file');

		$this
			->if($iterator = new iterators\recursives\atoum\source($sourceDirectory))
			->then
				->variable($iterator->current())->isNull()
			->if($sourceDirectory->readdir[1] = 'file')
			->and($sourceDirectory->readdir[2] = false)
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory))
			->then
				->string($iterator->current())->isEqualTo($file)
			->if($sourceDirectory->readdir[1] = '.file')
			->and($sourceDirectory->readdir[2] = false)
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory))
			->then
				->variable($iterator->current())->isNull()
		;
	}

	public function testKey()
	{
		$sourceDirectory = mock\stream::get('sourceDirectory');
		$sourceDirectory->opendir = true;
		$sourceDirectory->rewinddir = true;
		$sourceDirectory->readdir = false;
		$sourceDirectory->closedir = true;

		$file = mock\stream::get($sourceDirectory . '/file');


		$this
			->if($iterator = new iterators\recursives\atoum\source($sourceDirectory))
			->then
				->integer($iterator->key())->isZero()
			->if($sourceDirectory->readdir[1] = 'file')
			->and($sourceDirectory->readdir[2] = false)
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory))
			->then
				->string($iterator->key())->isEqualTo($file)
			->if($sourceDirectory->readdir[1] = '.file')
			->and($sourceDirectory->readdir[2] = false)
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory))
			->then
				->integer($iterator->key())->isZero()
			->if($iterator = new iterators\recursives\atoum\source($sourceDirectory, $pharDirectory = uniqid()))
			->then
				->string($iterator->key())->isEmpty()
			->if($sourceDirectory->readdir[1] = 'file')
			->and($sourceDirectory->readdir[2] = false)
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory, $pharDirectory = uniqid()))
			->then
				->string($iterator->key())->isEqualTo($pharDirectory . DIRECTORY_SEPARATOR . 'file')
			->if($sourceDirectory->readdir[1] = '.file')
			->and($sourceDirectory->readdir[2] = false)
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory, $pharDirectory = uniqid()))
			->then
				->string($iterator->key())->isEmpty()
		;
	}
}

?>
