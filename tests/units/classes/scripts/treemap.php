<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts\treemap as testedClass,
	mock\mageekguy\atoum\scripts\treemap\analyzer as analyzer,
	mock\mageekguy\atoum\scripts\treemap\categorizer as categorizer
;

require_once __DIR__ . '/../../runner.php';

class treemap extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\script\configurable');
	}

	public function testClassConstants()
	{
		$this->string(testedClass::defaultConfigFile)->isEqualTo('.treemap.php');
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
				->variable($treemap->getOutputDirectory())->isNull()
				->array($treemap->getAnalyzers())->isEmpty()
			->if($treemap = new testedClass($name = uniqid(), $adapter = new atoum\adapter()))
			->then
				->string($treemap->getName())->isEqualTo($name)
				->object($treemap->getAdapter())->isIdenticalTo($adapter)
				->object($treemap->getIncluder())->isEqualTo(new atoum\includer())
				->variable($treemap->getProjectName())->isNull()
				->array($treemap->getDirectories())->isEmpty()
				->variable($treemap->getOutputDirectory())->isNull()
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

	public function testSetProjectUrl()
	{
		$this
			->if($treemap = new testedClass(uniqid()))
			->then
				->object($treemap->setProjectUrl($projectUrl = uniqid()))->isIdenticalTo($treemap)
				->string($treemap->getProjectUrl())->isEqualTo($projectUrl)
		;
	}

	public function testSetCodeUrl()
	{
		$this
			->if($treemap = new testedClass(uniqid()))
			->then
				->object($treemap->setCodeUrl($codeUrl = uniqid()))->isIdenticalTo($treemap)
				->string($treemap->getCodeUrl())->isEqualTo($codeUrl)
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

	public function testSetHtmlDirectory()
	{
		$this
			->if($treemap = new testedClass(uniqid()))
			->then
				->object($treemap->setHtmlDirectory($directory = uniqid()))->isIdenticalTo($treemap)
				->string($treemap->getHtmlDirectory())->isEqualTo($directory)
		;
	}

	public function testSetOutputDirectory()
	{
		$this
			->if($treemap = new testedClass(uniqid()))
			->then
				->object($treemap->setOutputDirectory($directory = uniqid()))->isIdenticalTo($treemap)
				->string($treemap->getOutputDirectory())->isEqualTo($directory)
		;
	}

	public function testGetOnlyJsonFile()
	{
		$this
			->if($treemap = new testedClass(uniqid()))
			->then
				->boolean($treemap->getOnlyJsonFile())->isFalse()
				->boolean($treemap->getOnlyJsonFile(null))->isFalse()
				->boolean($treemap->getOnlyJsonFile(true))->isTrue()
				->boolean($treemap->getOnlyJsonFile())->isTrue()
				->boolean($treemap->getOnlyJsonFile(null))->isTrue()
				->boolean($treemap->getOnlyJsonFile(false))->isFalse()
				->boolean($treemap->getOnlyJsonFile())->isFalse()
				->boolean($treemap->getOnlyJsonFile(null))->isFalse()
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

	public function testAddCategorizer()
	{
		$this
			->if($treemap = new testedClass(uniqid()))
			->then
				->object($treemap->addCategorizer($categorizer = new categorizer(uniqid())))->isIdenticalTo($treemap)
				->array($treemap->getCategorizers())->isEqualTo(array($categorizer))
				->object($treemap->addCategorizer($otherCategorizer = new categorizer(uniqid())))->isIdenticalTo($treemap)
				->array($treemap->getCategorizers())->isEqualTo(array($categorizer, $otherCategorizer))
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
