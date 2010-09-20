<?php

namespace mageekguy\atoum\tests\units\phar;

use \mageekguy\atoum;
use \mageekguy\atoum\phar;

require_once(__DIR__ . '/../../runner.php');

/** @isolation on */
class generator extends atoum\test
{
	public function test__construct()
	{
		$name = uniqid();

		$generator = new atoum\phar\generator($name);

		$this->assert
			->object($generator->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->object($generator->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
			->string($generator->getName())->isEqualTo($name)
			->variable($generator->getFromDirectory())->isNull()
			->variable($generator->getDestinationDirectory())->isNull()
			->collection($generator->getErrors())->isEmpty()
			->collection($generator->getArguments())->isEmpty()
		;

		$name = uniqid();
		$locale = new atoum\locale();
		$adapter = new atoum\adapter();

		$generator = new atoum\phar\generator($name, $locale, $adapter);

		$this->assert
			->object($generator->getLocale())->isIdenticalTo($locale)
			->object($generator->getAdapter())->isIdenticalTo($adapter)
			->string($generator->getName())->isEqualTo($name)
			->variable($generator->getFromDirectory())->isNull()
			->variable($generator->getDestinationDirectory())->isNull()
			->collection($generator->getErrors())->isEmpty()
			->collection($generator->getArguments())->isEmpty()
		;
	}

	public function testSetFromDirectory()
	{
		$this->assert
			->exception(function() {
					$generator = new atoum\phar\generator(uniqid());
					$generator->setFromDirectory('');
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Empty path is invalid')
		;

		$generator = new atoum\phar\generator(uniqid());

		$this->assert
			->object($generator->setFromDirectory('/'))->isIdenticalTo($generator)
			->string($generator->getFromDirectory())->isEqualTo('/')
		;

		$generator = new atoum\phar\generator(uniqid());

		$directory = uniqid();

		$this->assert
			->object($generator->setFromDirectory($directory))->isIdenticalTo($generator)
			->string($generator->getFromDirectory())->isEqualTo($directory)
		;

		$generator = new atoum\phar\generator(uniqid());

		$directory = uniqid();

		$this->assert
			->object($generator->setFromDirectory($directory . '/'))->isIdenticalTo($generator)
			->string($generator->getFromDirectory())->isEqualTo($directory)
		;
	}
}

?>
