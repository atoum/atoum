<?php

namespace mageekguy\atoum\tests\units\phar;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\phar;

require_once(__DIR__ . '/../../runner.php');

/** @isolation on */
class generator extends atoum\test
{
	public function testClassConstants()
	{
		$this->assert
			->string(atoum\phar\generator::phar)->isEqualTo('mageekguy.atoum.phar')
		;
	}

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
		$adapter->realpath = function($path) { return $path; };

		$generator = new phar\generator(uniqid(), null, $adapter);

		$this->assert
			->exception(function() use ($generator) {
					$generator->setOriginDirectory('');
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Empty origin directory is invalid')
		;

		$adapter->is_dir = function() { return false; };

		$directory = uniqid();

		$this->assert
			->exception(function() use ($generator, $directory) {
					$generator->setOriginDirectory($directory);
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Path \'' . $directory . '\' of origin directory is invalid')
		;

		$adapter->is_dir = function() { return true; };

		$this->assert
			->object($generator->setOriginDirectory('/'))->isIdenticalTo($generator)
			->string($generator->getOriginDirectory())->isEqualTo('/')
		;

		$directory = uniqid();

		$this->assert
			->object($generator->setOriginDirectory($directory . '/'))->isIdenticalTo($generator)
			->string($generator->getOriginDirectory())->isEqualTo($directory)
		;

		$generator->setDestinationDirectory(uniqid());

		$this->assert
			->exception(function() use ($generator) {
					$generator->setOriginDirectory($generator->getDestinationDirectory());
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Origin directory must be different from destination directory')
		;

		$realDirectory = $generator->getDestinationDirectory() . DIRECTORY_SEPARATOR . uniqid();

		$adapter->realpath = function($path) use ($realDirectory) { return $realDirectory; };

		$this->assert
			->object($generator->setOriginDirectory('/'))->isIdenticalTo($generator)
			->string($generator->getOriginDirectory())->isEqualTo($realDirectory)
		;
	}

	public function testSetDestinationDirectory()
	{
		$adapter = new atoum\adapter();

		$adapter->php_sapi_name = function() { return 'cli'; };
		$adapter->realpath = function($path) { return $path; };

		$generator = new phar\generator(uniqid(), null, $adapter);

		$this->assert
			->exception(function() use ($generator) {
					$generator->setDestinationDirectory('');
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Empty destination directory is invalid')
		;

		$adapter->is_dir = function() { return false; };

		$directory = uniqid();

		$this->assert
			->exception(function() use ($generator, $directory) {
					$generator->setDestinationDirectory($directory);
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Path \'' . $directory . '\' of destination directory is invalid')
		;

		$adapter->is_dir = function() { return true; };

		$this->assert
			->object($generator->setDestinationDirectory('/'))->isIdenticalTo($generator)
			->string($generator->getDestinationDirectory())->isEqualTo('/')
		;

		$directory = uniqid();

		$this->assert
			->object($generator->setDestinationDirectory($directory))->isIdenticalTo($generator)
			->string($generator->getDestinationDirectory())->isEqualTo($directory)
		;

		$directory = uniqid();

		$this->assert
			->object($generator->setDestinationDirectory($directory . '/'))->isIdenticalTo($generator)
			->string($generator->getDestinationDirectory())->isEqualTo($directory)
		;

		$generator->setOriginDirectory(uniqid());

		$this->assert
			->exception(function() use ($generator) {
					$generator->setDestinationDirectory($generator->getOriginDirectory());
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Destination directory must be different from origin directory')
		;

		$realDirectory = $generator->getOriginDirectory() . DIRECTORY_SEPARATOR . uniqid();

		$adapter->realpath = function($path) use ($realDirectory) { return $realDirectory; };

		$this->assert
			->exception(function() use ($generator) {
					$generator->setDestinationDirectory(uniqid());
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Origin directory must not include destination directory')
		;
	}

	public function testSetPharInjecter()
	{
		$adapter = new atoum\adapter();

		$adapter->php_sapi_name = function() { return 'cli'; };
		$adapter->realpath = function($path) { return $path; };
		$adapter->is_dir = function() { return true; };

		$generator = new phar\generator(uniqid(), null, $adapter);

		$this->assert
			->exception(function() use ($generator) {
					$generator->setPharInjecter(function() {});
				}
			)
				->isInstanceOf('\runtimeException')
				->hasMessage('Phar injecter must take one argument')
		;

		$mockController = new mock\controller();
		$mockController
			->injectInNextMockInstance()
			->__construct = function() {}
		;

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\Phar');

		$pharName = uniqid();

		$phar = new mock\phar($pharName);

		$this->assert
			->exception(function() use ($generator, $pharName) { $generator->getPhar($pharName); })
				->isInstanceOf('\unexpectedValueException')
				->hasMessage('Cannot create phar \'' . $pharName . '\', file extension (or combination) not recognised')
			->object($generator->setPharInjecter(function($name) use ($phar) { return $phar; }))->isIdenticalTo($generator)
			->object($generator->getPhar(uniqid()))->isIdenticalTo($phar)
		;
	}

	public function testSetFileIteratorInjecter()
	{
		$adapter = new atoum\adapter();

		$adapter->php_sapi_name = function() { return 'cli'; };
		$adapter->realpath = function($path) { return $path; };
		$adapter->is_dir = function() { return true; };

		$generator = new phar\generator(uniqid(), null, $adapter);

		$this->assert
			->exception(function() use ($generator) {
					$generator->setFileIteratorInjecter(function() {});
				}
			)
				->isInstanceOf('\runtimeException')
				->hasMessage('File iterator injecter must take one argument')
		;

		$directory = uniqid();

		$mockController = new mock\controller();
		$mockController
			->injectInNextMockInstance()
			->__construct = function() {}
		;

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\recursiveDirectoryIterator');

		$iterator = new mock\recursiveDirectoryIterator($directory);

		$this->assert
			->exception(function() use ($generator, $directory) { $generator->getFileIterator($directory); })
				->isInstanceOf('\unexpectedValueException')
				->hasMessage('RecursiveDirectoryIterator::__construct(' . $directory . '): failed to open dir: No such file or directory')
			->object($generator->setFileIteratorInjecter(function($directory) use ($iterator) { return $iterator; }))->isIdenticalTo($generator)
			->object($generator->getFileIterator(uniqid()))->isIdenticalTo($iterator)
		;
	}

	public function testRun()
	{
		$adapter = new atoum\adapter();

		$adapter->php_sapi_name = function() { return 'cli'; };
		$adapter->realpath = function($path) { return $path; };
		$adapter->is_dir = function() { return true; };

		$generator = new phar\generator(uniqid(), null, $adapter);

		$this->assert
			->exception(function () use ($generator) {
						$generator->run();
					}
				)
				->isInstanceOf('\logicException')
				->hasMessage('Origin directory must be defined')
		;

		$generator->setOriginDirectory(uniqid());

		$this->assert
			->exception(function () use ($generator) {
						$generator->run();
					}
				)
				->isInstanceOf('\logicException')
				->hasMessage('Destination directory must be defined')
		;

		$generator->setDestinationDirectory(uniqid());

		$adapter->is_readable = function() { return false; };

		$this->assert
			->exception(function () use ($generator) {
						$generator->run();
					}
				)
				->isInstanceOf('\logicException')
				->hasMessage('Origin directory \'' . $generator->getOriginDirectory() . '\' is not readable')
		;

		$adapter->is_readable = function() { return true; };
		$adapter->is_writable = function() { return false; };

		$this->assert
			->exception(function () use ($generator) {
						$generator->run();
					}
				)
				->isInstanceOf('\logicException')
				->hasMessage('Destination directory \'' . $generator->getDestinationDirectory() . '\' is not writable')
		;

		$adapter->is_writable = function() { return true; };
		$adapter->file_get_contents = function() {};

		$mockGenerator = new mock\generator();

		$mockGenerator
			->generate('\phar')
			->generate('\recursiveDirectoryIterator')
		;

		$generator->setPharInjecter(function($name) use (& $phar) {
				$pharController = new mock\controller();
				$pharController->injectInNextMockInstance();
				$pharController->__construct = function() {};
				$pharController->setStub = function() {};
				$pharController->setMetadata = function() {};
				$pharController->buildFromIterator = function() {};
				$pharController->setSignatureAlgorithm = function() {};
				$pharController->injectInNextMockInstance();

				return ($phar = new mock\phar($name));
			}
		);

		$generator->setFileIteratorInjecter(function($directory) use (& $fileIterator) {
				$fileIteratorController = new mock\controller();
				$fileIteratorController->injectInNextMockInstance();
				$fileIteratorController->__construct = function() {};
				$fileIteratorController->injectInNextMockInstance();
				return ($fileIterator = new mock\recursiveDirectoryIterator($directory));
			}
		);

		$this->assert
			->object($generator->run())->isIdenticalTo($generator)
			->mock($phar)
				->callMethod('__construct', array($generator->getDestinationDirectory() . DIRECTORY_SEPARATOR . atoum\phar\generator::phar, null, null))
				->callMethod('buildFromIterator', array($fileIterator, null))
			->mock($fileIterator)
				->callMethod('__construct', array($generator->getOriginDirectory()))
		;
	}
}

?>
