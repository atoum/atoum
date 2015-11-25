<?php

namespace mageekguy\atoum\tests\units\php;

require_once __DIR__ . '/../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php
;

class mocker extends atoum\test
{
	public function test__construct()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->string($this->testedInstance->getDefaultNamespace())->isEmpty()
		;
	}

	public function testSetDefaultNamespace()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->setDefaultNamespace($defaultNamespace = uniqid()))->isTestedInstance
				->string($this->testedInstance->getDefaultNamespace())->isEqualTo($defaultNamespace . '\\')
				->object($this->testedInstance->setDefaultNamespace($defaultNamespace = uniqid() . '\\'))->isTestedInstance
				->string($this->testedInstance->getDefaultNamespace())->isEqualTo($defaultNamespace)
				->object($this->testedInstance->setDefaultNamespace('\\' . ($defaultNamespace = uniqid())))->isTestedInstance
				->string($this->testedInstance->getDefaultNamespace())->isEqualTo($defaultNamespace . '\\')
				->object($this->testedInstance->setDefaultNamespace('\\' . ($defaultNamespace = uniqid() . '\\')))->isTestedInstance
				->string($this->testedInstance->getDefaultNamespace())->isEqualTo($defaultNamespace)
				->object($this->testedInstance->setDefaultNamespace(''))->isTestedInstance
				->string($this->testedInstance->getDefaultNamespace())->isEmpty()
		;
	}

	public function testSetAdapter()
	{
		$this
			->variable(php\mocker::setAdapter($adapter = new atoum\php\mocker\adapter()))->isNull()
			->object(php\mocker::getAdapter())->isIdenticalTo($adapter)
			->variable(php\mocker::setAdapter())->isNull()
			->object(php\mocker::getAdapter())
				->isNotIdenticalTo($adapter)
				->isEqualTo(new atoum\php\mocker\adapter())
		;
	}
}
