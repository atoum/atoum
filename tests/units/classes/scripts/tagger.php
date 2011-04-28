<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\scripts
;

require_once(__DIR__ . '/../../runner.php');

class tagger extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubclassOf('\mageekguy\atoum\script')
				->hasInterface('\mageekguy\atoum\adapter\aggregator')
			->string(\mageekguy\atoum\scripts\tagger::versionTag)->isEqualTo('<tagger:version />')
		;
	}

	public function test__construct()
	{
		$tagger = new scripts\tagger(uniqid());

		$this->assert
			->variable($tagger->getSrcDirectory())->isNull()
			->variable($tagger->getDestinationDirectory())->isNull()
		;
	}

	public function testSetSrcDirectory()
	{
		$tagger = new scripts\tagger(uniqid());

		$this->assert
			->object($tagger->setSrcDirectory($directory = uniqid()))->isIdenticalTo($tagger)
			->string($tagger->getSrcDirectory())->isEqualTo($directory)
			->string($tagger->getDestinationDirectory())->isEqualTo($directory)
			->object($tagger->setSrcDirectory($otherDirectory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($tagger)
			->string($tagger->getSrcDirectory())->isEqualTo((string) $otherDirectory)
			->string($tagger->getDestinationDirectory())->isEqualTo($directory)
		;
	}

	public function testSetDestinationDirectory()
	{
		$tagger = new scripts\tagger(uniqid());

		$this->assert
			->object($tagger->setDestinationDirectory($directory = uniqid()))->isIdenticalTo($tagger)
			->string($tagger->getDestinationDirectory())->isEqualTo($directory)
			->object($tagger->setDestinationDirectory($directory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($tagger)
			->string($tagger->getDestinationDirectory())->isEqualTo((string) $directory)
		;
	}

	public function testSetSrcIteratorInjector()
	{
		$tagger = new scripts\tagger(uniqid());

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
		$tagger = new scripts\tagger(uniqid());

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
		$tagger = new scripts\tagger(uniqid(), null, $adapter = new atoum\test\adapter());

		$this->assert
			->exception(function() use ($tagger) {
					$tagger->tagVersion(uniqid());
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to tag, src directory is undefined')
		;

		$tagger
			->setSrcDirectory(uniqid())
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

		$srcIterator = new \arrayIterator(array($file1 = uniqid(), $file2 = uniqid(), $file3 = uniqid()));

		$tagger->setSrcIteratorInjector(function($directory) use ($srcIterator) { return $srcIterator; });

		$adapter->file_get_contents[1] = ($file1Part1 = uniqid()) . \mageekguy\atoum\scripts\tagger::versionTag . ($file1Part2 = uniqid());
		$adapter->file_get_contents[2] = $contentOfFile2 = uniqid();
		$adapter->file_get_contents[3] = ($file3Part1 = uniqid()) . \mageekguy\atoum\scripts\tagger::versionTag . ($file3Part2 = uniqid());
		$adapter->file_put_contents = function() {};

		$this->assert
			->object($tagger->tagVersion($version = uniqid()))->isIdenticalTo($tagger)
			->adapter($adapter)
				->call('file_get_contents', array($file1))
				->call('file_put_contents', array($file1, $file1Part1 . $version . $file1Part2))
				->call('file_get_contents', array($file2))
				->call('file_put_contents', array($file2, $contentOfFile2))
				->call('file_get_contents', array($file3))
				->call('file_put_contents', array($file3, $file3Part1 . $version . $file3Part2))
		;
	}
}

?>
