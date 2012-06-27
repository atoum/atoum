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
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\script')
		;
	}

	public function test__construct()
	{
		$tagger = new scripts\tagger(uniqid());

		$this->assert
			->object($tagger->getEngine())->isInstanceOf('mageekguy\atoum\scripts\tagger\engine')
			->object($tagger->getEngine()->getAdapter())->isIdenticalTo($tagger->getAdapter())
		;
	}

	public function testSetEngine()
	{
		$tagger = new scripts\tagger(uniqid());

		$this->assert
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
