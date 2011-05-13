<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	\mageekguy\atoum,
	\mageekguy\atoum\asserter,
	\mageekguy\atoum\asserters
;

require_once(__DIR__ . '/../../runner.php');

class output extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('\mageekguy\atoum\asserters\string')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\output($generator = new asserter\generator($this));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($this->getScore())
			->object($asserter->getLocale())->isIdenticalTo($this->getLocale())
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getValue())->isNull()
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\output(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->object($asserter->setWith(function() use (& $output) { echo ($output = uniqid()); }))->isIdenticalTo($asserter)
			->string($asserter->getValue())->isEqualTo($output)
			->variable($asserter->getCharlist())->isNull()
		;

		$this->assert
			->object($asserter->setWith(function() use (& $output) { echo ($output = uniqid()); }, null, "\010"))->isIdenticalTo($asserter)
			->string($asserter->getValue())->isEqualTo($output)
			->string($asserter->getCharlist())->isEqualTo("\010")
		;
	}
}

?>
