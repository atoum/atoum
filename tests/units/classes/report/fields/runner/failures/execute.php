<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\failures;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\failures\execute as testedClass
;

require_once __DIR__ . '/../../../../../runner.php';

class execute extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\fields\runner\failures');
	}

	public function test__construct()
	{
		$this
			->if($field = new testedClass($command = uniqid()))
			->then
				->string($field->getCommand())->isEqualTo($command)
				->object($field->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($field->getLocale())->isInstanceOf('mageekguy\atoum\locale')
		;
	}

	public function test__toString()
	{
		$this
			->if($field = new testedClass($command = '%1$s %2$s'))
			->and($field->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->system = function() {})
			->then
				->castToString($field)->isEmpty()
				->adapter($adapter)->call('system')->never()
			->if($score = new \mock\mageekguy\atoum\runner\score())
			->and($score->getMockController()->getErrors = array())
			->and($runner = new atoum\runner())
			->and($runner->setScore($score))
			->and($field->handleEvent(atoum\runner::runStart, $runner))
			->then
				->castToString($field)->isEmpty()
				->adapter($adapter)->call('system')->never()
			->if($field->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($field)->isEmpty()
				->adapter($adapter)->call('system')->never()
			->if($score->getMockController()->getFailAssertions = $fails = array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'class' => $class = uniqid(),
							'method' => $method = uniqid(),
							'file' => $file = uniqid(),
							'line' => $line = uniqid(),
							'asserter' => $asserter = uniqid(),
							'fail' => $fail = uniqid()
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'class' => $otherClass = uniqid(),
							'method' => $otherMethod = uniqid(),
							'file' => $otherFile = uniqid(),
							'line' => $otherLine = uniqid(),
							'asserter' => $otherAsserter = uniqid(),
							'fail' => $otherFail = uniqid()
						)
					)
				)
			->and($field->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($field)->isEmpty()
				->adapter($adapter)->call('system')->withArguments(sprintf($command, $file, $line))->once()
				->adapter($adapter)->call('system')->withArguments(sprintf($command, $otherFile, $otherLine))->once()
		;
	}

	public function testSetCommand()
	{
		$this
			->if($field = new testedClass(uniqid()))
			->then
				->object($field->setCommand($command = uniqid()))->isIdenticalTo($field)
				->string($field->getCommand())->isEqualTo($command)
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($field = new testedClass(uniqid()))
			->then
				->object($field->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($field)
				->object($field->getAdapter())->isEqualTo($adapter)
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($field = new testedClass(uniqid()))
			->then
				->boolean($field->handleEvent(atoum\runner::runStart, new atoum\runner()))->isFalse()
				->variable($field->getRunner())->isNull()
				->boolean($field->handleEvent(atoum\runner::runStop, $runner = new atoum\runner()))->isTrue()
				->object($field->getRunner())->isIdenticalTo($runner)
		;
	}
}
