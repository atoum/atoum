<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\failures\execute\unix;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\failures\execute\unix\phpstorm as testedClass
;

require_once __DIR__ . '/../../../../../../../runner.php';

class phpstorm extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass->isSubclassOf('mageekguy\atoum\report\fields\runner\failures\execute')
		;
	}

	public function test__construct()
	{
		$this
			->if($field = new testedClass($command = uniqid()))
			->then
				->string($field->getCommand())->isEqualTo($command . ' --line %2$d %1$s &> /dev/null &')
		;
	}

	public function testGetCommand()
	{
		$this
			->if($field = new testedClass($command = uniqid()))
			->then
				->string($field->getCommand())->isEqualTo($command . ' --line %2$d %1$s &> /dev/null &')
		;
	}

	public function test__toString()
	{
		$this
			->if($field = new testedClass($command = uniqid()))
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
							'line' => $line = rand(1, PHP_INT_MAX),
							'asserter' => $asserter = uniqid(),
							'fail' => $fail = uniqid()
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'class' => $otherClass = uniqid(),
							'method' => $otherMethod = uniqid(),
							'file' => $otherFile = uniqid(),
							'line' => $otherLine = rand(1, PHP_INT_MAX),
							'asserter' => $otherAsserter = uniqid(),
							'fail' => $otherFail = uniqid()
						)
					)
				)
			->and($field->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($field)->isEmpty()
				->adapter($adapter)->call('system')->withArguments($command . ' --line ' . $line . ' ' . $file . ' &> /dev/null &')->once()
				->adapter($adapter)->call('system')->withArguments($command . ' --line ' . $otherLine . ' ' . $otherFile . ' &> /dev/null &')->once()
		;
	}

	public function testSetCommand()
	{
		$this
			->if($field = new testedClass(uniqid()))
			->then
				->object($field->setCommand($command = uniqid()))->isIdenticalTo($field)
				->string($field->getCommand())->isEqualTo($command . ' --line %2$d %1$s &> /dev/null &')
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($field = new testedClass(uniqid()))
			->then
				->object($field->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($field)
				->object($field->getAdapter())->isEqualTo($adapter)
				->object($field->setAdapter())->isIdenticalTo($field)
				->object($field->getAdapter())
					->isNotIdenticalTo($adapter)
					->isEqualTo(new atoum\adapter())
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($field = new testedClass(uniqid()))
			->and($field->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->system = function() {})
			->then
				->boolean($field->handleEvent(atoum\runner::runStart, new atoum\runner()))->isFalse()
				->variable($field->getRunner())->isNull()
				->boolean($field->handleEvent(atoum\runner::runStop, $runner = new atoum\runner()))->isTrue()
				->object($field->getRunner())->isIdenticalTo($runner)
		;
	}
}
