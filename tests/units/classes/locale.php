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
		$this
			->if($locale = new atoum\locale())
			->then
				->variable($locale->get())->isNull()
			->if($locale = new atoum\locale($value = uniqid()))
			->then
				->string($locale->get())->isEqualTo($value)
		;
	}

	public function testSet()
	{
		$this
			->if($locale = new atoum\locale())
			->then
				->object($locale->set($value = uniqid()))->isIdenticalTo($locale)
				->string($locale->get())->isEqualTo($value)
		;
	}

	public function test_()
	{
		$this
			->if($locale = new atoum\locale())
			->and($string = uniqid())
			->then
				->string($locale->_($string))->isEqualTo($string)
		;
	}

	public function test__()
	{
		$this
			->if($locale = new atoum\locale())
			->and($singular = uniqid())
			->and($plural = uniqid())
			->then
				->string($locale->__($singular, $plural, - rand(1, PHP_INT_MAX)))->isEqualTo($singular)
				->string($locale->__($singular, $plural, 1))->isEqualTo($singular)
				->string($locale->__($singular, $plural, rand(2, PHP_INT_MAX)))->isEqualTo($plural)
		;
	}
}
