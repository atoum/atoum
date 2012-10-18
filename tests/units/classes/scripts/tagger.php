<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts
;

require_once __DIR__ . '/../../runner.php';

class tagger extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\script');
	}

	public function test__construct()
	{
		$this
			->if($tagger = new scripts\tagger(uniqid()))
			->then
				->object($tagger->getEngine())->isInstanceOf('mageekguy\atoum\scripts\tagger\engine')
		;
	}

	public function testSetEngine()
	{
		$this
			->if($tagger = new scripts\tagger(uniqid()))
			->then
				->object($tagger->setEngine($engine = new scripts\tagger\engine()))->isIdenticalTo($tagger)
				->object($tagger->getEngine())->isIdenticalTo($engine)
		;
	}

	public function testRun()
	{
		$tagger = new \mock\mageekguy\atoum\scripts\tagger(uniqid());

		$tagger
			->setEngine($engine = new \mock\mageekguy\atoum\scripts\tagger\engine())
			->getMockController()->writeMessage = $tagger
		;

		$engine->getMockController()->tagVersion = function() {};

		$this->assert
			->object($tagger->run())->isIdenticalTo($tagger)
			->mock($engine)
				->call('tagVersion')->once()
		;

		$engine->getMockController()->resetCalls();

		$this->assert
			->object($tagger->run(array('-h')))->isIdenticalTo($tagger)
			->mock($tagger)
				->call('help')->atLeastOnce()
			->mock($engine)
				->call('tagVersion')->never()
		;
	}
}
