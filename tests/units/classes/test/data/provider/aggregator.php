<?php

namespace mageekguy\atoum\tests\units\test\data\provider;

use mageekguy\atoum;

require_once __DIR__ . '/../../../runner.php';

class aggregator extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass->implements('mageekguy\atoum\test\data\provider');
	}

	public function testGenerate(atoum\test\data\provider $dataProvider, atoum\test\data\provider $otherDataProvider)
	{
		$this
			->given($this->newTestedInstance)
			->then
				->array($this->testedInstance->generate())->isEmpty
			->if(
				$this->calling($dataProvider)->generate = $value = uniqid(),
				$this->testedInstance->addProvider($dataProvider)
			)
			->then
				->array($this->testedInstance->generate())
					->string[0]->isEqualTo($value)
			->if(
				$this->calling($otherDataProvider)->generate = $otherValue = uniqid(),
				$this->testedInstance->addProvider($otherDataProvider)
			)
			->then
				->array($this->testedInstance->generate())
					->string[0]->isEqualTo($value)
					->string[1]->isEqualTo($otherValue)
		;
	}

	public function testAddProvider(atoum\test\data\provider $dataProvider, atoum\test\data\provider $otherDataProvider)
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->addProvider($dataProvider))->isTestedInstance
				->sizeOf($this->testedInstance)->isEqualTo(1)
			->if($this->testedInstance->addProvider($dataProvider))
			->then
				->sizeOf($this->testedInstance)->isEqualTo(2)
			->if($this->testedInstance->addProvider($otherDataProvider))
			->then
				->sizeOf($this->testedInstance)->isEqualTo(3)
		;
	}
}
