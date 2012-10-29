<?php

namespace mageekguy\atoum\tests\units\writers\std;

use
	mageekguy\atoum,
	mageekguy\atoum\writers\std
;

require_once __DIR__ . '/../../../runner.php';

class out extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->isSubClassOf('mageekguy\atoum\writers\std')
				->implements('mageekguy\atoum\adapter\aggregator')
				->implements('mageekguy\atoum\report\writers\realtime')
				->implements('mageekguy\atoum\report\writers\asynchronous')
		;
	}

	public function test__construct()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->fopen = null)
			->and($adapter->fwrite = null)
			->and($stdout = new std\out($adapter))
			->then
				->object($stdout->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testWrite()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->fopen = null)
			->and($adapter->fwrite = null)
			->and($stdout = new std\out($adapter))
			->then
				->object($stdout->write($string = uniqid()))->isIdenticalTo($stdout)
				->adapter($adapter)->call('fwrite')->withArguments(null, $string)->once()
				->object($stdout->write($string = (uniqid() . "\n")))->isIdenticalTo($stdout)
				->adapter($adapter)->call('fwrite')->withArguments(null, $string)->once()
		;
	}
}
