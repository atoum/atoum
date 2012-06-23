<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../runner.php';

class locale extends atoum\test
{
	public function test__construct()
	{
		$locale = new atoum\locale();

		$this->assert
			->variable($locale->get())->isNull()
		;

		$locale = new atoum\locale($value = uniqid());

		$this->assert
			->string($locale->get())->isEqualTo($value)
		;
	}

	public function testSet()
	{
		$locale = new atoum\locale();

		$this->assert
			->object($locale->set($value = uniqid()))->isIdenticalTo($locale)
			->string($locale->get())->isEqualTo($value)
		;
	}

	public function test_()
	{
		$locale = new atoum\locale();
		$string = uniqid();

		$this->assert->string($locale->_($string))->isEqualTo($string);
	}

	public function test__()
	{
		$locale = new atoum\locale();
		$singular = uniqid();
		$plural = uniqid();

		$this->assert->string($locale->__($singular, $plural, - rand(1, PHP_INT_MAX)))->isEqualTo($singular);

		$this->assert->string($locale->__($singular, $plural, 1))->isEqualTo($singular);

		$this->assert->string($locale->__($singular, $plural, rand(2, PHP_INT_MAX)))->isEqualTo($plural);
	}
}
