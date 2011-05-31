<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	\mageekguy\atoum,
	\mageekguy\atoum\asserter,
	\mageekguy\atoum\asserters,
	\mageekguy\atoum\tools\diffs
;

require_once(__DIR__ . '/../../runner.php');

class sizeOf extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('\mageekguy\atoum\asserters\integer')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\sizeOf($generator = new asserter\generator($this));

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
		$asserter = new asserters\sizeOf(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->object($asserter->setWith(array()))->isIdenticalTo($asserter)
			->boolean($asserter->wasSet())->isTrue()
			->integer($score->getFailNumber())->isZero()
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($asserter->getValue())->isZero()
			->object($asserter->setWith($countable = range(1, rand(2, 5))))->isIdenticalTo($asserter)
			->boolean($asserter->wasSet())->isTrue()
			->integer($score->getFailNumber())->isZero()
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($asserter->getValue())->isEqualTo(sizeof($countable))
		;
	}
}

?>
