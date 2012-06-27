<?php

namespace mageekguy\atoum\tests\units\test;

require_once __DIR__ . '/../../runner.php';

use
	mageekguy\atoum
;

class engine extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isAbstract();
	}

	public function test__construct()
	{
		$this
			->if($engine = new \mock\mageekguy\atoum\test\engine())
			->then
				->object($engine->getFactory())->isEqualTo(new atoum\factory())
			->if($engine = new \mock\mageekguy\atoum\test\engine($factory = new atoum\factory()))
			->then
				->object($engine->getFactory())->isIdenticalTo($factory)
		;
	}

	public function testSetFactory()
	{
		$this
			->if($engine = new \mock\mageekguy\atoum\test\engine())
			->then
				->object($engine->setFactory($factory = new atoum\factory()))->isIdenticalTo($engine)
				->object($engine->getFactory())->isIdenticalTo($factory)
		;
	}
}
