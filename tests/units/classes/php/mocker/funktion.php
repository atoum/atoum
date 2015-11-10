<?php

namespace mageekguy\atoum\tests\units\php\mocker;

require_once __DIR__ . '/../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php
;

function doesSomething() {}

class funktion extends atoum\test
{
	public function test__set()
	{
		$this
			->given($this->newTestedInstance)

			->if($this->testedInstance->{$functionName = __NAMESPACE__ . '\version_compare'} = $returnValue = uniqid())
			->then
				->string(version_compare(uniqid(), uniqid()))->isEqualTo($returnValue)

			->if($this->testedInstance->{$functionName = __NAMESPACE__ . '\version_compare'} = $otherReturnValue = uniqid())
			->then
				->string(version_compare(uniqid(), uniqid()))->isEqualTo($otherReturnValue)

			->if($this->testedInstance->{$otherFunctionName = __NAMESPACE__ . '\file_get_contents'} = $fileContents = uniqid())
			->then
				->string(version_compare(uniqid(), uniqid()))->isEqualTo($otherReturnValue)
				->string(file_get_contents(uniqid()))->isEqualTo($fileContents)
		;
	}

	public function test__get()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->{$functionName = __NAMESPACE__ . '\version_compare'})->isInstanceOf('mageekguy\atoum\test\adapter\invoker')
				->boolean(function_exists($functionName))->isTrue()
		;
	}

	public function test__isset()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->boolean(isset($this->testedInstance->{$functionName = __NAMESPACE__ . '\version_compare'}))->isFalse()

			->if($this->testedInstance->generate($functionName))
			->then
				->boolean(isset($this->testedInstance->{$functionName}))->isTrue()
		;
	}

	public function test__unset()
	{
		$this
			->given($mocker = $this->newTestedInstance)

			->if($functionName = __NAMESPACE__ . '\version_compare')
			->when(function() use ($mocker, $functionName) { unset($mocker->{$functionName}); })
			->then
				->boolean(function_exists($functionName))->isFalse()

			->if($this->testedInstance->{$functionName} = uniqid())
			->when(function() use ($mocker, $functionName) { unset($mocker->{$functionName}); })
			->then
				->integer(version_compare('5.4.0', '5.3.0'))->isEqualTo(1)
				->integer(version_compare('5.3.0', '5.4.0'))->isEqualTo(-1)
		;
	}

	public function testUseClassNamespace()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->useClassNamespace(__CLASS__))->isTestedInstance
				->string($this->testedInstance->getDefaultNamespace())->isEqualTo(__NAMESPACE__ . '\\')
		;
	}

	public function testResetCalls()
	{
		$this
			->given($this->newTestedInstance)

			->if(
				$this->mockGenerator
					->orphanize('__construct')
					->orphanize('resetCalls'),
				php\mocker::setAdapter($adapter = new \mock\atoum\test\adapter())
			)
			->then
				->object($this->testedInstance->resetCalls())->isTestedInstance
				->mock($adapter)->call('resetCalls')->once

				->object($this->testedInstance->resetCalls($functionName = uniqid()))->isTestedInstance
				->mock($adapter)->call('resetCalls')->withArguments($functionName)->once

			->if($this->testedInstance->setDefaultNamespace($defaultNamespace = uniqid()))
			->then
				->object($this->testedInstance->resetCalls($functionName = uniqid()))->isTestedInstance
				->mock($adapter)->call('resetCalls')->withArguments($defaultNamespace . '\\' . $functionName)->once
		;
	}

	public function testGenerate()
	{
		$this
			->given($mocker = $this->newTestedInstance)
			->then
				->object($this->testedInstance->generate($functionName = __NAMESPACE__ . '\version_compare'))->isIdenticalTo($this->testedInstance)
				->boolean(function_exists($functionName))->isTrue()
				->boolean(version_compare('5.4.0', '5.3.0'))->isFalse()
				->integer(\version_compare('5.4.0', '5.3.0'))->isEqualTo(1)
				->boolean(version_compare('5.3.0', '5.4.0'))->isTrue()
				->integer(\version_compare('5.3.0', '5.4.0'))->isEqualTo(-1)
				->exception(function() use ($mocker) { $mocker->generate(__NAMESPACE__ . '\doesSomething'); })
					->isInstanceof('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Function \'' . __NAMESPACE__ . '\doesSomething\' already exists')

			->if($this->testedInstance->{$functionName} = $returnValue = uniqid())
			->then
				->string(version_compare(uniqid(), uniqid()))->isEqualTo($returnValue)
				->object($this->testedInstance->generate($functionName = __NAMESPACE__ . '\version_compare'))->isIdenticalTo($this->testedInstance)
				->boolean(version_compare('5.4.0', '5.3.0'))->isFalse()
				->boolean(version_compare('5.3.0', '5.4.0'))->isTrue()
				->object($this->testedInstance->generate($unknownFunctionName = __NAMESPACE__ . '\\foo'))->isIdenticalTo($this->testedInstance)
				->variable(foo())->isNull()

			->if($this->testedInstance->{$unknownFunctionName} = $fooReturnValue = uniqid())
			->then
				->string(foo())->isEqualTo($fooReturnValue)

			->if($this->testedInstance->{$functionName} = $returnValue = uniqid())
			->when(function() use ($mocker, $functionName) { unset($mocker->{$functionName}); })
			->then
				->boolean(version_compare('5.4.0', '5.3.0'))->isFalse()
				->boolean(version_compare('5.3.0', '5.4.0'))->isTrue()

			->when(function() use ($mocker, $unknownFunctionName) { unset($mocker->{$unknownFunctionName}); })
			->then
				->variable(foo())->isNull()
		;
	}
}
