<?php

namespace mageekguy\atoum\tests\units\script;

use
	mageekguy\atoum,
	mock\mageekguy\atoum\script\cli as testedClass
;

require_once __DIR__ . '/../../runner.php';

class cli extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->isAbstract()
			->isSubclassOf('mageekguy\atoum\script')
		;
	}

	public function test__construct()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->php_sapi_name = uniqid())
			->and($factory = new atoum\factory())
			->and($factory->import('mageekguy\atoum'))
			->and($factory['atoum\adapter'] = $adapter)
			->then
				->exception(function() use ($factory, & $name) {
						new testedClass($name = uniqid(), $factory);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('\'' . $name . '\' must be used in CLI only')
		;
	}
}
