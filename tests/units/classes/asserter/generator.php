<?php

namespace mageekguy\atoum\tests\units\asserter;

use mageekguy\atoum;
use mageekguy\atoum\asserter;

require_once(__DIR__ . '/../../runner.php');

class generator extends atoum\test
{
	 public function beforeTestMethod()
	 {
		 $this->assert->setAlias('array', 'collection');
	 }

	public function test__construct()
	{
		$generator = new asserter\generator($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->object($generator->getScore())->isIdenticalTo($score)
			->object($generator->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetScore()
	{
		$generator = new asserter\generator(new atoum\score(), new atoum\locale());

		$this->assert
			->object($generator->setScore($score = new atoum\score()))->isIdenticalTo($generator)
			->object($generator->getScore())->isIdenticalTo($score)
		;
	}

	public function testSetLocale()
	{
		$generator = new asserter\generator(new atoum\score(), new atoum\locale());

		$this->assert
			->object($generator->setLocale($locale = new atoum\locale()))->isIdenticalTo($generator)
			->object($generator->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetAlias()
	{
		$generator = new asserter\generator($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->object($generator->setAlias($alias = uniqid(), $asserter = uniqid()))->isIdenticalTo($generator)
			->array($generator->getAliases())->isEqualTo(array($alias => $asserter))
		;
	}

	public function testResetAliases()
	{
		$generator = new asserter\generator($score = new atoum\score(), $locale = new atoum\locale());

		$generator->setAlias(uniqid(), uniqid());

		$this->assert
			->array($generator->getAliases())->isNotEmpty()
			->object($generator->resetAliases())->isIdenticalTo($generator)
			->array($generator->getAliases())->isEmpty()
		;
	}

	public function testGetAsserterClass()
	{
		$generator = new asserter\generator($score = new atoum\score(), $locale = new atoum\locale());
	}

	public function test__call()
	{
		$generator = new asserter\generator($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->exception(function() use ($generator, & $asserter) {
					$generator->{$asserter = uniqid()}();
				}
			)
			->isInstanceOf('\logicException')
			->hasMessage('Asserter \'mageekguy\atoum\asserters\\' . $asserter . '\' does not exist')
		;

		$this->assert
			->object($generator->variable(uniqid()))->isInstanceOf('mageekguy\atoum\asserters\variable')
		;
	}
}

?>
