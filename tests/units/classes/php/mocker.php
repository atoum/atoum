<?php

namespace atoum\tests\units\php;

require_once __DIR__ . '/../../runner.php';

use
	atoum,
	atoum\php\mocker as testedClass
;

function doSomething() {}

class mocker extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($php = new testedClass())
			->then
				->string($php->getDefaultNamespace())->isEmpty()
		;
	}

	public function test__isset()
	{
		$this
			->if($php = new testedClass())
			->then
				->boolean(isset($php->{$functionName = __NAMESPACE__ . '\version_compare'}))->isFalse()
			->if($php->generate($functionName))
			->then
				->boolean(isset($php->{$functionName}))->isTrue()
		;
	}

	public function test__set()
	{
		$this
			->if($php = new testedClass())
			->and($php->{$functionName = __NAMESPACE__ . '\version_compare'} = $returnValue = uniqid())
			->then
				->string(version_compare(uniqid(), uniqid()))->isEqualTo($returnValue)
			->if($php->{$functionName = __NAMESPACE__ . '\version_compare'} = $otherReturnValue = uniqid())
			->then
				->string(version_compare(uniqid(), uniqid()))->isEqualTo($otherReturnValue)
			->if($php->{$otherFunctionName = __NAMESPACE__ . '\file_get_contents'} = $fileContents = uniqid())
			->then
				->string(version_compare(uniqid(), uniqid()))->isEqualTo($otherReturnValue)
				->string(file_get_contents(uniqid()))->isEqualTo($fileContents)
		;
	}

	public function test__unset()
	{
		$this
			->if($php = new testedClass())
			->and($functionName = __NAMESPACE__ . '\version_compare')
			->when(function() use ($php, $functionName) { unset($php->{$functionName}); })
			->then
				->boolean(function_exists($functionName))->isFalse()
			->and($php->{$functionName} = uniqid())
			->when(function() use ($php, $functionName) { unset($php->{$functionName}); })
			->then
				->integer(version_compare('5.4.0', '5.3.0'))->isEqualTo(1)
				->integer(version_compare('5.3.0', '5.4.0'))->isEqualTo(-1)
		;
	}

	public function testSetDefaultNamespace()
	{
		$this
			->if($php = new testedClass())
			->then
				->object($php->setDefaultNamespace($defaultNamespace = uniqid()))->isIdenticalTo($php)
				->string($php->getDefaultNamespace())->isEqualTo($defaultNamespace . '\\')
				->object($php->setDefaultNamespace($defaultNamespace = uniqid() . '\\'))->isIdenticalTo($php)
				->string($php->getDefaultNamespace())->isEqualTo($defaultNamespace)
				->object($php->setDefaultNamespace('\\' . ($defaultNamespace = uniqid())))->isIdenticalTo($php)
				->string($php->getDefaultNamespace())->isEqualTo($defaultNamespace . '\\')
				->object($php->setDefaultNamespace('\\' . ($defaultNamespace = uniqid() . '\\')))->isIdenticalTo($php)
				->string($php->getDefaultNamespace())->isEqualTo($defaultNamespace)
				->object($php->setDefaultNamespace(''))->isIdenticalTo($php)
				->string($php->getDefaultNamespace())->isEmpty()
		;
	}

	public function testUseClassNamespace()
	{
		$this
			->if($php = new testedClass())
			->then
				->object($php->useClassNamespace(__CLASS__))->isIdenticalTo($php)
				->string($php->getDefaultNamespace())->isEqualTo(__NAMESPACE__ . '\\')
		;
	}

	public function testSetAdapter()
	{
		$this
			->variable(testedClass::setAdapter($adapter = new atoum\test\adapter()))->isNull()
			->object(testedClass::getAdapter())->isIdenticalTo($adapter)
			->variable(testedClass::setAdapter())->isNull()
			->object(testedClass::getAdapter())
				->isNotIdenticalTo($adapter)
				->isEqualTo(new atoum\test\adapter())
		;
	}

	public function testGenerate()
	{
		$this
			->if($php = new testedClass())
			->then
				->object($php->generate($functionName = __NAMESPACE__ . '\version_compare'))->isIdenticalTo($php)
				->boolean(function_exists($functionName))->isTrue()
				->boolean(version_compare('5.4.0', '5.3.0'))->isFalse()
				->boolean(version_compare('5.3.0', '5.4.0'))->isTrue()
				->exception(function() use ($php) { $php->generate(__NAMESPACE__ . '\doSomething'); })
					->isInstanceof('atoum\exceptions\logic\invalidArgument')
					->hasMessage('Function \'' . __NAMESPACE__ . '\doSomething\' already exists')
			->if($php->{$functionName} = $returnValue = uniqid())
			->then
				->string(version_compare(uniqid(), uniqid()))->isEqualTo($returnValue)
				->object($php->generate($functionName = __NAMESPACE__ . '\version_compare'))->isIdenticalTo($php)
				->boolean(version_compare('5.4.0', '5.3.0'))->isFalse()
				->boolean(version_compare('5.3.0', '5.4.0'))->isTrue()
			->if($php->{$functionName} = $returnValue = uniqid())
			->when(function() use ($php, $functionName) { unset($php->{$functionName}); })
			->then
				->boolean(version_compare('5.4.0', '5.3.0'))->isFalse()
				->boolean(version_compare('5.3.0', '5.4.0'))->isTrue()
		;
	}
}
