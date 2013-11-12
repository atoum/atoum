<?php

namespace mageekguy\atoum\tests\units\writer\decorators;

require_once __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\writer\decorators\prompt as testedClass
;

class prompt extends atoum
{
	public function testClass()
	{
		$this->testedClass->implements('mageekguy\atoum\writer\decorator');
	}

	public function testClassConstants()
	{
		$this->string(testedClass::defaultPrompt)->isEqualTo('$ ');
	}

	public function test__construct()
	{
		$this
			->if($decorator = new testedClass())
			->then
				->string($decorator->getPrompt())->isEqualTo(testedClass::defaultPrompt)
			->if($decorator = new testedClass($prompt = uniqid()))
			->then
				->string($decorator->getPrompt())->isEqualTo($prompt)
		;
	}

	public function testSetPrompt()
	{
		$this
			->if($decorator = new testedClass())
			->then
				->object($decorator->setPrompt($prompt = uniqid()))->isIdenticalTo($decorator)
				->string($decorator->getPrompt())->isEqualTo($prompt)
				->object($decorator->setPrompt())->isIdenticalTo($decorator)
				->string($decorator->getPrompt())->isEqualTo(testedClass::defaultPrompt)
		;
	}

	public function testDecorate()
	{
		$this
			->if($decorator = new testedClass())
			->then
				->string($decorator->decorate($message = uniqid()))->isEqualTo($decorator->getPrompt() . $message)
				->string($decorator->decorate($message = uniqid() . PHP_EOL))->isEqualTo($decorator->getPrompt() . $message)
				->string($decorator->decorate($message = ' ' . uniqid() . PHP_EOL . PHP_EOL))->isEqualTo($decorator->getPrompt() . $message)
		;
	}
}
