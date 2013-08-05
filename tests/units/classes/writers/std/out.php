<?php

namespace mageekguy\atoum\tests\units\writers\std;

use
	mageekguy\atoum,
	mageekguy\atoum\writers\std\out as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class out extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->extends('mageekguy\atoum\writers\std')
				->implements('mageekguy\atoum\report\writers\realtime')
				->implements('mageekguy\atoum\report\writers\asynchronous')
		;
	}

	public function test__construct()
	{
		$this
			->if($stdout = new testedClass())
			->then
				->object($stdout->getCli())->isEqualTo(new atoum\cli())
				->object($stdout->getAdapter())->isEqualTo(new atoum\adapter())
			->if($cli = new atoum\cli())
			->and($adapter = new atoum\test\adapter())
			->and($adapter->fopen = null)
			->and($adapter->fwrite = null)
			->and($stdout = new testedClass($cli, $adapter))
			->then
				->object($stdout->getCli())->isIdenticalTo($cli)
				->object($stdout->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testWrite()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->fopen = null)
			->and($adapter->fwrite = null)
			->and($stdout = new testedClass(null, $adapter))
			->then
				->object($stdout->write($string = uniqid()))->isIdenticalTo($stdout)
				->adapter($adapter)->call('fwrite')->withArguments(null, $string)->once()
				->object($stdout->write($string = (uniqid() . "\n")))->isIdenticalTo($stdout)
				->adapter($adapter)->call('fwrite')->withArguments(null, $string)->once()
		;
	}
}
