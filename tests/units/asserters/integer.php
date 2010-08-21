<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserters;

require_once(__DIR__ . '/../../../runners/autorunner.php');

class integer extends atoum\test
{
	public function test__construct()
	{
		$score = new atoum\score($this);
		$locale = new atoum\locale();

		$asserter = new asserters\integer($score, $locale);

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\integer(new atoum\score($this), new atoum\locale());

		$variable = uniqid();

		$this->assert
			->object($asserter->setWith($variable))->isIdenticalTo($asserter)
			->string($asserter->getInteger())->isEqualTo($variable)
			->object($asserter->setWith($this))->isIdenticalTo($asserter)
			->object($asserter->getInteger())->isIdenticalTo($this)
		;
	}

	public function testIsEqualTo()
	{
		$score = new atoum\score($this);

		$asserter = new asserters\integer($score, new atoum\locale());

		$variable = uniqid();

		$asserter->setWith($variable);

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->isEqualTo($variable))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
			->object($asserter->isEqualTo(uniqid()))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$asserter->setWith(1);

		$asserter->setWith($variable);

		$this->assert
			->object($asserter->isEqualTo('1'))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(1)
			->object($asserter->isEqualTo('0'))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}

	public function testIsNotEqualTo()
	{
		$score = new atoum\score($this);

		$asserter = new asserters\integer($score, new atoum\locale());

		$variable = uniqid();

		$asserter->setWith($variable);

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->object($asserter->isNotEqualTo(uniqid()))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
			->object($asserter->isNotEqualTo($variable))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;
	}
}

?>
