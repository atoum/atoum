<?php

namespace mageekguy\atoum\tests\units\scripts\phar;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\mock\stream,
	mageekguy\atoum\iterators,
	mageekguy\atoum\scripts\phar
;

require_once __DIR__ . '/../../../runner.php';

class generator extends atoum\test
{
	public function testClassConstants()
	{
		$this->assert
			->string(phar\generator::phar)->isEqualTo('mageekguy.atoum.phar')
		;
	}

	public function test__construct()
	{
		$this->assert
			->if($adapter = new atoum\test\adapter())
			->and($adapter->php_sapi_name = function() { return uniqid(); })
			->and($factory = new atoum\factory())
			->and($factory->import('mageekguy\atoum'))
			->and($factory->returnWhenBuild('atoum\adapter', $adapter))
			->then
				->exception(function() use (& $name, $factory) {
						$generator = new phar\generator($name = uniqid(), $factory);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('\'' . $name . '\' must be used in CLI only')
			->if($adapter->php_sapi_name = function() { return 'cli'; })
			->and($generator = new phar\generator($name = uniqid(), $factory))
			->then
				->object($generator->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($generator->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($generator->getOutputWriter())->isInstanceOf('mageekguy\atoum\writer')
				->object($generator->getErrorWriter())->isInstanceOf('mageekguy\atoum\writer')
				->string($generator->getName())->isEqualTo($name)
				->variable($generator->getOriginDirectory())->isNull()
				->variable($generator->getDestinationDirectory())->isNull()
				->object($generator->getArgumentsParser())->isInstanceOf('mageekguy\atoum\script\arguments\parser')
			->if($factory->returnWhenBuild('atoum\locale', $locale = new atoum\locale()))
			->and($factory->returnWhenBuild('atoum\script\arguments\parser', $argumentsParser = new atoum\script\arguments\parser()))
			->and($generator = new phar\generator($name = uniqid(), $factory))
			->then
				->string($generator->getName())->isEqualTo($name)
				->object($generator->getLocale())->isIdenticalTo($locale)
				->object($generator->getAdapter())->isIdenticalTo($adapter)
				->object($generator->getArgumentsParser())->isIdenticalTo($argumentsParser)
				->variable($generator->getOriginDirectory())->isNull()
				->variable($generator->getDestinationDirectory())->isNull()
		;
	}

	public function testSetOriginDirectory()
	{
		$this->assert
			->if($adapter = new atoum\test\adapter())
			->and($adapter->php_sapi_name = function() { return 'cli'; })
			->and($adapter->realpath = function($path) { return $path; })
			->and($factory = new atoum\factory())
			->and($factory->import('mageekguy\atoum'))
			->and($factory->returnWhenBuild('atoum\adapter', $adapter))
			->and($generator = new phar\generator(uniqid(), $factory))
			->then
				->exception(function() use ($generator) {
						$generator->setOriginDirectory('');
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Empty origin directory is invalid')
			->if($adapter->is_dir = function() { return false; })
			->then
				->exception(function() use ($generator, & $directory) {
						$generator->setOriginDirectory($directory = uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Path \'' . $directory . '\' of origin directory is invalid')
			->if($adapter->is_dir = function() { return true; })
			->then
				->object($generator->setOriginDirectory('/'))->isIdenticalTo($generator)
				->string($generator->getOriginDirectory())->isEqualTo('/')
				->object($generator->setOriginDirectory(($directory = uniqid()) . DIRECTORY_SEPARATOR))->isIdenticalTo($generator)
				->string($generator->getOriginDirectory())->isEqualTo($directory)
			->if($generator->setDestinationDirectory(uniqid()))
			->then
				->exception(function() use ($generator) {
						$generator->setOriginDirectory($generator->getDestinationDirectory());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Origin directory must be different from destination directory')
			->if($realDirectory = $generator->getDestinationDirectory() . DIRECTORY_SEPARATOR . uniqid())
			->and($adapter->realpath = function($path) use ($realDirectory) { return $realDirectory; })
			->then
				->object($generator->setOriginDirectory('/'))->isIdenticalTo($generator)
				->string($generator->getOriginDirectory())->isEqualTo($realDirectory)
		;
	}

	public function testSetDestinationDirectory()
	{
		$this->assert
			->if($adapter = new atoum\test\adapter())
			->and($adapter->php_sapi_name = function() { return 'cli'; })
			->and($adapter->realpath = function($path) { return $path; })
			->and($factory = new atoum\factory())
			->and($factory->import('mageekguy\atoum'))
			->and($factory->returnWhenBuild('atoum\adapter', $adapter))
			->and($generator = new phar\generator(uniqid(), $factory))
			->then
				->exception(function() use ($generator) {
						$generator->setDestinationDirectory('');
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Empty destination directory is invalid')
			->if ($adapter->is_dir = function() { return false; })
			->then
				->exception(function() use ($generator, & $directory) {
						$generator->setDestinationDirectory($directory = uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Path \'' . $directory . '\' of destination directory is invalid')
			->if($adapter->is_dir = function() { return true; })
			->then
				->object($generator->setDestinationDirectory('/'))->isIdenticalTo($generator)
				->string($generator->getDestinationDirectory())->isEqualTo('/')
				->object($generator->setDestinationDirectory($directory = uniqid()))->isIdenticalTo($generator)
				->string($generator->getDestinationDirectory())->isEqualTo($directory)
				->object($generator->setDestinationDirectory(($directory = uniqid()) . DIRECTORY_SEPARATOR))->isIdenticalTo($generator)
				->string($generator->getDestinationDirectory())->isEqualTo($directory)
			->if ($generator->setOriginDirectory(uniqid()))
			->then
				->exception(function() use ($generator) {
						$generator->setDestinationDirectory($generator->getOriginDirectory());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Destination directory must be different from origin directory')
		;
	}

	public function testSetStubFile()
	{
		$this->assert
			->if($adapter = new atoum\test\adapter())
			->and($adapter->php_sapi_name = function() { return 'cli'; })
			->and($adapter->realpath = function($path) { return $path; })
			->and($factory = new atoum\factory())
			->and($factory->import('mageekguy\atoum'))
			->and($factory->returnWhenBuild('atoum\adapter', $adapter))
			->and($generator = new phar\generator(uniqid(), $factory))
			->then
				->exception(function() use ($generator) {
						$generator->setStubFile('');
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Stub file is invalid')
			->if($adapter->is_file = function() { return false; })
			->then
				->exception(function() use ($generator) {
						$generator->setStubFile(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Stub file is not a valid file')
			->if($adapter->is_file = function() { return true; })
			->then
				->object($generator->setStubFile($stubFile = uniqid()))->isIdenticalTo($generator)
				->string($generator->getStubFile())->isEqualTo($stubFile)
		;
	}

	public function testSetOutputWriter()
	{
		$this->assert
			->if($generator = new phar\generator(uniqid()))
			->then
				->object($generator->setOutputWriter($stdout = new atoum\writers\std\out()))->isIdenticalTo($generator)
				->object($generator->getOutputWriter())->isIdenticalTo($stdout)
		;
	}

	public function testSetErrorWriter()
	{

		$this->assert
			->if($generator = new phar\generator(uniqid()))
			->then
				->object($generator->setErrorWriter($stderr = new atoum\writers\std\err()))->isIdenticalTo($generator)
				->object($generator->getErrorWriter())->isIdenticalTo($stderr)
		;
	}

	public function testWriteMessage()
	{
		$this
			->assert
				->if($generator = new phar\generator(uniqid()))
				->and($stdout = new \mock\mageekguy\atoum\writers\std\out())
				->and($stdout->getMockController()->write = function() {})
				->and($generator->setOutputWriter($stdout))
				->then
					->object($generator->writeMessage($message = uniqid()))->isIdenticalTo($generator)
					->mock($stdout)->call('write')->withArguments($message . PHP_EOL)->once()
		;
	}

	public function testWriteError()
	{
		$this
			->assert
				->if($generator = new phar\generator(uniqid()))
				->and($stderr = new \mock\mageekguy\atoum\writers\std\err())
				->and($stderr->getMockController()->write = function() {})
				->and($generator->setErrorWriter($stderr))
				->then
					->object($generator->writeError($error = uniqid()))->isIdenticalTo($generator)
					->mock($stderr)
						->call('write')->withArguments(sprintf($generator->getLocale()->_('Error: %s'), $error) . PHP_EOL)->once()
		;
	}

	public function testRun()
	{
		$this
			->assert
				->if($originDirectory = stream::get())
				->and($originDirectory->opendir = true)
				->and($adapter = new atoum\test\adapter())
				->and($adapter->php_sapi_name = function() { return 'cli'; })
				->and($adapter->realpath = function($path) { return $path; })
				->and($adapter->is_dir = function() { return true; })
				->and($adapter->is_file = function() { return true; })
				->and($adapter->unlink = function() {})
				->and($factory = new atoum\factory())
				->and($factory->import('mageekguy\atoum'))
				->and($factory->returnWhenBuild('atoum\adapter', $adapter))
				->and($generator = new phar\generator(uniqid(), $factory))
				->then
					->exception(function () use ($generator) {
							$generator->run();
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Origin directory must be defined')
				->if($generator->setOriginDirectory((string) $originDirectory))
				->then
					->exception(function () use ($generator) {
							$generator->run();
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Destination directory must be defined')
				->if($generator->setDestinationDirectory(uniqid()))
				->then
					->exception(function () use ($generator) {
							$generator->run();
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Stub file must be defined')
				->if($generator->setStubFile($stubFile = uniqid()))
				->and($adapter->is_readable = function() { return false; })
				->then
					->exception(function () use ($generator) {
							$generator->run();
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Origin directory \'' . $generator->getOriginDirectory() . '\' is not readable')
				->if($adapter->is_readable = function($path) use ($originDirectory) { return ($path === (string) $originDirectory); })
				->and($adapter->is_writable = function() { return false; })
				->then
					->exception(function () use ($generator) {
							$generator->run();
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Destination directory \'' . $generator->getDestinationDirectory() . '\' is not writable')
				->if($adapter->is_writable = function() { return true; })
				->then
					->exception(function () use ($generator) {
							$generator->run();
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Stub file \'' . $generator->getStubFile() . '\' is not readable')
				->if($adapter->is_readable = function($path) use ($originDirectory, $stubFile) { return ($path === (string) $originDirectory || $path === $stubFile); })
				->and($generator->setFactory($factory->setBuilder('phar', function($name) use (& $phar) {
								$pharController = new mock\controller();
								$pharController->__construct = function() {};
								$pharController->setStub = function() {};
								$pharController->setMetadata = function() {};
								$pharController->buildFromIterator = function() {};
								$pharController->setSignatureAlgorithm = function() {};
								$pharController->offsetGet = function() {};
								$pharController->offsetSet = function() {};

								return ($phar = new \mock\phar($name));
							}
						)
					)
				)
				->and($adapter->file_get_contents = function($file) { return false; })
				->then
					->exception(function() use ($generator) {
							$generator->run();
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('ABOUT file is missing in \'' . $generator->getOriginDirectory() . '\'')
				->if($adapter->file_get_contents = function($file) use ($generator, & $description) {
						switch ($file)
						{
							case $generator->getOriginDirectory() . DIRECTORY_SEPARATOR . 'ABOUT':
								return ($description = uniqid());

							default:
								return false;
						}
					}
				)
				->then
					->exception(function() use ($generator) {
							$generator->run();
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('COPYING file is missing in \'' . $generator->getOriginDirectory() . '\'')
				->if($adapter->file_get_contents = function($file) use ($generator, & $description, & $licence, & $stub) {
						switch ($file)
						{
							case $generator->getOriginDirectory() . DIRECTORY_SEPARATOR . 'ABOUT':
								return ($description = uniqid());

							case $generator->getOriginDirectory() . DIRECTORY_SEPARATOR . 'COPYING':
								return ($licence = uniqid());

							case $generator->getStubFile():
								return ($stub = uniqid());

							default:
								return uniqid();
						}
					}
				)
				->then
					->object($generator->run())->isIdenticalTo($generator)
					->mock($phar)
						->call('__construct')->withArguments($generator->getDestinationDirectory() . DIRECTORY_SEPARATOR . atoum\scripts\phar\generator::phar, null, null, null)->once()
						->call('setMetadata')
							->withArguments(array(
									'version' => atoum\version,
									'author' => atoum\author,
									'support' => atoum\mail,
									'repository' => atoum\repository,
									'description' => $description,
									'licence' => $licence
								)
							)
							->once()
						->call('setStub')->withArguments($stub, null)->once()
						->call('buildFromIterator')
							->withArguments(new iterators\recursives\atoum\source($generator->getOriginDirectory(), '1'), null)
							->once()
						->call('setSignatureAlgorithm')
							->withArguments(\phar::SHA1, null)
							->once()
				->if($superglobals = new atoum\superglobals())
				->and($superglobals->_SERVER = array('argv' => array(uniqid(), '--help')))
				->and($generator->setArgumentsParser(new atoum\script\arguments\parser($superglobals)))
				->and($stdout = new \mock\mageekguy\atoum\writers\std\out())
				->and($stdout->getMockController()->write = function() {})
				->and($stderr = new \mock\mageekguy\atoum\writers\std\err())
				->and($stderr->getMockController()->write = function() {})
				->and($generator->setOutputWriter($stdout))
				->and($generator->setErrorWriter($stderr))
				->then
					->object($generator->run())->isIdenticalTo($generator)
					->mock($stdout)
						->call('write')->withArguments(sprintf($generator->getLocale()->_('Usage: %s [options]'), $generator->getName()) . PHP_EOL)->once()
						->call('write')->withArguments($generator->getLocale()->_('Available options are:') . PHP_EOL)->once()
						->call('write')->withArguments('                                -h, --help: ' . $generator->getLocale()->_('Display this help') . PHP_EOL)->once()
						->call('write')->withArguments('   -d <directory>, --directory <directory>: ' . $generator->getLocale()->_('Destination directory <dir>') . PHP_EOL)->once()
				->if($generator->getFactory()->setBuilder('phar', function($name) use (& $phar) {
							$pharController = new mock\controller();
							$pharController->__construct = function() {};
							$pharController->setStub = function() {};
							$pharController->setMetadata = function() {};
							$pharController->buildFromIterator = function() {};
							$pharController->setSignatureAlgorithm = function() {};
							$pharController->offsetGet = function() {};
							$pharController->offsetSet = function() {};

							return ($phar = new \mock\phar($name));
						}
					)
				)
				->then
					->object($generator->run(array('-d', $directory = uniqid())))->isIdenticalTo($generator)
					->string($generator->getDestinationDirectory())->isEqualTo($directory)
					->mock($phar)
						->call('__construct')
							->withArguments($generator->getDestinationDirectory() . DIRECTORY_SEPARATOR . atoum\scripts\phar\generator::phar, null, null, null)
							->once()
						->call('setMetadata')
							->withArguments(
								array(
									'version' => atoum\version,
									'author' => atoum\author,
									'support' => atoum\mail,
									'repository' => atoum\repository,
									'description' => $description,
									'licence' => $licence
								)
							)
							->once()
						->call('setStub')->withArguments($stub, null)->once()
						->call('buildFromIterator')
							->withArguments(new iterators\recursives\atoum\source($generator->getOriginDirectory(), '1'), null)
							->once()
						->call('setSignatureAlgorithm')
							->withArguments(\phar::SHA1, null)
							->once()
					->adapter($adapter)
						->call('unlink')->withArguments($directory . DIRECTORY_SEPARATOR . phar\generator::phar)->once()
		;
	}
}
