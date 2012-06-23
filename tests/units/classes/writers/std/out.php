<?php

namespace atoum\tests\units\writers\std;

use
	atoum,
	atoum\writers\std
;

require_once __DIR__ . '/../../../runner.php';

class out extends atoum\test
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

		$stdout = new std\out($adapter);

		$this->assert
			->object($stdout->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testWrite()
	{
		$adapter = new atoum\test\adapter();
		$adapter->fopen = null;
		$adapter->fwrite = null;

		$stdout = new std\out($adapter);

		$this->assert
			->object($stdout->write($string = uniqid()))->isIdenticalTo($stdout)
			->adapter($adapter)->call('fwrite')->withArguments(null, $string)->once()
			->object($stdout->write($string = (uniqid() . "\n")))->isIdenticalTo($stdout)
			->adapter($adapter)->call('fwrite')->withArguments(null, $string)->once()
		;
	}
}
