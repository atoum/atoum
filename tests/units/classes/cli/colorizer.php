<?php

namespace mageekguy\atoum\tests\units\cli;

use
	mageekguy\atoum,
	mageekguy\atoum\cli
;

require_once __DIR__ . '/../../runner.php';

class colorizer extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($colorizer = new cli\colorizer())
			->then
				->variable($colorizer->getForeground())->isNull()
				->variable($colorizer->getBackground())->isNull()
				->object($colorizer->getCli())->isEqualTo(new atoum\cli())
			->if($colorizer = new cli\colorizer($foreground = uniqid()))
			->then
				->string($colorizer->getForeground())->isEqualTo($foreground)
				->variable($colorizer->getBackground())->isNull()
				->object($colorizer->getCli())->isEqualTo(new atoum\cli())
			->if($colorizer = new cli\colorizer($foreground = rand(1, PHP_INT_MAX)))
			->then
				->string($colorizer->getForeground())->isEqualTo($foreground)
				->variable($colorizer->getBackground())->isNull()
				->object($colorizer->getCli())->isEqualTo(new atoum\cli())
			->if($colorizer = new cli\colorizer($foreground = uniqid(), $background = uniqid()))
			->then
				->string($colorizer->getForeground())->isEqualTo($foreground)
				->string($colorizer->getBackground())->isEqualTo($background)
				->object($colorizer->getCli())->isEqualTo(new atoum\cli())
			->if($colorizer = new cli\colorizer($foreground = uniqid(), $background = rand(1, PHP_INT_MAX)))
			->then
				->string($colorizer->getForeground())->isEqualTo($foreground)
				->string($colorizer->getBackground())->isEqualTo($background)
				->object($colorizer->getCli())->isEqualTo(new atoum\cli())
			->if($colorizer = new cli\colorizer($foreground = uniqid(), $background = rand(1, PHP_INT_MAX), $cli = new atoum\cli()))
			->then
				->string($colorizer->getForeground())->isEqualTo($foreground)
				->string($colorizer->getBackground())->isEqualTo($background)
				->object($colorizer->getCli())->isIdenticalTo($cli)
		;
	}

	public function testSetCli()
	{
		$this
			->if($colorizer = new cli\colorizer(uniqid()))
			->then
				->object($colorizer->setCli($cli = new atoum\cli()))->isIdenticalTo($colorizer)
				->object($colorizer->getCli())->isIdenticalTo($cli)
		;
	}

	public function testSetForeground()
	{
		$this
			->if($colorizer = new cli\colorizer(uniqid()))
			->then
				->object($colorizer->setForeground($foreground = uniqid()))->isIdenticalTo($colorizer)
				->string($colorizer->getForeground())->isEqualTo($foreground)
				->object($colorizer->setForeground($foreground = rand(1, PHP_INT_MAX)))->isIdenticalTo($colorizer)
				->string($colorizer->getForeground())->isEqualTo($foreground)
		;
	}

	public function testSetBackground()
	{
		$this
			->if($colorizer = new cli\colorizer(uniqid()))
			->then
				->object($colorizer->setBackground($foreground = uniqid()))->isIdenticalTo($colorizer)
				->string($colorizer->getBackground())->isEqualTo($foreground)
				->object($colorizer->setBackground($foreground = rand(1, PHP_INT_MAX)))->isIdenticalTo($colorizer)
				->string($colorizer->getBackground())->isEqualTo($foreground)
		;
	}

	public function testColorize()
	{
		$this
			->if($colorizer = new cli\colorizer(null, null, $cli = new \mock\mageekguy\atoum\cli()))
			->and($cli->getMockController()->isTerminal = true)
			->then
				->string($colorizer->colorize($string = uniqid()))->isEqualTo($string)
			->if($colorizer = new cli\colorizer($foreground = uniqid(), null, $cli))
			->then
				->string($colorizer->colorize($string = uniqid()))->isEqualTo("\033[" . $foreground . 'm' . $string . "\033[0m")
			->if($colorizer = new cli\colorizer($foreground = uniqid(), $background = uniqid(), $cli))
			->then
				->string($colorizer->colorize($string = uniqid()))->isEqualTo("\033[" . $foreground . 'm' . "\033[" . $background . 'm' . $string . "\033[0m")
			->if($colorizer = new cli\colorizer(null, $background = uniqid(), $cli))
			->then
				->string($colorizer->colorize($string = uniqid()))->isEqualTo("\033[" . $background . 'm' . $string . "\033[0m")
			->if($colorizer = new cli\colorizer($foreground = uniqid(), $background = uniqid(), $cli))
			->and($cli->getMockController()->isTerminal = false)
			->then
				->string($colorizer->colorize($string = uniqid()))->isEqualTo($string)
		;
	}
}
