<?php

namespace mageekguy\atoum\tests\units\writers\std;

use
	mageekguy\atoum,
	mageekguy\atoum\writers\std
;

require_once __DIR__ . '/../../../runner.php';

class err extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->isSubClassOf('mageekguy\atoum\writers\std')
				->hasInterface('mageekguy\atoum\adapter\aggregator')
				->hasInterface('mageekguy\atoum\report\writers\realtime')
				->hasInterface('mageekguy\atoum\report\writers\asynchronous')
		;
	}

	public function test__construct()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->fopen = null)
			->and($adapter->fwrite = null)
			->and($stderr = new std\err($adapter))
			->then
				->object($stderr->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testWrite()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->fopen = null)
			->and($adapter->fwrite = null)
			->and($stderr = new std\err($adapter))
			->then
				->object($stderr->write($string = uniqid()))->isIdenticalTo($stderr)
				->adapter($adapter)->call('fwrite')->withArguments(null, $string)->once()
				->object($stderr->write($string = uniqid() . "\n"))->isIdenticalTo($stderr)
				->adapter($adapter)->call('fwrite')->withArguments(null, $string)->once()
		;
	}
}
