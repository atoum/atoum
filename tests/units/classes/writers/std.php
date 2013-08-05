<?php

namespace mageekguy\atoum\tests\units\writers;

use
	mageekguy\atoum,
	mock\mageekguy\atoum\writers\std as testedClass
;

require __DIR__ . '/../../runner.php';

class std extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\writer');
	}

	public function test__construct()
	{
		$this
			->if($std = new testedClass())
			->then
				->object($std->getCli())->isEqualTo(new atoum\cli())
				->object($std->getAdapter())->isEqualTo(new atoum\adapter())
			->if($std = new testedClass($cli = new atoum\cli(), $adapter = new atoum\adapter()))
			->then
				->object($std->getCli())->isIdenticalTo($cli)
				->object($std->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($std = new testedClass())
			->then
				->object($std->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($std)
				->object($std->getAdapter())->isIdenticalTo($adapter)
				->object($std->setAdapter())->isIdenticalTo($std)
				->object($std->getAdapter())
					->isNotIdenticalTo($adapter)
					->isEqualTo(new atoum\adapter())
		;
	}

	public function testSetCli()
	{
		$this
			->if($std = new testedClass())
			->then
				->object($std->setCli($cli = new atoum\cli()))->isIdenticalTo($std)
				->object($std->getCli())->isIdenticalTo($cli)
				->object($std->setCli())->isIdenticalTo($std)
				->object($std->getCli())
					->isNotIdenticalTo($cli)
					->isEqualTo(new atoum\cli())
		;
	}

	public function testWrite()
	{
		$this
			->if($std = new testedClass($cli = new atoum\cli(), $adapter = new atoum\test\adapter()))
			->and($adapter->fwrite = function() {})
			->and($this->calling($std)->init = $std)
			->then
				->object($std->write($something = uniqid()))->isIdenticalTo($std)
				->adapter($adapter)->call('fwrite')->withArguments(null, $something)->once()
		;
	}

	public function testClear()
	{
		$this
			->if($std = new testedClass($cli = new \mock\mageekguy\atoum\cli(), $adapter = new atoum\test\adapter()))
			->and($adapter->fwrite = function() {})
			->and($this->calling($cli)->isTerminal = true)
			->and($this->calling($std)->init = $std)
			->then
				->object($std->clear())->isidenticalto($std)
				->adapter($adapter)->call('fwrite')->witharguments(null, "[1K")->once()
			->if($this->calling($cli)->isTerminal = false)
			->then
				->object($std->clear())->isidenticalto($std)
				->adapter($adapter)->call('fwrite')->witharguments(null, PHP_EOL)->once()
		;
	}
}
