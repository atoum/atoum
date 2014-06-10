<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\coverage;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\coverage\treemap as testedClass
;

require_once __DIR__ . '/../../../../../runner.php';

class treemap extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\fields\runner\coverage\cli');
	}

	public function test__construct()
	{
		$this
			->if($treemap = new testedClass($projectName = uniqid(), $destinationDirectory = uniqid()))
			->then
				->string($treemap->getProjectName())->isEqualTo($projectName)
				->string($treemap->getDestinationDirectory())->isEqualTo($destinationDirectory)
				->string($treemap->getTreemapUrl())->isEqualTo('/')
				->object($treemap->getAdapter())->isEqualTo(new atoum\adapter())
				->object($treemap->getUrlPrompt())->isEqualTo(new atoum\cli\prompt())
				->object($treemap->getUrlColorizer())->isEqualTo(new atoum\cli\colorizer())
				->variable($treemap->getHtmlReportBaseUrl())->isNull()
				->string($treemap->getResourcesDirectory())->isEqualTo(atoum\directory . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'coverage' . DIRECTORY_SEPARATOR . 'treemap')
		;
	}

	public function testSetHtmlReportBaseUrl()
	{
		$this
			->if($treemap = new testedClass(uniqid(), uniqid()))
			->then
				->object($treemap->setHtmlReportBaseUrl($url = uniqid()))->isIdenticalTo($treemap)
				->string($treemap->getHtmlReportBaseUrl())->isEqualTo($url)
				->object($treemap->setHtmlReportBaseUrl($url = rand(1, PHP_INT_MAX)))->isIdenticalTo($treemap)
				->string($treemap->getHtmlReportBaseUrl())->isEqualTo((string) $url)
		;
	}

	public function testSetReflectionClassFactory()
	{
		$this
			->if($treemap = new testedClass(uniqid(), uniqid()))
			->then
				->object($treemap->setReflectionClassFactory($factory = function($className) use(& $reflectionClassInstance) { return ($reflectionClassInstance = new \reflectionClass(__CLASS__)); }))->isIdenticalTo($treemap)
				->object($treemap->getReflectionClass(uniqid()))->isIdenticalTo($reflectionClassInstance)
				->object($treemap->getReflectionClass(uniqid()))->isIdenticalTo($reflectionClassInstance)
				->exception(function() use($treemap) { $treemap->setReflectionClassFactory(function() {}); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Reflection class factory must take one argument')
		;
	}

	public function testSetProjectName()
	{
		$this
			->if($treemap = new testedClass(uniqid(), uniqid()))
			->then
				->object($treemap->setProjectName($name = uniqid()))->isIdenticalTo($treemap)
				->string($treemap->getProjectName())->isEqualTo($name)
		;
	}

	public function testSetDestinationDirectory()
	{
		$this
			->if($treemap = new testedClass(uniqid(), uniqid()))
			->then
				->object($treemap->setDestinationDirectory($directory = uniqid()))->isIdenticalTo($treemap)
				->string($treemap->getDestinationDirectory())->isEqualTo($directory)
		;
	}

	public function testSetUrlPrompt()
	{
		$this
			->if($treemap = new testedClass(uniqid(), uniqid()))
			->then
				->object($treemap->setUrlPrompt($prompt = new atoum\cli\prompt()))->isIdenticalTo($treemap)
				->object($treemap->getUrlPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetUrlColorizer()
	{
		$this
			->if($treemap = new testedClass(uniqid(), uniqid()))
			->then
				->object($treemap->setUrlColorizer($colorizer = new atoum\cli\colorizer()))->isIdenticalTo($treemap)
				->object($treemap->getUrlColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetTreemapUrl()
	{
		$this
			->if($treemap = new testedClass(uniqid(), uniqid()))
			->then
				->object($treemap->setTreemapUrl($url = uniqid()))->isIdenticalTo($treemap)
				->string($treemap->getTreemapUrl())->isEqualTo($url)
				->object($treemap->setTreemapUrl($url = (uniqid() . '/')))->isIdenticalTo($treemap)
				->string($treemap->getTreemapUrl())->isEqualTo($url)
		;
	}

	public function testSetResourcesDirectory()
	{
		$this
			->if($treemap = new testedClass(uniqid(), uniqid()))
			->then
				->object($treemap->setResourcesDirectory($directory = uniqid()))->isIdenticalTo($treemap)
				->string($treemap->getResourcesDirectory())->isEqualTo($directory)
				->object($treemap->setResourcesDirectory())->isIdenticalTo($treemap)
				->string($treemap->getResourcesDirectory())->isEqualTo(atoum\directory . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'coverage' . DIRECTORY_SEPARATOR . 'treemap')
		;
	}
}
