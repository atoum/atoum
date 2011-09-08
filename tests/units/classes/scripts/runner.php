<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\scripts,
	mageekguy\atoum\scripts\builder\vcs
;

require_once __DIR__ . '/../../runner.php';

class runner extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\script')
			->string(scripts\builder::defaultUnitTestRunnerScript)->isEqualTo('scripts/runner.php')
			->string(scripts\builder::defaultPharGeneratorScript)->isEqualTo('scripts/phar/generator.php')
		;
	}

	public function test__construct()
	{
		$runner = new scripts\runner($name = uniqid());

		$this->assert
			->string($runner->getName())->isEqualTo($name)
			->object($runner->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			->boolean(isset($runner->getAdapter()->exit))->isTrue()
			->object($runner->getLocale())->isEqualTo(new atoum\locale())
			->object($runner->getRunner())->isEqualTo(new atoum\runner())
		;
	}

	public function testGetPhpConfiguration()
	{
		$runner = new scripts\runner($name = uniqid());

		$this->assert
			->array($runner->getPhpConfiguration())->isEmpty()
		;
	}
}

?>
