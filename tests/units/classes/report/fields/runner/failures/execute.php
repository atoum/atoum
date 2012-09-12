<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\failures;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\failures\execute as testedClass
;

require_once __DIR__ . '/../../../../../runner.php';

class execute extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($field = new testedClass($command = uniqid()))
			->then
				->string($field->getCommand())->isEqualTo($command)
				->object($field->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($field->getLocale())->isInstanceOf('mageekguy\atoum\locale')
			->if($field = new testedClass($command = uniqid(), $adapter = new atoum\adapter(), $locale = new atoum\locale()))
			->then
				->string($field->getCommand())->isEqualTo($command)
				->object($field->getAdapter())->isIdenticalTo($adapter)
				->object($field->getLocale())->isIdenticalTo($locale)
		;
	}

	public function test__toString()
	{
		$this
			->if($field = new testedClass($command = uniqid(), $adapter = new atoum\test\adapter()))
			->then
				->castToString($field)->isEmpty()
				->adapter($adapter)->call('system')->never()
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
}
