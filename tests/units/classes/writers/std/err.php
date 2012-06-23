<?php

namespace atoum\tests\units\writers\std;

use
	atoum,
	atoum\writers\std
;

require_once __DIR__ . '/../../../runner.php';

class err extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubClassOf('atoum\writers\std')
				->hasInterface('atoum\adapter\aggregator')
				->hasInterface('atoum\report\writers\realtime')
				->hasInterface('atoum\report\writers\asynchronous')
		;
	}

	public function test__construct()
	{
		$adapter = new atoum\test\adapter();
		$adapter->fopen = null;
		$adapter->fwrite = null;

		$stderr = new std\err($adapter);

		$this->assert
			->object($stderr->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testWrite()
	{
		$adapter = new atoum\test\adapter();
		$adapter->fopen = null;
		$adapter->fwrite = null;

		$stderr = new std\err($adapter);

		$this->assert
			->object($stderr->write($string = uniqid()))->isIdenticalTo($stderr)
			->adapter($adapter)->call('fwrite')->withArguments(null, $string)->once()
			->object($stderr->write($string = uniqid() . "\n"))->isIdenticalTo($stderr)
			->adapter($adapter)->call('fwrite')->withArguments(null, $string)->once()
		;
	}
}
