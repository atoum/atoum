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
			->if($this->newTestedInstance)
			->then
				->string($this->testedInstance->getValue())->isEmpty()
				->object($this->testedInstance->getColorizer())->isInstanceOf('mageekguy\atoum\cli\colorizer')
				->variable($this->testedInstance->getColorizer()->getForeground())->isNull()
				->variable($this->testedInstance->getColorizer()->getBackground())->isNull()
			->if($this->newTestedInstance($value = uniqid()))
			->then
				->string($this->testedInstance->getValue())->isEqualTo($value)
				->object($this->testedInstance->getColorizer())->isInstanceOf('mageekguy\atoum\cli\colorizer')
				->variable($this->testedInstance->getColorizer()->getForeground())->isNull()
				->variable($this->testedInstance->getColorizer()->getBackground())->isNull()
			->if($this->newTestedInstance($value = uniqid(), $colorizer = new cli\colorizer()))
			->then
				->string($this->testedInstance->getValue())->isEqualTo($value)
				->object($this->testedInstance->getColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetValue()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setValue($value = uniqid()))->isTestedInstance
				->string($this->testedInstance->getValue())->isEqualTo($value)
				->object($this->testedInstance->setValue($value = rand(1, PHP_INT_MAX)))->isTestedInstance
				->string($this->testedInstance->getValue())->isEqualTo($value)
		;
	}

	public function testSetColorizer()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setColorizer($colorizer = new cli\colorizer()))->isTestedInstance
				->object($this->testedInstance->getColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function test__toString()
	{
		$this
			->if(
				$this->newTestedInstance,
				$colorizer = new cli\colorizer(uniqid(), uniqid())
			)
			->then
				->castToString($this->testedInstance)->isEmpty()
				->castToString($this->testedInstance->setValue($value = uniqid()))->isEqualTo($value)
				->castToString($this->testedInstance->setColorizer($colorizer))->isEqualTo($colorizer->colorize($value))
		;
	}
}
