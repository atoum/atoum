<?php

namespace mageekguy\atoum\tests\units\phar;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\phar;

require_once(__DIR__ . '/../../runner.php');

class generator extends atoum\test
{
	public function testClassConstants()
	{
		$this->assert
			->string(phar\generator::phar)->isEqualTo('mageekguy.atoum.phar')
			->string(phar\generator::version)->isEqualTo('0.0.1')
			->string(phar\generator::author)->isEqualTo('Frédéric Hardy')
			->string(phar\generator::mail)->isEqualTo('support@atoum.org')
			->string(phar\generator::repository)->isEqualTo('https://svn.mageekbox.net/repositories/unit/trunk')
		;
	}

	public function test__construct()
	{
		$adapter = new atoum\adapter();

		$adapter->php_sapi_name = function() { return uniqid(); };

		$name = uniqid();

		$this->assert
			->exception(function() use ($name, $adapter) {
					$generator = new phar\generator($name, null, $adapter);
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('\'' . $name . '\' must be used in CLI only')
		;

		$adapter->php_sapi_name = function() { return 'cli'; };

		$name = uniqid();

		$generator = new phar\generator($name, null, $adapter);

		$this->assert
			->object($generator->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->object($generator->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
			->object($generator->getOutputWriter())->isInstanceOf('\mageekguy\atoum\writer')
			->object($generator->getErrorWriter())->isInstanceOf('\mageekguy\atoum\writer')
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
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Empty origin directory is invalid')
		;

		$adapter->is_dir = function() { return false; };

		$directory = uniqid();

		$this->assert
			->exception(function() use ($generator, $directory) {
					$generator->setOriginDirectory($directory);
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
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
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
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
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Empty destination directory is invalid')
		;

		$adapter->is_dir = function() { return false; };

		$directory = uniqid();

		$this->assert
			->exception(function() use ($generator, $directory) {
					$generator->setDestinationDirectory($directory);
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
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
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Destination directory must be different from origin directory')
		;

		$realDirectory = $generator->getOriginDirectory() . DIRECTORY_SEPARATOR . uniqid();

		$adapter->realpath = function($path) use ($realDirectory) { return $realDirectory; };

		$this->assert
			->exception(function() use ($generator) {
					$generator->setDestinationDirectory(uniqid());
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Origin directory must not include destination directory')
		;
	}

	public function testSetStubFile()
	{
		$adapter = new atoum\adapter();

		$adapter->php_sapi_name = function() { return 'cli'; };
		$adapter->realpath = function($path) { return $path; };

		$generator = new phar\generator(uniqid(), null, $adapter);

		$this->assert
			->exception(function() use ($generator) {
					$generator->setStubFile('');
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Stub file is invalid')
		;

		$adapter->is_file = function() { return false; };

		$this->assert
			->exception(function() use ($generator) {
					$generator->setStubFile(uniqid());
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Stub file is not a valid file')
		;

		$adapter->is_file = function() { return true; };

		$this->assert
			->object($generator->setStubFile($stubFile = uniqid()))->isIdenticalTo($generator)
			->string($generator->getStubFile())->isEqualTo($stubFile)
		;
	}

	public function testSetPharInjector()
	{
		$adapter = new atoum\adapter();

		$adapter->php_sapi_name = function() { return 'cli'; };
		$adapter->realpath = function($path) { return $path; };
		$adapter->is_dir = function() { return true; };

		$generator = new phar\generator(uniqid(), null, $adapter);

		$this->assert
			->exception(function() use ($generator) {
					$generator->setPharInjector(function() {});
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Phar injector must take one argument')
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
			->object($generator->setPharInjector(function($name) use ($phar) { return $phar; }))->isIdenticalTo($generator)
			->object($generator->getPhar(uniqid()))->isIdenticalTo($phar)
		;
	}

	public function testSetFileIteratorInjector()
	{
		$adapter = new atoum\adapter();

		$adapter->php_sapi_name = function() { return 'cli'; };
		$adapter->realpath = function($path) { return $path; };
		$adapter->is_dir = function() { return true; };

		$generator = new phar\generator(uniqid(), null, $adapter);

		$this->assert
			->exception(function() use ($generator) {
					$generator->setFileIteratorInjector(function() {});
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('File iterator injector must take one argument')
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
			->object($generator->setFileIteratorInjector(function($directory) use ($iterator) { return $iterator; }))->isIdenticalTo($generator)
			->object($generator->getFileIterator(uniqid()))->isIdenticalTo($iterator)
		;
	}

	public function testSetOutputWriter()
	{
		$generator = new phar\generator(uniqid());

		$stdout = new atoum\writers\stdout();

		$this->assert
			->object($generator->setOutputWriter($stdout))->isIdenticalTo($generator)
			->object($generator->getOutputWriter())->isIdenticalTo($stdout)
		;
	}

	public function testSetErrorWriter()
	{
		$generator = new phar\generator(uniqid());

		$stderr = new atoum\writers\stderr();

		$this->assert
			->object($generator->setErrorWriter($stderr))->isIdenticalTo($generator)
			->object($generator->getErrorWriter())->isIdenticalTo($stderr)
		;
	}

	public function testWriteMessage()
	{
		$generator = new phar\generator(uniqid());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\writers\stdout')
		;

		$stdout = new mock\mageekguy\atoum\writers\stdout();
		$stdout->getMockController()->write = function() {};

		$generator->setOutputWriter($stdout);

		$this->assert
			->object($generator->writeMessage($message = uniqid()))->isIdenticalTo($generator)
			->mock($stdout)
				->call('write', array($message))
		;
	}

	public function testWriteError()
	{
		$generator = new phar\generator(uniqid());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\writers\stderr')
		;

		$stderr = new mock\mageekguy\atoum\writers\stderr();
		$stderr->getMockController()->write = function() {};

		$generator->setErrorWriter($stderr);

		$this->assert
			->object($generator->writeError($error = uniqid()))->isIdenticalTo($generator)
			->mock($stderr)
				->call('write', array(sprintf($generator->getLocale()->_('Error: %s'), $error)))
		;
	}

	public function testRun()
	{
		$adapter = new atoum\adapter();

		$adapter->php_sapi_name = function() { return 'cli'; };
		$adapter->realpath = function($path) { return $path; };
		$adapter->is_dir = function() { return true; };
		$adapter->is_file = function() { return true; };

		$generator = new phar\generator(uniqid(), null, $adapter);

		$this->assert
			->exception(function () use ($generator) {
						$generator->run();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Origin directory must be defined')
		;

		$generator->setOriginDirectory($originDirectory = uniqid());

		$this->assert
			->exception(function () use ($generator) {
						$generator->run();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Destination directory must be defined')
		;

		$generator->setDestinationDirectory(uniqid());

		$this->assert
			->exception(function () use ($generator) {
						$generator->run();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Stub file must be defined')
		;

		$generator->setStubFile($stubFile = uniqid());

		$adapter->is_readable = function() { return false; };

		$this->assert
			->exception(function () use ($generator) {
						$generator->run();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Origin directory \'' . $generator->getOriginDirectory() . '\' is not readable')
		;

		$adapter->is_readable = function($path) use ($originDirectory) { return ($path === $originDirectory); };

		$adapter->is_writable = function() { return false; };

		$this->assert
			->exception(function () use ($generator) {
						$generator->run();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Destination directory \'' . $generator->getDestinationDirectory() . '\' is not writable')
		;

		$adapter->is_writable = function() { return true; };

		$this->assert
			->exception(function () use ($generator) {
						$generator->run();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Stub file \'' . $generator->getStubFile() . '\' is not readable')
		;

		$adapter->is_readable = function($path) use ($originDirectory, $stubFile) { return ($path === $originDirectory || $path === $stubFile); };

		$generator->setPharInjector(function($name) { return null; });

		$this->assert
			->exception(function() use ($generator) {
						$generator->run();
					}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Phar injector must return a \phar instance')
		;

		$mockGenerator = new mock\generator();

		$mockGenerator->generate('\phar');

		$generator->setPharInjector(function($name) use (& $phar) {
				$pharController = new mock\controller();
				$pharController->__construct = function() {};
				$pharController->setStub = function() {};
				$pharController->setMetadata = function() {};
				$pharController->buildFromIterator = function() {};
				$pharController->setSignatureAlgorithm = function() {};
				$pharController->injectInNextMockInstance();

				return ($phar = new mock\phar($name));
			}
		);

		$generator->setFileIteratorInjector(function($directory) { return null; });

		$this->assert
			->exception(function() use ($generator) {
						$generator->run();
					}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('File iterator injector must return a \iterator instance')
		;

		$mockGenerator->generate('\recursiveDirectoryIterator');

		$generator->setFileIteratorInjector(function($directory) use (& $fileIterator) {
				$fileIteratorController = new mock\controller();
				$fileIteratorController->injectInNextMockInstance();
				$fileIteratorController->__construct = function() {};
				$fileIteratorController->injectInNextMockInstance();
				return ($fileIterator = new mock\recursiveDirectoryIterator($directory));
			}
		);

		$adapter->file_get_contents = function($file) { return false; };

		$this->assert
			->exception(function() use ($generator) {
					$generator->run();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('ABOUT file is missing in \'' . $generator->getOriginDirectory() . '\'')
		;

		$description = uniqid();

		$adapter->file_get_contents = function($file) use ($generator, $description) {
				switch ($file)
				{
					case $generator->getOriginDirectory() . DIRECTORY_SEPARATOR . 'ABOUT':
						return $description;

					default:
						return false;
				}
			}
		;

		$this->assert
			->exception(function() use ($generator) {
					$generator->run();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('COPYING file is missing in \'' . $generator->getOriginDirectory() . '\'')
		;

		$licence = uniqid();
		$stub = uniqid();

		$adapter->file_get_contents = function($file) use ($generator, $description, $licence, $stub) {
				switch ($file)
				{
					case $generator->getOriginDirectory() . DIRECTORY_SEPARATOR . 'ABOUT':
						return $description;

					case $generator->getOriginDirectory() . DIRECTORY_SEPARATOR . 'COPYING':
						return $licence;

					case $generator->getStubFile():
						return $stub;

					default:
						return uniqid();
				}
			}
		;

		$this->assert
			->object($generator->run())->isIdenticalTo($generator)
			->mock($phar)
				->call('__construct', array(
						$generator->getDestinationDirectory() . DIRECTORY_SEPARATOR . atoum\phar\generator::phar, null, null, null
					)
				)
				->call('setMetadata', array(
						array(
							'version' => atoum\test::getVersion(),
							'author' => atoum\test::author,
							'support' => atoum\phar\generator::mail,
							'repository' => atoum\phar\generator::repository,
							'description' => $description,
							'licence' => $licence
						)
					)
				)
				->call('setStub', array($stub, null))
				->call('buildFromIterator', array(
						$fileIterator,
						$generator->getOriginDirectory()
					)
				)
				->call('setSignatureAlgorithm', array(
						\phar::SHA1,
						null
					)
				)
			->mock($fileIterator)
				->call('__construct', array($generator->getOriginDirectory(), null))
		;

		$superglobals = new atoum\superglobals();

		$superglobals->_SERVER = array('argv' => array(uniqid(), '--help'));

		$mockGenerator
			->generate('\mageekguy\atoum\writers\stdout')
			->generate('\mageekguy\atoum\writers\stderr')
		;

		$stdout = new mock\mageekguy\atoum\writers\stdout();
		$stdout
			->getMockController()
				->write = function() {}
		;

		$stderr = new mock\mageekguy\atoum\writers\stderr();
		$stderr
			->getMockController()
				->write = function() {}
		;

		$generator
			->setOutputWriter($stdout)
			->setErrorWriter($stderr)
		;

		$this->assert
			->object($generator->run($superglobals))->isIdenticalTo($generator)
			->mock($stdout)
				->call('write', array(sprintf($generator->getLocale()->_('Usage: %s [options]'), $generator->getName()) . PHP_EOL))
				->call('write', array(sprintf($generator->getLocale()->_('Phar generator of \mageekguy\atoum version %s'), atoum\phar\generator::version) . PHP_EOL))
				->call('write', array($generator->getLocale()->_('Available options are:') . PHP_EOL))
				->call('write', array('                    -h, --help: ' . $generator->getLocale()->_('Display this help') . PHP_EOL))
				->call('write', array('   -d <dir>, --directory <dir>: ' . $generator->getLocale()->_('Destination directory <dir>') . PHP_EOL))
		;

		$generator->setPharInjector(function($name) use (& $phar) {
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

		$superglobals->_SERVER = array('argv' => array(uniqid(), '-d', 	$directory = uniqid()));

		$this->assert
			->object($generator->run($superglobals))->isIdenticalTo($generator)
			->string($generator->getDestinationDirectory())->isEqualTo($directory)
			->mock($phar)
				->call('__construct', array(
						$generator->getDestinationDirectory() . DIRECTORY_SEPARATOR . atoum\phar\generator::phar, null, null, null
					)
				)
				->call('setMetadata', array(
						array(
							'version' => atoum\test::getVersion(),
							'author' => atoum\test::author,
							'support' => atoum\phar\generator::mail,
							'repository' => atoum\phar\generator::repository,
							'description' => $description,
							'licence' => $licence
						)
					)
				)
				->call('setStub', array($stub, null))
				->call('buildFromIterator', array(
						$fileIterator,
						$generator->getOriginDirectory()
					)
				)
				->call('setSignatureAlgorithm', array(
						\phar::SHA1,
						null
					)
				)
			->mock($fileIterator)
				->call('__construct', array($generator->getOriginDirectory(), null))
		;
	}
}

?>
