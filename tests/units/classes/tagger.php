<?php

namespace mageekguy\atoum\tests\units;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock
;

require_once(__DIR__ . '/../runner.php');

class tagger extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->hasInterface('\mageekguy\atoum\adapter\aggregator')
			->string(\mageekguy\atoum\tagger::defaultVersionPattern)->isEqualTo('/\$Rev: [^ ]+ \$/')
		;
	}

	public function test__construct()
	{
		$tagger = new atoum\tagger();

		$this->assert
			->variable($tagger->getSrcDirectory())->isNull()
			->variable($tagger->getDestinationDirectory())->isNull()
			->variable($tagger->getVersion())->isNull()
			->string($tagger->getVersionPattern())->isEqualTo(\mageekguy\atoum\tagger::defaultVersionPattern)
			->object($tagger->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
		;

		$tagger = new atoum\tagger($adapter = new atoum\adapter());

		$this->assert
			->variable($tagger->getSrcDirectory())->isNull()
			->variable($tagger->getDestinationDirectory())->isNull()
			->variable($tagger->getVersion())->isNull()
			->string($tagger->getVersionPattern())->isEqualTo(\mageekguy\atoum\tagger::defaultVersionPattern)
			->object($tagger->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetAdapter()
	{
		$tagger = new atoum\tagger();

		$this->assert
			->object($tagger->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($tagger)
			->object($tagger->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetVersion()
	{
		$tagger = new atoum\tagger();

		$this->assert
			->object($tagger->setVersion($version = uniqid()))->isIdenticalTo($tagger)
			->string($tagger->getVersion())->isEqualTo($version)
			->object($tagger->setVersion($version = rand(1, PHP_INT_MAX)))->isIdenticalTo($tagger)
			->string($tagger->getVersion())->isEqualTo((string) $version)
		;
	}

	public function testSetVersionPattern()
	{
		$tagger = new atoum\tagger();

		$this->assert
			->object($tagger->setVersionPattern($pattern = uniqid()))->isIdenticalTo($tagger)
			->string($tagger->getVersionPattern())->isEqualTo($pattern)
			->object($tagger->setVersionPattern($pattern = rand(1, PHP_INT_MAX)))->isIdenticalTo($tagger)
			->string($tagger->getVersionPattern())->isEqualTo((string) $pattern)
		;
	}

	public function testSetSrcDirectory()
	{
		$tagger = new atoum\tagger();

		$this->assert
			->object($tagger->setSrcDirectory($directory = uniqid()))->isIdenticalTo($tagger)
			->string($tagger->getSrcDirectory())->isEqualTo($directory)
			->string($tagger->getDestinationDirectory())->isEqualTo($directory)
			->object($tagger->setSrcDirectory(($otherDirectory = uniqid()) . \DIRECTORY_SEPARATOR))->isIdenticalTo($tagger)
			->string($tagger->getSrcDirectory())->isEqualTo($otherDirectory)
			->string($tagger->getDestinationDirectory())->isEqualTo($directory)
			->object($tagger->setSrcDirectory($otherDirectory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($tagger)
			->string($tagger->getSrcDirectory())->isEqualTo((string) $otherDirectory)
			->string($tagger->getDestinationDirectory())->isEqualTo($directory)
		;
	}

	public function testSetDestinationDirectory()
	{
		$tagger = new atoum\tagger();

		$this->assert
			->object($tagger->setDestinationDirectory($directory = uniqid()))->isIdenticalTo($tagger)
			->string($tagger->getDestinationDirectory())->isEqualTo($directory)
			->object($tagger->setDestinationDirectory(($directory = uniqid()) . \DIRECTORY_SEPARATOR))->isIdenticalTo($tagger)
			->string($tagger->getDestinationDirectory())->isEqualTo($directory)
			->object($tagger->setDestinationDirectory($directory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($tagger)
			->string($tagger->getDestinationDirectory())->isEqualTo((string) $directory)
		;
	}

	public function testSetSrcIteratorInjector()
	{
		$tagger = new atoum\tagger();

		$tagger->setSrcDirectory(__DIR__);

		$this->assert
			->exception(function() use ($tagger) {
					$tagger->setSrcIteratorInjector(function() {});
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Src iterator injector must take one argument')
			->object($tagger->setSrcIteratorInjector(function($directory) { return new \recursiveDirectoryIterator($directory); }))->isIdenticalTo($tagger)
			->object($tagger->getSrcIterator())->isInstanceOf('\recursiveDirectoryIterator')
			->string($tagger->getSrcIterator()->getPath())->isEqualTo(__DIR__)
		;
	}

	public function testGetSrcIterator()
	{
		$tagger = new atoum\tagger();

		$this->assert
			->exception(function() use ($tagger) {
					$tagger->getSrcIterator();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to get files iterator, source directory is undefined')
		;

		$tagger->setSrcDirectory(__DIR__);

		$this->assert
			->object($tagger->getSrcIterator())->isInstanceOf('\recursiveIteratorIterator')
			->object($tagger->getSrcIterator()->getInnerIterator())->isInstanceOf('\mageekguy\atoum\src\iterator\filter')
		;
	}

	public function testTagVersion()
	{
		$tagger = new atoum\tagger($adapter = new atoum\test\adapter());

		$adapter->is_dir = true;
		$adapter->mkdir = function() {};

		$this->assert
			->exception(function() use ($tagger) {
					$tagger->tagVersion(uniqid());
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to tag, src directory is undefined')
		;

		$tagger->setSrcDirectory($srcDirectory = uniqid());

		$this->assert
			->exception(function() use ($tagger) {
					$tagger->tagVersion(uniqid());
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to tag, version is undefined')
		;

		$tagger
			->setVersion($version = uniqid())
			->setSrcIteratorInjector(function($directory) {})
		;

		$this->assert
			->exception(function() use ($tagger) {
					$tagger->tagVersion(uniqid());
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to tag, src iterator injector does not return an iterator')
		;

		$srcIterator = new \arrayIterator(array(
				$file1 = $srcDirectory . \DIRECTORY_SEPARATOR . ($basename1 = uniqid()),
				$file2 = $srcDirectory . \DIRECTORY_SEPARATOR . ($basename2 = uniqid()),
				$file3 = $srcDirectory . \DIRECTORY_SEPARATOR . ($basename3 = uniqid()),
			)
		);

		$tagger->setSrcIteratorInjector(function($directory) use ($srcIterator) { return $srcIterator; });

		$adapter->file_get_contents[1] = ($file1Part1 = uniqid()) . '\'$Rev: ' . rand(1, PHP_INT_MAX) . ' $\'' . ($file1Part2 = uniqid());
		$adapter->file_get_contents[2] = $contentOfFile2 = uniqid();
		$adapter->file_get_contents[3] = ($file3Part1 = uniqid()) . '"$Rev: ' . rand(1, PHP_INT_MAX) . ' $"' . ($file3Part2 = uniqid());
		$adapter->file_put_contents = function() {};

		$this->assert
			->object($tagger->tagVersion())->isIdenticalTo($tagger)
			->adapter($adapter)
				->call('file_get_contents', array($file1))
				->call('file_put_contents', array($file1, $file1Part1 . '\'' . $version . '\'' . $file1Part2, \LOCK_EX))
				->call('file_get_contents', array($file2))
				->call('file_put_contents', array($file2, $contentOfFile2, \LOCK_EX))
				->call('file_get_contents', array($file3))
				->call('file_put_contents', array($file3, $file3Part1 . '"' . $version . '"' . $file3Part2, \LOCK_EX))
		;

		$adapter->resetCalls()->file_get_contents[2] = false;

		$this->assert
			->exception(function() use ($tagger) {
					$tagger->tagVersion(uniqid());
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to tag, path \'' . $file2 . '\' is unreadable')
		;

		$adapter->resetCalls();
		$adapter->file_get_contents[2] = $contentOfFile2;
		$adapter->file_put_contents[2] = false;

		$this->assert
			->exception(function() use ($tagger) {
					$tagger->tagVersion(uniqid());
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to tag, path \'' . $file2 . '\' is unwritable')
		;

		$adapter->resetCalls();
		unset($adapter->file_put_contents[2]);

		$tagger->setDestinationDirectory($destinationDirectory = uniqid());

		$this->assert
			->object($tagger->tagVersion())->isIdenticalTo($tagger)
			->adapter($adapter)
				->call('is_dir', array($destinationDirectory))
				->notCall('mkdir')
				->call('file_get_contents', array($file1))
				->call('file_put_contents', array($destinationDirectory . \DIRECTORY_SEPARATOR . $basename1, $file1Part1 . '\'' . $version . '\'' . $file1Part2, \LOCK_EX))
				->call('file_get_contents', array($file2))
				->call('file_put_contents', array($destinationDirectory . \DIRECTORY_SEPARATOR . $basename2, $contentOfFile2, \LOCK_EX))
				->call('file_get_contents', array($file3))
				->call('file_put_contents', array($destinationDirectory . \DIRECTORY_SEPARATOR . $basename3, $file3Part1 . '"' . $version . '"' . $file3Part2, \LOCK_EX))
		;

		$adapter
			->resetCalls()
			->is_dir = false
		;

		$this->assert
			->object($tagger->tagVersion())->isIdenticalTo($tagger)
			->adapter($adapter)
				->call('is_dir', array($destinationDirectory))
				->call('mkdir', array($destinationDirectory, 0777, true))
				->call('file_get_contents', array($file1))
				->call('file_put_contents', array($destinationDirectory . \DIRECTORY_SEPARATOR . $basename1, $file1Part1 . '\'' . $version . '\'' . $file1Part2, \LOCK_EX))
				->call('file_get_contents', array($file2))
				->call('file_put_contents', array($destinationDirectory . \DIRECTORY_SEPARATOR . $basename2, $contentOfFile2, \LOCK_EX))
				->call('file_get_contents', array($file3))
				->call('file_put_contents', array($destinationDirectory . \DIRECTORY_SEPARATOR . $basename3, $file3Part1 . '"' . $version . '"' . $file3Part2, \LOCK_EX))
		;
	}
}

?>
