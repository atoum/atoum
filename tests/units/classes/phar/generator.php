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
		$adapter = new atoum\adapter();

		$adapter->php_sapi_name = function() { return uniqid(); };

		$this->assert
			->exception(function() use ($adapter) {
					$generator = new phar\generator(uniqid(), null, $adapter);
				}
			)
				->isInstanceOf('\logicException')
		;

		$adapter->php_sapi_name = function() { return 'cli'; };

		$name = uniqid();

		$generator = new phar\generator($name, null, $adapter);

		$this->assert
			->object($generator->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->object($generator->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
			->string($generator->getName())->isEqualTo($name)
			->variable($generator->getOriginDirectory())->isNull()
			->variable($generator->getDestinationDirectory())->isNull()
			->collection($generator->getArguments())->isEmpty()
		;

		$name = uniqid();
		$locale = new atoum\locale();

		$generator = new phar\generator($name, $locale, $adapter);

		$this->assert
			->object($generator->getLocale())->isIdenticalTo($locale)
			->object($generator->getAdapter())->isIdenticalTo($adapter)
			->string($generator->getName())->isEqualTo($name)
			->variable($generator->getOriginDirectory())->isNull()
			->variable($generator->getDestinationDirectory())->isNull()
			->collection($generator->getArguments())->isEmpty()
		;
	}

	public function testSetOriginDirectory()
	{
		$adapter = new atoum\adapter();

		$adapter->php_sapi_name = function() { return 'cli'; };

		$this->assert
			->exception(function() use ($adapter) {
					$generator = new phar\generator(uniqid(), null, $adapter);
					$generator->setOriginDirectory('');
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Empty origin directory is invalid')
		;

		$generator = new phar\generator(uniqid(), null, $adapter);

		$this->assert
			->object($generator->setOriginDirectory('/'))->isIdenticalTo($generator)
			->string($generator->getOriginDirectory())->isEqualTo('/')
		;

		$generator = new phar\generator(uniqid(), null, $adapter);

		$directory = uniqid();

		$this->assert
			->object($generator->setOriginDirectory($directory))->isIdenticalTo($generator)
			->string($generator->getOriginDirectory())->isEqualTo($directory)
		;

		$generator = new phar\generator(uniqid(), null, $adapter);

		$directory = uniqid();

		$this->assert
			->object($generator->setOriginDirectory($directory . '/'))->isIdenticalTo($generator)
			->string($generator->getOriginDirectory())->isEqualTo($directory)
		;

		$this->assert
			->exception(function() use ($adapter) {
					$generator = new phar\generator(uniqid(), null, $adapter);
					$generator->setDestinationDirectory(uniqid());
					$generator->setOriginDirectory($generator->getDestinationDirectory());
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Origin directory must be different from destination directory')
		;
	}

	public function testSetDestinationDirectory()
	{
		$adapter = new atoum\adapter();

		$adapter->php_sapi_name = function() { return 'cli'; };

		$this->assert
			->exception(function() use ($adapter) {
					$generator = new phar\generator(uniqid(), null, $adapter);
					$generator->setDestinationDirectory('');
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Empty destination directory is invalid')
		;

		$generator = new phar\generator(uniqid(), null, $adapter);

		$this->assert
			->object($generator->setDestinationDirectory('/'))->isIdenticalTo($generator)
			->string($generator->getDestinationDirectory())->isEqualTo('/')
		;

		$generator = new phar\generator(uniqid(), null, $adapter);

		$directory = uniqid();

		$this->assert
			->object($generator->setDestinationDirectory($directory))->isIdenticalTo($generator)
			->string($generator->getDestinationDirectory())->isEqualTo($directory)
		;

		$generator = new phar\generator(uniqid(), null, $adapter);

		$directory = uniqid();

		$this->assert
			->object($generator->setDestinationDirectory($directory . '/'))->isIdenticalTo($generator)
			->string($generator->getDestinationDirectory())->isEqualTo($directory)
		;

		$this->assert
			->exception(function() use ($adapter) {
					$generator = new phar\generator(uniqid(), null, $adapter);
					$generator->setOriginDirectory(uniqid());
					$generator->setDestinationDirectory($generator->getOriginDirectory());
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Destination directory must be different from origin directory')
		;
	}

	public function testRun()
	{
		$adapter = new atoum\adapter();

		$adapter->php_sapi_name = function() { return 'cli'; };

		$generator = new phar\generator(uniqid(), null, $adapter);

		$this->assert
			->variable($generator->getOriginDirectory())->isNull()
			->variable($generator->getDestinationDirectory())->isNull()
			->exception(function () use ($adapter) {
						$generator = new phar\generator(uniqid(), null, $adapter);
						$generator->run();
					}
				)
				->isInstanceOf('\logicException')
				->hasMessage('Origin directory must be defined')
		;
	}
}

?>
