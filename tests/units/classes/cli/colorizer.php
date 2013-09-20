<?php

namespace mageekguy\atoum\tests\units\cli;

use
	mageekguy\atoum,
	mageekguy\atoum\cli
;

require_once __DIR__ . '/../../runner.php';

class colorizer extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->implements('mageekguy\atoum\writer\decorator');
	}

	public function test__construct()
	{
		$this
			->if($colorizer = new cli\colorizer())
			->then
				->variable($colorizer->getForeground())->isNull()
				->variable($colorizer->getBackground())->isNull()
				->object($colorizer->getCli())->isEqualTo(new atoum\cli())
				->variable($colorizer->getPattern())->isNull()
			->if($colorizer = new cli\colorizer($foreground = uniqid()))
			->then
				->string($colorizer->getForeground())->isEqualTo($foreground)
				->variable($colorizer->getBackground())->isNull()
				->object($colorizer->getCli())->isEqualTo(new atoum\cli())
				->variable($colorizer->getPattern())->isNull()
				->variable($colorizer->getPattern())->isNull()
			->if($colorizer = new cli\colorizer($foreground = rand(1, PHP_INT_MAX)))
			->then
				->string($colorizer->getForeground())->isEqualTo($foreground)
				->variable($colorizer->getBackground())->isNull()
				->object($colorizer->getCli())->isEqualTo(new atoum\cli())
				->variable($colorizer->getPattern())->isNull()
			->if($colorizer = new cli\colorizer($foreground = uniqid(), $background = uniqid()))
			->then
				->string($colorizer->getForeground())->isEqualTo($foreground)
				->string($colorizer->getBackground())->isEqualTo($background)
				->object($colorizer->getCli())->isEqualTo(new atoum\cli())
				->variable($colorizer->getPattern())->isNull()
			->if($colorizer = new cli\colorizer($foreground = uniqid(), $background = rand(1, PHP_INT_MAX)))
			->then
				->string($colorizer->getForeground())->isEqualTo($foreground)
				->string($colorizer->getBackground())->isEqualTo($background)
				->object($colorizer->getCli())->isEqualTo(new atoum\cli())
				->variable($colorizer->getPattern())->isNull()
			->if($colorizer = new cli\colorizer($foreground = uniqid(), $background = rand(1, PHP_INT_MAX), $cli = new atoum\cli()))
			->then
				->string($colorizer->getForeground())->isEqualTo($foreground)
				->string($colorizer->getBackground())->isEqualTo($background)
				->object($colorizer->getCli())->isIdenticalTo($cli)
				->variable($colorizer->getPattern())->isNull()
		;
	}

	public function testSetCli()
	{
		$this
			->if($colorizer = new cli\colorizer(uniqid()))
			->then
				->object($colorizer->setCli($cli = new atoum\cli()))->isIdenticalTo($colorizer)
				->object($colorizer->getCli())->isIdenticalTo($cli)
				->object($colorizer->setCli())->isIdenticalTo($colorizer)
				->object($colorizer->getCli())
					->isNotIdenticalTo($cli)
					->isEqualTo(new atoum\cli())
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
				->object($colorizer->setBackground($background = uniqid()))->isIdenticalTo($colorizer)
				->string($colorizer->getBackground())->isEqualTo($background)
				->object($colorizer->setBackground($background = rand(1, PHP_INT_MAX)))->isIdenticalTo($colorizer)
				->string($colorizer->getBackground())->isEqualTo($background)
		;
	}

	public function testSetPattern()
	{
		$this
			->if($colorizer = new cli\colorizer(uniqid()))
			->then
				->object($colorizer->setPattern($pattern = uniqid()))->isIdenticalTo($colorizer)
				->string($colorizer->getPattern())->isEqualTo($pattern)
		;
	}

	public function testColorize()
	{
		$this
			->if($colorizer = new cli\colorizer(null, null, $cli = new \mock\mageekguy\atoum\cli()))
			->and($this->calling($cli)->isTerminal = true)
			->then
				->string($colorizer->colorize($string = uniqid()))->isEqualTo($string)
			->if($colorizer = new cli\colorizer($foreground = uniqid(), null, $cli))
			->then
				->string($colorizer->colorize($string = uniqid()))->isEqualTo("\033[" . $foreground . 'm' . $string . "\033[0m")
			->if($colorizer = new cli\colorizer($foreground = uniqid(), $background = uniqid(), $cli))
			->then
				->string($colorizer->colorize($string = uniqid()))->isEqualTo("\033[" . $foreground . 'm' . "\033[" . $background . 'm' . $string . "\033[0m")
			->if($colorizer->setPattern('/^\s*([^:]+:)/'))
			->then
				->string($colorizer->colorize('Error:' . ($string = uniqid())))->isEqualTo("\033[" . $foreground . 'm' . "\033[" . $background . 'mError:' . "\033[0m" . $string)
			->if($colorizer = new cli\colorizer(null, $background = uniqid(), $cli))
			->then
				->string($colorizer->colorize($string = uniqid()))->isEqualTo("\033[" . $background . 'm' . $string . "\033[0m")
			->if($colorizer = new cli\colorizer($foreground = uniqid(), $background = uniqid(), $cli))
			->and($this->calling($cli)->isTerminal = false)
			->then
				->string($colorizer->colorize($string = uniqid()))->isEqualTo($string)
			->if($colorizer->setPattern('/^\s*([^:]+:)/'))
			->then
				->string($colorizer->colorize($string = 'Error:' . uniqid()))->isEqualTo($string)
		;
	}

	public function testDecorate()
	{
		$this
			->if($colorizer = new cli\colorizer(null, null, $cli = new \mock\mageekguy\atoum\cli()))
			->and($this->calling($cli)->isTerminal = true)
			->then
				->string($colorizer->decorate($string = uniqid()))->isEqualTo($string)
			->if($colorizer = new cli\colorizer($foreground = uniqid(), null, $cli))
			->then
				->string($colorizer->decorate($string = uniqid()))->isEqualTo("\033[" . $foreground . 'm' . $string . "\033[0m")
			->if($colorizer = new cli\colorizer($foreground = uniqid(), $background = uniqid(), $cli))
			->then
				->string($colorizer->decorate($string = uniqid()))->isEqualTo("\033[" . $foreground . 'm' . "\033[" . $background . 'm' . $string . "\033[0m")
			->if($colorizer = new cli\colorizer(null, $background = uniqid(), $cli))
			->then
				->string($colorizer->decorate($string = uniqid()))->isEqualTo("\033[" . $background . 'm' . $string . "\033[0m")
			->if($colorizer = new cli\colorizer($foreground = uniqid(), $background = uniqid(), $cli))
			->and($this->calling($cli)->isTerminal = false)
			->then
				->string($colorizer->decorate($string = uniqid()))->isEqualTo($string)
		;
	}
}
