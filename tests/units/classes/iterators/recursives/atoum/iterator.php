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
		$this->assert->testedClass->hasInterface('outerIterator');
	}

	public function test__construct()
	{
		$sourceDirectoryController = mock\stream::get('sourceDirectory');
		$sourceDirectoryController->dir_opendir = true;
		$sourceDirectoryController->dir_closedir = true;
		$sourceDirectoryController->dir_rewinddir = true;
		$sourceDirectoryController->dir_readdir = false;

		$this->assert
			->if($iterator = new iterators\recursives\atoum\source($sourceDirectory = 'atoum://sourceDirectory'))
			->then
				->string($iterator->getSourceDirectory())->isEqualTo($sourceDirectory)
				->variable($iterator->getPharDirectory())->isNull()
				->object($iterator->getInnerIterator())->isInstanceOf('recursiveIteratorIterator')
			->if($iterator = new iterators\recursives\atoum\source($sourceDirectory = 'atoum://sourceDirectory', $pharDirectory = uniqid()))
			->then
				->string($iterator->getSourceDirectory())->isEqualTo($sourceDirectory)
				->string($iterator->getPharDirectory())->isEqualTo($pharDirectory)
				->object($iterator->getInnerIterator())->isInstanceOf('recursiveIteratorIterator')
		;
	}

	public function testCurrent()
	{
		$fileController = mock\stream::get('sourceDirectory/file');
		$dotFileController = mock\stream::get('sourceDirectory/.file');

		$sourceDirectoryController = mock\stream::get('sourceDirectory');
		$sourceDirectoryController->opendir = true;
		$sourceDirectoryController->rewinddir = true;
		$sourceDirectoryController->readdir = false;
		$sourceDirectoryController->closedir = true;

		$this->assert
			->if($iterator = new iterators\recursives\atoum\source($sourceDirectory = 'atoum://sourceDirectory'))
			->then
				->variable($iterator->current())->isNull()
			->if($sourceDirectoryController->readdir[1] = 'file')
			->and($sourceDirectoryController->readdir[2] = false)
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory = 'atoum://sourceDirectory'))
			->then
				->string($iterator->current())->isEqualTo('atoum://sourceDirectory/file')
			->if($sourceDirectoryController->readdir[1] = '.file')
			->and($sourceDirectoryController->readdir[2] = false)
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory = 'atoum://sourceDirectory'))
			->then
				->variable($iterator->current())->isNull()
		;
	}

	public function testKey()
	{
		$fileController = mock\stream::get('sourceDirectory/file');
		$dotFileController = mock\stream::get('sourceDirectory/.file');

		$sourceDirectoryController = mock\stream::get('sourceDirectory');
		$sourceDirectoryController->opendir = true;
		$sourceDirectoryController->rewinddir = true;
		$sourceDirectoryController->readdir = false;
		$sourceDirectoryController->closedir = true;

		$this->assert
			->if($iterator = new iterators\recursives\atoum\source($sourceDirectory = 'atoum://sourceDirectory'))
			->then
				->integer($iterator->key())->isZero()
			->if($sourceDirectoryController->readdir[1] = 'file')
			->and($sourceDirectoryController->readdir[2] = false)
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory = 'atoum://sourceDirectory'))
			->then
				->string($iterator->key())->isEqualTo('atoum://sourceDirectory/file')
			->if($sourceDirectoryController->readdir[1] = '.file')
			->and($sourceDirectoryController->readdir[2] = false)
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory = 'atoum://sourceDirectory'))
			->then
				->integer($iterator->key())->isZero()
			->if($iterator = new iterators\recursives\atoum\source($sourceDirectory = 'atoum://sourceDirectory', $pharDirectory = uniqid()))
			->then
				->string($iterator->key())->isEmpty()
			->if($sourceDirectoryController->readdir[1] = 'file')
			->and($sourceDirectoryController->readdir[2] = false)
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory = 'atoum://sourceDirectory', $pharDirectory = uniqid()))
			->then
				->string($iterator->key())->isEqualTo($pharDirectory . '/file')
			->if($sourceDirectoryController->readdir[1] = '.file')
			->and($sourceDirectoryController->readdir[2] = false)
			->and($iterator = new iterators\recursives\atoum\source($sourceDirectory = 'atoum://sourceDirectory', $pharDirectory = uniqid()))
			->then
				->string($iterator->key())->isEmpty()
		;
	}
}

?>
