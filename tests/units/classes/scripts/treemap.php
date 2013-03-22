<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts\treemap as testedClass,
	mock\mageekguy\atoum\scripts\treemap\analyzer as analyzer
;

require_once __DIR__ . '/../../runner.php';

class treemap extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\script');
	}

	public function test__construct()
	{
		$this
			->if($treemap = new testedClass($name = uniqid()))
			->then
				->string($treemap->getName())->isEqualTo($name)
				->object($treemap->getAdapter())->isEqualTo(new atoum\adapter())
				->object($treemap->getIncluder())->isEqualTo(new atoum\includer())
				->variable($treemap->getProjectName())->isNull()
				->array($treemap->getDirectories())->isEmpty()
				->variable($treemap->getOutputFile())->isNull()
				->array($treemap->getAnalyzers())->isEmpty()
			->if($treemap = new testedClass($name = uniqid(), $adapter = new atoum\adapter()))
			->then
				->string($treemap->getName())->isEqualTo($name)
				->object($treemap->getAdapter())->isIdenticalTo($adapter)
				->object($treemap->getIncluder())->isEqualTo(new atoum\includer())
				->variable($treemap->getProjectName())->isNull()
				->array($treemap->getDirectories())->isEmpty()
				->variable($treemap->getOutputFile())->isNull()
				->array($treemap->getAnalyzers())->isEmpty()
		;
	}

	public function testSetProjectName()
	{
		$this
			->if($treemap = new testedClass(uniqid()))
			->then
				->object($treemap->setProjectName($projectName = uniqid()))->isIdenticalTo($treemap)
				->string($treemap->getProjectName())->isEqualTo($projectName)
		;
	}

	public function testAddDirectory()
	{
		$this
			->if($treemap = new testedClass(uniqid()))
			->then
				->object($treemap->addDirectory($directory = uniqid()))->isIdenticalTo($treemap)
				->array($treemap->getDirectories())->isEqualTo(array($directory))
				->object($treemap->addDirectory($otherDirectory = uniqid()))->isIdenticalTo($treemap)
				->array($treemap->getDirectories())->isEqualTo(array($directory, $otherDirectory))
				->object($treemap->addDirectory($directory))->isIdenticalTo($treemap)
				->array($treemap->getDirectories())->isEqualTo(array($directory, $otherDirectory))
		;
	}

	public function testSetOutputFile()
	{
		$this
			->if($treemap = new testedClass(uniqid()))
			->then
				->object($treemap->setOutputFile($file = uniqid()))->isIdenticalTo($treemap)
				->string($treemap->getOutputFile())->isEqualTo($file)
		;
	}

	public function testSetIncluder()
	{
		$this
			->if($treemap = new testedClass(uniqid()))
			->then
				->object($treemap->setIncluder($includer = new atoum\includer()))->isIdenticalTo($treemap)
				->object($treemap->getIncluder())->isIdenticalTo($includer)
				->object($treemap->setIncluder())->isIdenticalTo($treemap)
				->object($treemap->getIncluder())
					->isNotIdenticalTo($includer)
					->isEqualTo(new atoum\includer())
		;
	}

	public function testAddAnalyzer()
	{
		$this
			->if($treemap = new testedClass(uniqid()))
			->then
				->object($treemap->addAnalyzer($analyzer = new analyzer()))->isIdenticalTo($treemap)
				->array($treemap->getAnalyzers())->isEqualTo(array($analyzer))
				->object($treemap->addAnalyzer($otherAnalyzer = new analyzer()))->isIdenticalTo($treemap)
				->array($treemap->getAnalyzers())->isEqualTo(array($analyzer, $otherAnalyzer))
		;
	}

	public function testUseConfigFile()
	{
		$this
			->if($treemap = new testedClass(uniqid()))
			->and($treemap->setIncluder($includer = new \mock\atoum\includer()))
			->and($this->calling($includer)->includePath = function() {})
			->then
				->object($treemap->useConfigFile($file = uniqid()))->isIdenticalTo($treemap)
				->mock($includer)->call('includePath')->withArguments($file)->once()
			->if($this->calling($includer)->includePath->throw = new atoum\includer\exception())
			->then
				->exception(function() use ($treemap, & $file) { $treemap->useConfigFile($file = uniqid()); })
					->isInstanceOf('mageekguy\atoum\includer\exception')
					->hasMessage('Unable to find configuration file \'' . $file . '\'')
		;
	}
}
