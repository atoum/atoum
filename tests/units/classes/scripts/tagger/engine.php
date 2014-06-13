<?php

namespace mageekguy\atoum\tests\units\scripts\tagger;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\scripts\tagger
;

require_once __DIR__ . '/../../../runner.php';

class engine extends atoum\test
{
	public function testClassConstants()
	{
		$this
			->testedClass
				->hasConstant('defaultVersionPattern')->isEqualTo('/\$Rev: [^ %]+ \$/')
		;
	}

	public function test__construct()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getSrcDirectory())->isNull()
				->variable($this->testedInstance->getDestinationDirectory())->isNull()
				->variable($this->testedInstance->getVersion())->isNull()
				->string($this->testedInstance->getVersionPattern())->isEqualTo(\mageekguy\atoum\scripts\tagger\engine::defaultVersionPattern)
				->object($this->testedInstance->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			->if($this->newTestedInstance($adapter = new atoum\adapter()))
			->then
				->variable($this->testedInstance->getSrcDirectory())->isNull()
				->variable($this->testedInstance->getDestinationDirectory())->isNull()
				->variable($this->testedInstance->getVersion())->isNull()
				->string($this->testedInstance->getVersionPattern())->isEqualTo(\mageekguy\atoum\scripts\tagger\engine::defaultVersionPattern)
				->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setAdapter($adapter = new atoum\adapter()))->isTestedInstance
				->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetVersion()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setVersion($version = uniqid()))->isTestedInstance
				->string($this->testedInstance->getVersion())->isEqualTo($version)
				->object($this->testedInstance->setVersion($version = rand(1, PHP_INT_MAX)))->isTestedInstance
				->string($this->testedInstance->getVersion())->isEqualTo((string) $version)
		;
	}

	public function testSetVersionPattern()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setVersionPattern($pattern = uniqid()))->isTestedInstance
				->string($this->testedInstance->getVersionPattern())->isEqualTo($pattern)
				->object($this->testedInstance->setVersionPattern($pattern = rand(1, PHP_INT_MAX)))->isTestedInstance
				->string($this->testedInstance->getVersionPattern())->isEqualTo((string) $pattern)
		;
	}

	public function testSetSrcDirectory()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setSrcDirectory($directory = uniqid()))->isTestedInstance
				->string($this->testedInstance->getSrcDirectory())->isEqualTo($directory)
				->string($this->testedInstance->getDestinationDirectory())->isEqualTo($directory)
				->object($this->testedInstance->setSrcDirectory(($otherDirectory = uniqid()) . \DIRECTORY_SEPARATOR))->isTestedInstance
				->string($this->testedInstance->getSrcDirectory())->isEqualTo($otherDirectory)
				->string($this->testedInstance->getDestinationDirectory())->isEqualTo($directory)
				->object($this->testedInstance->setSrcDirectory($otherDirectory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isTestedInstance
				->string($this->testedInstance->getSrcDirectory())->isEqualTo((string) $otherDirectory)
				->string($this->testedInstance->getDestinationDirectory())->isEqualTo($directory)
		;
	}

	public function testSetDestinationDirectory()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setDestinationDirectory($directory = uniqid()))->isTestedInstance
				->string($this->testedInstance->getDestinationDirectory())->isEqualTo($directory)
				->object($this->testedInstance->setDestinationDirectory(($directory = uniqid()) . \DIRECTORY_SEPARATOR))->isTestedInstance
				->string($this->testedInstance->getDestinationDirectory())->isEqualTo($directory)
				->object($this->testedInstance->setDestinationDirectory($directory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isTestedInstance
				->string($this->testedInstance->getDestinationDirectory())->isEqualTo((string) $directory)
		;
	}

	public function testSetSrcIteratorInjector()
	{
		$this
			->if(
				$tagger = $this->newTestedInstance,
				$this->testedInstance->setSrcDirectory(__DIR__)
			)
			->then
				->exception(function() use ($tagger) {
						$tagger->setSrcIteratorInjector(function() {});
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Src iterator injector must take one argument')
				->object($this->testedInstance->setSrcIteratorInjector(function($directory) { return new \recursiveDirectoryIterator($directory); }))->isTestedInstance
				->object($this->testedInstance->getSrcIterator())->isInstanceOf('recursiveDirectoryIterator')
				->string($this->testedInstance->getSrcIterator()->getPath())->isEqualTo(__DIR__)
		;
	}

	public function testGetSrcIterator()
	{
		$this
			->if($tagger = $this->newTestedInstance)
			->then
				->exception(function() use ($tagger) {
						$tagger->getSrcIterator();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Unable to get files iterator, source directory is undefined')
			->if($this->testedInstance->setSrcDirectory(__DIR__))
			->then
				->object($this->testedInstance->getSrcIterator())->isInstanceOf('recursiveIteratorIterator')
				->object($this->testedInstance->getSrcIterator()->getInnerIterator())->isInstanceOf('mageekguy\atoum\iterators\filters\recursives\dot')
		;
	}

	public function testTagVersion()
	{
		$this
			->if(
				$tagger = $this->newTestedInstance($adapter = new atoum\test\adapter()),
				$adapter->is_dir = true,
				$adapter->mkdir = function() {}
			)
			->then
				->exception(function() use ($tagger) {
						$tagger->tagVersion(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Unable to tag, src directory is undefined')
			->if($this->testedInstance->setSrcDirectory($srcDirectory = uniqid()))
			->then
				->exception(function() use ($tagger) {
						$tagger->tagVersion(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Unable to tag, version is undefined')
			->if(
				$tagger
					->setVersion($version = uniqid())
					->setSrcIteratorInjector(function($directory) {})
			)
			->then
				->exception(function() use ($tagger) {
						$tagger->tagVersion(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Unable to tag, src iterator injector does not return an iterator')
			->if(
				$srcIterator = new \arrayIterator(array(
						$file1 = $srcDirectory . \DIRECTORY_SEPARATOR . ($basename1 = uniqid()),
						$file2 = $srcDirectory . \DIRECTORY_SEPARATOR . ($basename2 = uniqid()),
						$file3 = $srcDirectory . \DIRECTORY_SEPARATOR . ($basename3 = uniqid()),
					)
				),
				$this->testedInstance->setSrcIteratorInjector(function($directory) use ($srcIterator) { return $srcIterator; }),
				$adapter->file_get_contents[1] = ($file1Part1 = uniqid()) . '\'$Rev: ' . rand(1, PHP_INT_MAX) . ' $\'' . ($file1Part2 = uniqid()),
				$adapter->file_get_contents[2] = $contentOfFile2 = uniqid(),
				$adapter->file_get_contents[3] = ($file3Part1 = uniqid()) . '"$Rev: ' . rand(1, PHP_INT_MAX) . ' $"' . ($file3Part2 = uniqid()),
				$adapter->file_put_contents = function() {}
			)
			->then
				->object($this->testedInstance->tagVersion())->isTestedInstance
				->adapter($adapter)
					->call('file_get_contents')->withArguments($file1)->once()
					->call('file_put_contents')->withArguments($file1, $file1Part1 . '\'' . $version . '\'' . $file1Part2, \LOCK_EX)->once()
					->call('file_get_contents')->withArguments($file2)->once()
					->call('file_put_contents')->withArguments($file2, $contentOfFile2, \LOCK_EX)->once()
					->call('file_get_contents')->withArguments($file3)->once()
					->call('file_put_contents')->withArguments($file3, $file3Part1 . '"' . $version . '"' . $file3Part2, \LOCK_EX)->once()
			->if($adapter->resetCalls()->file_get_contents[2] = false)
			->then
				->exception(function() use ($tagger) {
						$tagger->tagVersion(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to tag, path \'' . $file2 . '\' is unreadable')
			->if(
				$adapter->resetCalls(),
				$adapter->file_get_contents[2] = $contentOfFile2,
				$adapter->file_put_contents[2] = false
			)
			->then
				->exception(function() use ($tagger) {
						$tagger->tagVersion(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to tag, path \'' . $file2 . '\' is unwritable')
			->if(
				$adapter->resetCalls(),
				$this->testedInstance->setDestinationDirectory($destinationDirectory = uniqid())
			)
			->when(function() use ($adapter) { unset($adapter->file_put_contents[2]); })
			->then
				->object($this->testedInstance->tagVersion())->isTestedInstance
				->adapter($adapter)
					->call('is_dir')->withArguments($destinationDirectory)->exactly(3)
					->call('mkdir')->never()
					->call('file_get_contents')->withArguments($file1)->once()
					->call('file_put_contents')->withArguments($destinationDirectory . \DIRECTORY_SEPARATOR . $basename1, $file1Part1 . '\'' . $version . '\'' . $file1Part2, \LOCK_EX)->once()
					->call('file_get_contents')->withArguments($file2)->once()
					->call('file_put_contents')->withArguments($destinationDirectory . \DIRECTORY_SEPARATOR . $basename2, $contentOfFile2, \LOCK_EX)->once()
					->call('file_get_contents')->withArguments($file3)->once()
					->call('file_put_contents')->withArguments($destinationDirectory . \DIRECTORY_SEPARATOR . $basename3, $file3Part1 . '"' . $version . '"' . $file3Part2, \LOCK_EX)->once()
			->if(
				$adapter
					->resetCalls()
					->is_dir = false
			)
			->then
				->object($this->testedInstance->tagVersion())->isTestedInstance
				->adapter($adapter)
					->call('is_dir')->withArguments($destinationDirectory)->exactly(3)
					->call('mkdir')->withArguments($destinationDirectory, 0777, true)->exactly(3)
					->call('file_get_contents')->withArguments($file1)->once()
					->call('file_put_contents')->withArguments($destinationDirectory . \DIRECTORY_SEPARATOR . $basename1, $file1Part1 . '\'' . $version . '\'' . $file1Part2, \LOCK_EX)->once()
					->call('file_get_contents')->withArguments($file2)->once()
					->call('file_put_contents')->withArguments($destinationDirectory . \DIRECTORY_SEPARATOR . $basename2, $contentOfFile2, \LOCK_EX)->once()
					->call('file_get_contents')->withArguments($file3)->once()
					->call('file_put_contents')->withArguments($destinationDirectory . \DIRECTORY_SEPARATOR . $basename3, $file3Part1 . '"' . $version . '"' . $file3Part2, \LOCK_EX)->once()
		;
	}
}
