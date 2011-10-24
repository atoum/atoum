<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../runner.php';

class script extends atoum\test
{
	public function test__construct()
	{
		$this->mock('mageekguy\atoum\script');

		$script = new \mock\mageekguy\atoum\script($name = uniqid());

		$this->assert
			->string($script->getName())->isEqualTo($name)
			->object($script->getLocale())->isEqualTo(new atoum\locale())
			->object($script->getAdapter())->isEqualTo(new atoum\adapter())
			->object($script->getArgumentsParser())->isEqualTo(new atoum\script\arguments\parser())
			->object($script->getOutputWriter())->isEqualTo(new atoum\writers\std\out())
			->object($script->getErrorWriter())->isEqualTo(new atoum\writers\std\err())
		;
	}
}

?>
