<?php

namespace mageekguy\atoum\tests\units\test\engines;

require_once __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\test\engines
;

class inline extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\test\engine');
	}

	public function testIsAsynchronous()
	{
		$this
			->if($engine = new engines\inline())
			->then
				->boolean($engine->isAsynchronous())->isFalse()
		;
	}

	public function testRun()
	{
		$this
			->if($engine = new engines\inline())
			->then
				->object($engine->run($test = new \mock\mageekguy\atoum\test()))->isIdenticalTo($engine)
			->if($test->getMockController()->getCurrentMethod = $method = uniqid())
			->and($test->getMockController()->runTestMethod = $test)
			->then
				->object($engine->run($test))->isIdenticalTo($engine)
				->mock($test)
					->call('getScore')->beforeMethodCall('runTestMethod')->once()
					->call('setScore')->withIdenticalArguments($engine->getScore())->beforeMethodCall('runTestMethod')->once()
					->call('runTestMethod')->withArguments($method)->once()
					->call('setScore')->withIdenticalArguments($test->getScore())->afterMethodCall('runTestMethod')->once()
		;
	}

	public function testGetScore()
	{
		$this
			->if($engine = new engines\inline())
			->then
				->object($engine->getScore())->isInstanceof('mageekguy\atoum\score')
			->if($test = new \mock\mageekguy\atoum\test())
			->and($test->getMockController()->getCurrentMethod = $method = uniqid())
			->and($test->getMockController()->runTestMethod = $test)
			->and($engine->run($test))
			->then
				->object($engine->getScore())->isInstanceOf('mageekguy\atoum\score')
		;
	}
}
