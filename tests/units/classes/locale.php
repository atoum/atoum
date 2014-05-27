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
			->given($this->newTestedInstance())
			->then
				->variable($this->testedInstance->get())->isNull()
				->castToString($this->testedInstance)->isEqualTo('unknown')

			->given($this->newTestedInstance($value = uniqid()))
			->then
				->string($this->testedInstance->get())->isEqualTo($value)
				->castToString($this->testedInstance)->isEqualTo($value)
		;
	}

	public function testSet()
	{
		$this
			->given($this->newTestedInstance())
			->then
				->object($this->testedInstance->set($locale = uniqid()))->isTestedInstance
				->string($this->testedInstance->get())->isEqualTo($locale)
		;
	}

	public function test_()
	{
		$this
			->given($this->newTestedInstance())
			->then
				->string($this->testedInstance->_($string = uniqid()))->isEqualTo($string)
				->string($this->testedInstance->_($string = '%s %s %s', $one = uniqid(), $two = uniqid(), $three = uniqid()))->isEqualTo(sprintf($string, $one, $two, $three))
		;
	}

	public function test__()
	{
		$this
			->given($this->newTestedInstance())
			->then
				->string($this->testedInstance->__($singular = 'Singular %s %s %s', $plural = 'Plural %s %s %s', - rand(1, PHP_INT_MAX)))->isEqualTo($singular)
				->string($this->testedInstance->__($singular, $plural, - rand(1, PHP_INT_MAX)))->isEqualTo($singular)
				->string($this->testedInstance->__($singular, $plural, 1))->isEqualTo($singular)
				->string($this->testedInstance->__($singular, $plural, 1, $one = uniqid(), $two = uniqid(), $three = uniqid()))->isEqualTo(sprintf($singular, $one, $two, $three))
				->string($this->testedInstance->__($singular, $plural, rand(2, PHP_INT_MAX)))->isEqualTo($plural)
				->string($this->testedInstance->__($singular, $plural, rand(2, PHP_INT_MAX), $one = uniqid(), $two = uniqid(), $three = uniqid()))->isEqualTo(sprintf($plural, $one, $two, $three))
		;
	}
}
