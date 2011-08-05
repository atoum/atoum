<?php

namespace mageekguy\atoum\tests\units\cli;

use
	mageekguy\atoum,
	mageekguy\atoum\cli
;

require_once(__DIR__ . '/../../runner.php');

class colorizer extends atoum\test
{
	public function beforeTestMethod($testMethod)
	{
		if (defined('STDOUT') === false)
		{
			define('STDOUT', uniqid());
		}
	}

	public function test__construct()
	{
		$colorizer = new cli\colorizer();

		$this->assert
			->variable($colorizer->getForeground())->isNull()
			->variable($colorizer->getBackground())->isNull()
			->object($colorizer->getAdapter())->isEqualTo(new atoum\adapter())
		;

		$colorizer = new cli\colorizer($foreground = uniqid());

		$this->assert
			->string($colorizer->getForeground())->isEqualTo($foreground)
			->variable($colorizer->getBackground())->isNull()
			->object($colorizer->getAdapter())->isEqualTo(new atoum\adapter())
		;

		$colorizer = new cli\colorizer($foreground = rand(1, PHP_INT_MAX));

		$this->assert
			->string($colorizer->getForeground())->isEqualTo($foreground)
			->variable($colorizer->getBackground())->isNull()
			->object($colorizer->getAdapter())->isEqualTo(new atoum\adapter())
		;

		$colorizer = new cli\colorizer($foreground = uniqid(), $background = uniqid());

		$this->assert
			->string($colorizer->getForeground())->isEqualTo($foreground)
			->string($colorizer->getBackground())->isEqualTo($background)
			->object($colorizer->getAdapter())->isEqualTo(new atoum\adapter())
		;

		$colorizer = new cli\colorizer($foreground = uniqid(), $background = rand(1, PHP_INT_MAX));

		$this->assert
			->string($colorizer->getForeground())->isEqualTo($foreground)
			->string($colorizer->getBackground())->isEqualTo($background)
			->object($colorizer->getAdapter())->isEqualTo(new atoum\adapter())
		;

		$colorizer = new cli\colorizer($foreground = uniqid(), $background = rand(1, PHP_INT_MAX), $adapter = new atoum\adapter());

		$this->assert
			->string($colorizer->getForeground())->isEqualTo($foreground)
			->string($colorizer->getBackground())->isEqualTo($background)
			->object($colorizer->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetAdapter()
	{
		$colorizer = new cli\colorizer(uniqid());

		$this->assert
			->object($colorizer->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($colorizer)
			->object($colorizer->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetForeground()
	{
		$colorizer = new cli\colorizer(uniqid());

		$this->assert
			->object($colorizer->setForeground($foreground = uniqid()))->isIdenticalTo($colorizer)
			->string($colorizer->getForeground())->isEqualTo($foreground)
			->object($colorizer->setForeground($foreground = rand(1, PHP_INT_MAX)))->isIdenticalTo($colorizer)
			->string($colorizer->getForeground())->isEqualTo($foreground)
		;
	}

	public function testSetBackground()
	{
		$colorizer = new cli\colorizer(uniqid());

		$this->assert
			->object($colorizer->setBackground($foreground = uniqid()))->isIdenticalTo($colorizer)
			->string($colorizer->getBackground())->isEqualTo($foreground)
			->object($colorizer->setBackground($foreground = rand(1, PHP_INT_MAX)))->isIdenticalTo($colorizer)
			->string($colorizer->getBackground())->isEqualTo($foreground)
		;
	}

	public function testColorize()
	{
		$adapter = new atoum\test\adapter();

		$colorizer = new cli\colorizer(null, null, $adapter);

		$adapter->defined = function($constant) { return $constant == 'STDOUT'; };
		$adapter->posix_isatty = true;

		$this->assert
			->string($colorizer->colorize($string = uniqid()))->isEqualTo($string)
		;

		$colorizer = new cli\colorizer($foreground = uniqid(), null, $adapter);

		$this->assert
			->string($colorizer->colorize($string = uniqid()))->isEqualTo("\033[" . $foreground . 'm' . $string . "\033[0m")
		;

		$colorizer = new cli\colorizer($foreground = uniqid(), $background = uniqid(), $adapter);

		$this->assert
			->string($colorizer->colorize($string = uniqid()))->isEqualTo("\033[" . $foreground . 'm' . "\033[" . $background . 'm' . $string . "\033[0m")
		;

		$colorizer = new cli\colorizer(null, $background = uniqid(), $adapter);

		$this->assert
			->string($colorizer->colorize($string = uniqid()))->isEqualTo("\033[" . $background . 'm' . $string . "\033[0m")
		;

		$colorizer = new cli\colorizer($foreground = uniqid(), $background = uniqid(), $adapter);

		$adapter->defined = function($constant) { return $constant != 'STDOUT'; };

		$this->assert
			->string($colorizer->colorize($string = uniqid()))->isEqualTo($string)
		;

		$adapter->defined = function($constant) { return $constant == 'STDOUT'; };
		$adapter->posix_isatty = false;

		$this->assert
			->string($colorizer->colorize($string = uniqid()))->isEqualTo($string)
		;
	}
}

?>
