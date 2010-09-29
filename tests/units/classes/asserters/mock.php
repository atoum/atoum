<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserters;

require_once(__DIR__ . '/../../runner.php');

class mock extends atoum\test
{
	public function test__construct()
	{
		$score = new atoum\score();
		$locale = new atoum\locale();

		$asserter = new asserters\mock($score, $locale);

		$this->assert
			->object($asserter)->isInstanceOf('\mageekguy\atoum\asserter')
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\mock($score = new atoum\score(), $locale = new atoum\locale());

		$mockGenerator = new atoum\mock\generator();
		$mockGenerator->generate(__CLASS__);

		$mock = uniqid();

		$this->assert
			->exception(function() use ($asserter, $mock) {
						$asserter->setWith($mock);
					}
				)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not a mock'), $mock))
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isZero()
		;

		$mock = new atoum\mock\mageekguy\atoum\tests\units\asserters\mock();

		$this->assert
			->object($asserter->setWith($mock))->isIdenticalTo($asserter)
			->object($asserter->getMock())->isIdenticalTo($mock)
		;
	}

	public function testWasCalled()
	{
		$asserter = new asserters\mock($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->wasCalled();
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Mock is undefined')
		;

	}
}

?>
