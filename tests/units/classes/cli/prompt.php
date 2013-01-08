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
		$prompt = new cli\prompt();

		$this->assert
			->string($prompt->getValue())->isEmpty()
			->object($prompt->getColorizer())->isInstanceOf('mageekguy\atoum\cli\colorizer')
			->variable($prompt->getColorizer()->getForeground())->isNull()
			->variable($prompt->getColorizer()->getBackground())->isNull()
		;

		$prompt = new cli\prompt($value = uniqid());

		$this->assert
			->string($prompt->getValue())->isEqualTo($value)
			->object($prompt->getColorizer())->isInstanceOf('mageekguy\atoum\cli\colorizer')
			->variable($prompt->getColorizer()->getForeground())->isNull()
			->variable($prompt->getColorizer()->getBackground())->isNull()
		;

		$prompt = new cli\prompt($value = uniqid(), $colorizer = new cli\colorizer());

		$this->assert
			->string($prompt->getValue())->isEqualTo($value)
			->object($prompt->getColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetValue()
	{
		$prompt = new cli\prompt();

		$this->assert
			->object($prompt->setValue($value = uniqid()))->isIdenticalTo($prompt)
			->string($prompt->getValue())->isEqualTo($value)
			->object($prompt->setValue($value = rand(1, PHP_INT_MAX)))->isIdenticalTo($prompt)
			->string($prompt->getValue())->isEqualTo($value)
		;
	}

	public function testSetColorizer()
	{
		$prompt = new cli\prompt();

		$this->assert
			->object($prompt->setColorizer($colorizer = new cli\colorizer()))->isIdenticalTo($prompt)
			->object($prompt->getColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function test__toString()
	{
		$prompt = new cli\prompt();

		$colorizer = new cli\colorizer(uniqid(), uniqid());

		$this->assert
			->castToString($prompt)->isEmpty()
			->castToString($prompt->setValue($value = uniqid()))->isEqualTo($value)
			->castToString($prompt->setColorizer($colorizer))->isEqualTo($colorizer->colorize($value))
		;
	}
}
