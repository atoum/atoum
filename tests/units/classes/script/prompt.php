<?php

namespace mageekguy\atoum\tests\units\script;

use
	mageekguy\atoum,
	mageekguy\atoum\script\prompt as testedClass
;

require_once __DIR__ . '/../../runner.php';

class prompt extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($prompt = new testedClass())
			->then
				->object($prompt->getInputReader())->isEqualTo(new atoum\readers\std\in())
				->object($prompt->getOutputWriter())->isEqualTo(new atoum\writers\std\out())
		;
	}

	public function testSetInputReader()
	{
		$this
			->if($prompt = new testedClass())
			->then
				->object($prompt->setInputReader($reader = new atoum\readers\std\in()))->isIdenticalTo($prompt)
				->object($prompt->getInputReader())->isIdenticalTo($reader)
				->object($prompt->setInputReader())->isIdenticalTo($prompt)
				->object($prompt->getInputReader())
					->isNotIdenticalTo($reader)
					->isEqualTo(new atoum\readers\std\in())
		;
	}

	public function testSetOutputWriter()
	{
		$this
			->if($prompt = new testedClass())
			->then
				->object($prompt->setOutputWriter($writer = new atoum\writers\std\out()))->isIdenticalTo($prompt)
				->object($prompt->getOutputWriter())->isIdenticalTo($writer)
				->object($prompt->setOutputWriter())->isIdenticalTo($prompt)
				->object($prompt->getOutputWriter())
					->isNotIdenticalTo($writer)
					->isEqualTo(new atoum\writers\std\out())
		;
	}

	public function testAsk()
	{
		$this
			->if($prompt = new testedClass())
			->and($writer = new \mock\atoum\writer())
			->and($reader = new \mock\atoum\reader())
			->and($prompt->setOutputWriter($writer))
			->and($prompt->setInputReader($reader))
			->and($this->calling($reader)->read = $line = uniqid())
			->then
				->string($prompt->ask($question = uniqid()))->isEqualTo($line)
				->mock($writer)->call('write')->withArguments($question)->once()
				->string($prompt->ask($question = uniqid()))->isEqualTo($line)
		;
	}
}
