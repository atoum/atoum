<?php

namespace mageekguy\atoum\tests\units;

use \mageekguy\atoum;

require_once(__DIR__ . '/../../runners/autorunner.php');

class score extends atoum\test
{
	public function testSetTestClass()
	{
		$score = new atoum\score();

		$this->assert
			->object($score->setTestClass($this))->isIdenticalTo($score)
			->string($score->getTestClass())->isEqualTo(__CLASS__)
			->integer($score->getOutputNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(0)
			->integer($score->getErrorNumber())->isEqualTo(0)
			->integer($score->getExceptionNumber())->isEqualTo(0)
		;
	}
}

?>
