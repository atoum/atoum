<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserters;

require_once(__DIR__ . '/../../../runners/autorunner.php');

/**
@isolation off
*/
class output extends atoum\test
{
	public function test__construct()
	{
		$asserter = new asserters\output($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->object($asserter)->isInstanceOf('\mageekguy\atoum\asserters\string')
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\output(new atoum\score(), new atoum\locale());

		$output = uniqid();

		$this->assert
			->object($asserter->setWith(function() use ($output) { echo $output; }))->isIdenticalTo($asserter)
			->string($asserter->getVariable())->isEqualTo($output)
			->variable($asserter->getCharlist())->isNull()
		;

		$output = uniqid();

		$this->assert
			->object($asserter->setWith(function() use ($output) { echo $output; }, "\010"))->isIdenticalTo($asserter)
			->string($asserter->getVariable())->isEqualTo($output)
			->string($asserter->getCharlist())->isEqualTo("\010")
		;
	}
}

?>
