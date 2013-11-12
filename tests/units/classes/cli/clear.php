<?php

namespace mageekguy\atoum\tests\units\cli;

require_once __DIR__ . '/../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\cli\clear as testedClass
;

class clear extends atoum
{
	public function testClass()
	{
		$this->testedClass->implements('mageekguy\atoum\writer\decorator');
	}

	public function test__construct()
	{
		$this
			->if($clear = new testedClass())
			->then
				->object($clear->getCli())->isEqualTo(new atoum\cli())
		;
	}

	public function testSetCli()
	{
		$this
			->if($clear = new testedClass())
			->then
				->object($clear->setCli($cli = new atoum\cli()))->isIdenticalTo($clear)
				->object($clear->getCli())->isIdenticalTo($cli)
				->object($clear->setCli())->isIdenticalTo($clear)
				->object($clear->getCli())
					->isNotIdenticalTo($cli)
					->isEqualTo(new atoum\cli())
		;
	}

	public function testDecorate()
	{
		$this
			->given(
				$clear = new testedClass($cli = new \mock\mageekguy\atoum\cli())
			)
			->if($this->calling($cli)->isTerminal = false)
			->then
				->string($clear->decorate(''))->isEqualTo(PHP_EOL)
				->string($clear->decorate($message = uniqid()))->isEqualTo(PHP_EOL . $message)
			->if($this->calling($cli)->isTerminal = true)
			->then
				->string($clear->decorate(''))->isEqualTo("\033[1K\r")
				->string($clear->decorate($message = uniqid()))->isEqualTo("\033[1K\r" . $message)
		;
	}
}
