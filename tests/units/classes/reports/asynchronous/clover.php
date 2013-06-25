<?php

namespace mageekguy\atoum\tests\units\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\score,
	mageekguy\atoum\mock,
	mageekguy\atoum\reports\asynchronous\clover as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class clover extends atoum\test
{
	public function beforeTestMethod($method)
	{
		$this->extension('libxml')->isLoaded();
	}

	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\reports\asynchronous');
	}

	public function testClassConstants()
	{
		$this
			->string(testedClass::defaultTitle)->isEqualTo('atoum code coverage')
			->string(testedClass::defaultPackage)->isEqualTo('atoumCodeCoverage')
			->string(testedClass::lineTypeMethod)->isEqualTo('method')
			->string(testedClass::lineTypeStatement)->isEqualTo('stmt')
			->string(testedClass::lineTypeConditional)->isEqualTo('cond')
		;
	}

	public function test__construct()
	{
		$this
			->if($report = new testedClass($adapter = new atoum\test\adapter()))
			->then
				->array($report->getFields(atoum\runner::runStart))->isEmpty()
				->object($report->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($report->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->array($report->getFields())->isEmpty()
				->adapter($adapter)->call('extension_loaded')->withArguments('libxml')->once()
			->if($adapter->extension_loaded = false)
			->then
				->exception(function() use ($adapter) {
								new testedClass($adapter);
							}
						)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('libxml PHP extension is mandatory for clover report')
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->if($adapter->extension_loaded = true)
			->and($report = new testedClass($adapter))
			->and($score = new \mock\mageekguy\atoum\score())
			->and($coverage = new \mock\mageekguy\atoum\score\coverage())
			->and($writer = new \mock\mageekguy\atoum\writers\file())
			->and($writer->getMockController()->write = $writer)
			->then
				->when(function() use ($report, $writer) {
						$report->addWriter($writer)->handleEvent(atoum\runner::runStop, new \mageekguy\atoum\runner());
					})
					->mock($writer)->call('writeAsynchronousReport')->withArguments($report)->once()
			->and($adapter->time = 762476400)
			->and($adapter->uniqid = 'foo')
			->and($observable = new \mock\mageekguy\atoum\runner())
			->and($observable->getMockController()->getScore = $score)
			->and($score->getMockController()->getCoverage = $coverage)
			->and($coverage->getMockController()->getClasses = array())
			->and($filepath = join(
				DIRECTORY_SEPARATOR,
				array(
					__DIR__,
					'clover',
					'resources',
					'1.xml'
				)
			))
			->and($report = new testedClass($adapter))
			->then
				->object($report->handleEvent(atoum\runner::runStop, $observable))->isIdenticalTo($report)
				->castToString($report)->isEqualToContentsOfFile($filepath)
			->if($coverage->getMockController()->getClasses = array())
			->and($classController = new mock\controller())
			->and($classController->disableMethodChecking())
			->and($classController->__construct = function() {})
			->and($classController->getName = $className = 'bar')
			->and($classController->getFileName = $classFile = 'foo/bar.php')
			->and($classController->getTraits = array())
			->and($classController->getStartLine = 1)
			->and($classController->getEndLine = 12)
			->and($class = new \mock\reflectionClass(uniqid(), $classController))
			->and($methodController = new mock\controller())
			->and($methodController->__construct = function() {})
			->and($methodController->getName = $methodName = 'baz')
			->and($methodController->isAbstract = false)
			->and($methodController->getFileName = $classFile)
			->and($methodController->getDeclaringClass = $class)
			->and($methodController->getStartLine = 4)
			->and($methodController->getEndLine = 8)
			->and($classController->getMethods = array(new \mock\reflectionMethod($className, $methodName, $methodController)))
			->and($coverage->getMockController()->getClasses = array(
				$className => $classFile,
				'foo' => 'bar/foo.php'
			))
			->and($xdebugData = array(
				$classFile =>
				array(
					3 => 1,
					4 => 1,
					5 => 1,
					6 => 0,
					7 => 1,
					8 => 1,
					9 => 1
				)
			))
			->and($filepath = join(
				DIRECTORY_SEPARATOR,
				array(
					__DIR__,
					'clover',
					'resources',
					'2.xml'
				)
			))
			->and($coverage->setReflectionClassFactory(function() use ($class) { return $class; }))
			->and($coverage->addXdebugDataForTest($this, $xdebugData))
			->then
				->object($report->handleEvent(atoum\runner::runStop, $observable))->isIdenticalTo($report)
				->castToString($report)->isEqualToContentsOfFile($filepath)
		;
	}

	public function testGetTitle()
	{
		$this
			->if($report = new testedClass())
			->then
				->string($report->getTitle())->isEqualTo(testedClass::defaultTitle)
			->if($report->setTitle($title = uniqid()))
			->then
				->string($report->getTitle())->isEqualTo($title)
		;
	}

	public function testSetTitle()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->setTitle($title = uniqid()))->isIdenticalTo($report)
				->string($report->getTitle())->isEqualTo($title)
		;
	}

	public function testGetPackage()
	{
		$this
			->if($report = new testedClass())
			->then
				->string($report->getPackage())->isEqualTo(testedClass::defaultPackage)
			->if($report->setPackage($package = uniqid()))
			->then
				->string($report->getPackage())->isEqualTo($package)
		;
	}

	public function testSetPackage()
	{
		$this
			->if($report = new testedClass())
			->then
				->object($report->setPackage($package = uniqid()))->isIdenticalTo($report)
				->string($report->getPackage())->isEqualTo($package)
		;
	}
}
