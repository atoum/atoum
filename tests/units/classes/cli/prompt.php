<?php

namespace mageekguy\atoum\tests\units\cli;

use
	mageekguy\atoum,
	mageekguy\atoum\cli
;

require_once __DIR__ . '/../../runner.php';

class prompt extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($prompt = new cli\prompt())
			->then
				->string($prompt->getValue())->isEmpty()
				->object($prompt->getColorizer())->isInstanceOf('mageekguy\atoum\cli\colorizer')
				->variable($prompt->getColorizer()->getForeground())->isNull()
				->variable($prompt->getColorizer()->getBackground())->isNull()
			->if($prompt = new cli\prompt($value = uniqid()))
			->then
				->string($prompt->getValue())->isEqualTo($value)
				->object($prompt->getColorizer())->isInstanceOf('mageekguy\atoum\cli\colorizer')
				->variable($prompt->getColorizer()->getForeground())->isNull()
				->variable($prompt->getColorizer()->getBackground())->isNull()
			->if($prompt = new cli\prompt($value = uniqid(), $colorizer = new cli\colorizer()))
			->then
				->string($prompt->getValue())->isEqualTo($value)
				->object($prompt->getColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetValue()
	{
		$this
			->if($prompt = new cli\prompt())
			->then
				->object($prompt->setValue($value = uniqid()))->isIdenticalTo($prompt)
				->string($prompt->getValue())->isEqualTo($value)
				->object($prompt->setValue($value = rand(1, PHP_INT_MAX)))->isIdenticalTo($prompt)
				->string($prompt->getValue())->isEqualTo($value)
		;
	}

	public function testSetColorizer()
	{
		$this
			->if($prompt = new cli\prompt())
			->then
				->object($prompt->setColorizer($colorizer = new cli\colorizer()))->isIdenticalTo($prompt)
				->object($prompt->getColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function __toString()
	{
		$this
			->if($prompt = new cli\prompt())
			->and($colorizer = new cli\colorizer(uniqid(), uniqid()))
			->then
				->castToString($prompt)->isEmpty()
				->castToString($prompt->setValue($value = uniqid())->isEqualTo($value))
				->castToString($prompt->setColorizer($colorizer)->isEqualTo($colorizer->colorize($value)))
		;

		return '';
	}
}
