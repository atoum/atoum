<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\scripts
;

require_once(__DIR__ . '/../../runner.php');

class tagger extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('\mageekguy\atoum\script')
		;
	}

	public function test__construct()
	{
		$tagger = new scripts\tagger(uniqid());

		$this->assert
			->object($tagger->getTagger())->isInstanceOf('\mageekguy\atoum\tagger')
			->object($tagger->getTagger()->getAdapter())->isIdenticalTo($tagger->getAdapter())
		;
	}

	public function testSetTagger()
	{
		$tagger = new scripts\tagger(uniqid());

		$this->assert
			->object($tagger->setTagger($internalTagger = new atoum\tagger()))->isIdenticalTo($tagger)
			->object($tagger->getTagger())->isIdenticalTo($internalTagger)
		;
	}

	public function testRun()
	{
		$this
			->mock('\mageekguy\atoum\tagger')
			->mock('\mageekguy\atoum\scripts\tagger')
		;

		$tagger = new mock\mageekguy\atoum\scripts\tagger(uniqid());

		$tagger
			->setTagger($internalTagger = new mock\mageekguy\atoum\tagger())
			->getMockController()->writeMessage = $tagger
		;

		$internalTagger->getMockController()->tagVersion = function() {};

		$this->assert
			->object($tagger->run())->isIdenticalTo($tagger)
			->mock($internalTagger)
				->call('tagVersion')
		;

		$internalTagger->getMockController()->resetCalls();

		$this->assert
			->object($tagger->run(array('-h')))->isIdenticalTo($tagger)
			->mock($tagger)
				->call('help')
			->mock($internalTagger)
				->notCall('tagVersion')
		;
	}
}

?>
