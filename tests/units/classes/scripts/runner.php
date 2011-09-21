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
			->variable($runner->getScoreFile())->isNull()
			->array($runner->getArguments())->isEmpty()
		;
	}

	public function testSetArguments()
	{
		$runner = new scripts\runner($name = uniqid());

		$this->assert
			->object($runner->setArguments(array()))->isIdenticalTo($runner)
			->array($runner->getArguments())->isEmpty()
			->object($runner->setArguments($arguments = array(uniqid(), uniqid(), uniqid())))->isIdenticalTo($runner)
			->array($runner->getArguments())->isEqualTo($arguments)
		;
	}
}

?>
