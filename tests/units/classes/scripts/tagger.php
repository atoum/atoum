<?php

namespace atoum\tests\units\scripts;

use
	atoum,
	atoum\scripts,
	mock\atoum as mock
;

require_once __DIR__ . '/../../runner.php';

class tagger extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('atoum\script');
	}

	public function test__construct()
	{
		$this
			->if($tagger = new scripts\tagger(uniqid()))
			->then
				->object($tagger->getEngine())->isInstanceOf('atoum\scripts\tagger\engine')
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
		$this
			->if($helpWriter = new mock\writers\std\out())
			->and($this->calling($helpWriter)->write = function() {})
			->and($tagger = new \mock\atoum\scripts\tagger(uniqid()))
			->and($tagger->setEngine($engine = new \mock\atoum\scripts\tagger\engine()))
			->and($tagger->setHelpWriter($helpWriter))
			->and($this->calling($engine)->tagVersion = function() {})
			->then
				->object($tagger->run())->isIdenticalTo($tagger)
				->mock($engine)->call('tagVersion')->once()
			->if($engine->getMockController()->resetCalls())
			->then
				->object($tagger->run(array('-h')))->isIdenticalTo($tagger)
				->mock($tagger)->call('help')->atLeastOnce()
				->mock($engine)->call('tagVersion')->never()
		;
	}
}
