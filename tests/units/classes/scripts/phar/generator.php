<?php

namespace mageekguy\atoum\tests\units\scripts\phar;

use mageekguy\atoum;
use mageekguy\atoum\iterators;
use mageekguy\atoum\mock;
use mageekguy\atoum\mock\stream;
use mageekguy\atoum\scripts\phar;

require_once __DIR__ . '/../../../runner.php';

class generator extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\script::class);
    }

    public function testClassConstants()
    {
        $this->string(phar\generator::phar)->isEqualTo('atoum.phar');
    }

    public function test__construct()
    {
        $this
            ->if($adapter = new atoum\test\adapter())
            ->and($adapter->php_sapi_name = function () {
                return uniqid();
            })
            ->then
                ->exception(function () use (& $name, $adapter) {
                    new phar\generator($name = uniqid(), $adapter);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('\'' . $name . '\' must be used in CLI only')
            ->if($adapter->php_sapi_name = function () {
                return 'cli';
            })
            ->and($generator = new phar\generator($name = uniqid(), $adapter))
            ->then
                ->object($generator->getAdapter())->isIdenticalTo($adapter)
                ->object($generator->getLocale())->isInstanceOf(atoum\locale::class)
                ->object($generator->getOutputWriter())->isInstanceOf(atoum\writer::class)
                ->object($generator->getErrorWriter())->isInstanceOf(atoum\writer::class)
                ->string($generator->getName())->isEqualTo($name)
                ->variable($generator->getOriginDirectory())->isNull()
                ->variable($generator->getDestinationDirectory())->isNull()
                ->object($generator->getArgumentsParser())->isInstanceOf(atoum\script\arguments\parser::class)
        ;
    }

    public function testSetOriginDirectory()
    {
        $this
            ->if($adapter = new atoum\test\adapter())
            ->and($adapter->php_sapi_name = function () {
                return 'cli';
            })
            ->and($adapter->realpath = function ($path) {
                return $path;
            })
            ->and($generator = new phar\generator(uniqid(), $adapter))
            ->then
                ->exception(function () use ($generator) {
                    $generator->setOriginDirectory('');
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Empty origin directory is invalid')
            ->if($adapter->is_dir = function () {
                return false;
            })
            ->then
                ->exception(function () use ($generator, & $directory) {
                    $generator->setOriginDirectory($directory = uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Path \'' . $directory . '\' of origin directory is invalid')
            ->if($adapter->is_dir = function () {
                return true;
            })
            ->then
                ->object($generator->setOriginDirectory('/'))->isIdenticalTo($generator)
                ->string($generator->getOriginDirectory())->isEqualTo('/')
                ->object($generator->setOriginDirectory(($directory = uniqid()) . DIRECTORY_SEPARATOR))->isIdenticalTo($generator)
                ->string($generator->getOriginDirectory())->isEqualTo($directory)
            ->if($generator->setDestinationDirectory(uniqid()))
            ->then
                ->exception(function () use ($generator) {
                    $generator->setOriginDirectory($generator->getDestinationDirectory());
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Origin directory must be different from destination directory')
            ->if($realDirectory = $generator->getDestinationDirectory() . DIRECTORY_SEPARATOR . uniqid())
            ->and($adapter->realpath = function ($path) use ($realDirectory) {
                return $realDirectory;
            })
            ->then
                ->object($generator->setOriginDirectory('/'))->isIdenticalTo($generator)
                ->string($generator->getOriginDirectory())->isEqualTo($realDirectory)
        ;
    }

    public function testSetDestinationDirectory()
    {
        $this
            ->if($adapter = new atoum\test\adapter())
            ->and($adapter->php_sapi_name = function () {
                return 'cli';
            })
            ->and($adapter->realpath = function ($path) {
                return $path;
            })
            ->and($generator = new phar\generator(uniqid(), $adapter))
            ->then
                ->exception(function () use ($generator) {
                    $generator->setDestinationDirectory('');
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Empty destination directory is invalid')
            ->if($adapter->is_dir = function () {
                return false;
            })
            ->then
                ->exception(function () use ($generator, & $directory) {
                    $generator->setDestinationDirectory($directory = uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Path \'' . $directory . '\' of destination directory is invalid')
            ->if($adapter->is_dir = function () {
                return true;
            })
            ->then
                ->object($generator->setDestinationDirectory('/'))->isIdenticalTo($generator)
                ->string($generator->getDestinationDirectory())->isEqualTo('/')
                ->object($generator->setDestinationDirectory($directory = uniqid()))->isIdenticalTo($generator)
                ->string($generator->getDestinationDirectory())->isEqualTo($directory)
                ->object($generator->setDestinationDirectory(($directory = uniqid()) . DIRECTORY_SEPARATOR))->isIdenticalTo($generator)
                ->string($generator->getDestinationDirectory())->isEqualTo($directory)
            ->if($generator->setOriginDirectory(uniqid()))
            ->then
                ->exception(function () use ($generator) {
                    $generator->setDestinationDirectory($generator->getOriginDirectory());
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Destination directory must be different from origin directory')
        ;
    }

    public function testSetStubFile()
    {
        $this
            ->if($adapter = new atoum\test\adapter())
            ->and($adapter->php_sapi_name = function () {
                return 'cli';
            })
            ->and($adapter->realpath = function ($path) {
                return $path;
            })
            ->and($generator = new phar\generator(uniqid(), $adapter))
            ->then
                ->exception(function () use ($generator) {
                    $generator->setStubFile('');
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Stub file is invalid')
            ->if($adapter->is_file = function () {
                return false;
            })
            ->then
                ->exception(function () use ($generator) {
                    $generator->setStubFile(uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Stub file is not a valid file')
            ->if($adapter->is_file = function () {
                return true;
            })
            ->then
                ->object($generator->setStubFile($stubFile = uniqid()))->isIdenticalTo($generator)
                ->string($generator->getStubFile())->isEqualTo($stubFile)
        ;
    }

    public function testRun()
    {
        $this
            ->if
                ->extension('phar')->isLoaded()
            ->and($originDirectory = stream::get())
            ->and($originDirectory->opendir = true)
            ->and($adapter = new atoum\test\adapter())
            ->and($adapter->php_sapi_name = function () {
                return 'cli';
            })
            ->and($adapter->realpath = function ($path) {
                return $path;
            })
            ->and($adapter->is_dir = function () {
                return true;
            })
            ->and($adapter->is_file = function () {
                return true;
            })
            ->and($adapter->unlink = function () {
            })
            ->and($generator = new phar\generator(uniqid(), $adapter))
            ->then
                ->exception(function () use ($generator) {
                    $generator->run();
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Origin directory must be defined')
            ->if($generator->setOriginDirectory((string) $originDirectory))
            ->then
                ->exception(function () use ($generator) {
                    $generator->run();
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Destination directory must be defined')
            ->if($generator->setDestinationDirectory(uniqid()))
            ->then
                ->exception(function () use ($generator) {
                    $generator->run();
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Stub file must be defined')
            ->if($generator->setStubFile($stubFile = uniqid()))
            ->and($adapter->is_readable = function () {
                return false;
            })
            ->then
                ->exception(function () use ($generator) {
                    $generator->run();
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Origin directory \'' . $generator->getOriginDirectory() . '\' is not readable')
            ->if($adapter->is_readable = function ($path) use ($originDirectory) {
                return ($path === (string) $originDirectory);
            })
            ->and($adapter->is_writable = function () {
                return false;
            })
            ->then
                ->exception(function () use ($generator) {
                    $generator->run();
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Destination directory \'' . $generator->getDestinationDirectory() . '\' is not writable')
            ->if($adapter->is_writable = function () {
                return true;
            })
            ->then
                ->exception(function () use ($generator) {
                    $generator->run();
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Stub file \'' . $generator->getStubFile() . '\' is not readable')
            ->if($adapter->is_readable = function ($path) use ($originDirectory, $stubFile) {
                return ($path === (string) $originDirectory || $path === $stubFile);
            })
            ->and($generator->setPharFactory(function ($name) use (& $phar) {
                $pharController = new mock\controller();
                $pharController->__construct = function () {
                };
                $pharController->setStub = function () {
                };
                $pharController->setMetadata = function () {
                };
                $pharController->buildFromIterator = function () {
                };
                $pharController->setSignatureAlgorithm = function () {
                };
                $pharController->offsetGet = function () {
                };
                $pharController->offsetSet = function () {
                };

                return ($phar = new \mock\phar($name));
            }))
            ->and($adapter->file_get_contents = function ($file) {
                return false;
            })
            ->then
                ->exception(function () use ($generator) {
                    $generator->run();
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('ABOUT file is missing in \'' . $generator->getOriginDirectory() . '\'')
            ->if($adapter->file_get_contents = function ($file) use ($generator, & $description) {
                switch ($file) {
                    case $generator->getOriginDirectory() . DIRECTORY_SEPARATOR . 'ABOUT':
                        return ($description = uniqid());

                    default:
                        return false;
                }
            })
            ->then
                ->exception(function () use ($generator) {
                    $generator->run();
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('LICENSE file is missing in \'' . $generator->getOriginDirectory() . '\'')
            ->if($adapter->file_get_contents = function ($file) use ($generator, & $description, & $licence, & $stub) {
                switch ($file) {
                    case $generator->getOriginDirectory() . DIRECTORY_SEPARATOR . 'ABOUT':
                        return ($description = uniqid());

                    case $generator->getOriginDirectory() . DIRECTORY_SEPARATOR . 'LICENSE':
                        return ($licence = uniqid());

                    case $generator->getStubFile():
                        return ($stub = uniqid());

                    default:
                        return uniqid();
                }
            })
            ->then
                ->object($generator->run())->isIdenticalTo($generator)
                ->mock($phar)
                    ->call('__construct')->withArguments($generator->getDestinationDirectory() . DIRECTORY_SEPARATOR . atoum\scripts\phar\generator::phar, null, null)->once()
                    ->call('setMetadata')
                        ->withArguments(
                            [
                                'version' => atoum\version,
                                'author' => atoum\author,
                                'support' => atoum\mail,
                                'repository' => atoum\repository,
                                'description' => $description,
                                'licence' => $licence
                            ]
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
            ->and($superglobals->_SERVER = ['argv' => [uniqid(), '--help']])
            ->and($generator->setArgumentsParser(new atoum\script\arguments\parser($superglobals)))
            ->and($stdout = new \mock\mageekguy\atoum\writers\std\out())
            ->and($stdout->getMockController()->write = function () {
            })
            ->and($stderr = new \mock\mageekguy\atoum\writers\std\err())
            ->and($stderr->getMockController()->write = function () {
            })
            ->and($generator->setHelpWriter($stdout))
            ->and($generator->setErrorWriter($stderr))
            ->then
                ->object($generator->run())->isIdenticalTo($generator)
                ->mock($stdout)
                    ->call('write')->withArguments(sprintf($generator->getLocale()->_('Usage: %s [options]'), $generator->getName()))->once()
                    ->call('write')->withArguments($generator->getLocale()->_('Available options are:'))->once()
                    ->call('write')->withArguments('  -h, --help                               ' . $generator->getLocale()->_('Display this help'))->once()
                    ->call('write')->withArguments('  -d <directory>, --directory <directory>  ' . $generator->getLocale()->_('Destination directory <dir>'))->once()
            ->if($generator->setPharFactory(function ($name) use (& $phar) {
                $pharController = new mock\controller();
                $pharController->__construct = function () {
                };
                $pharController->setStub = function () {
                };
                $pharController->setMetadata = function () {
                };
                $pharController->buildFromIterator = function () {
                };
                $pharController->setSignatureAlgorithm = function () {
                };
                $pharController->offsetGet = function () {
                };
                $pharController->offsetSet = function () {
                };

                return ($phar = new \mock\phar($name));
            }))
            ->then
                ->object($generator->run(['-d', $directory = uniqid()]))->isIdenticalTo($generator)
                ->string($generator->getDestinationDirectory())->isEqualTo($directory)
                ->mock($phar)
                    ->call('__construct')
                        ->withArguments($generator->getDestinationDirectory() . DIRECTORY_SEPARATOR . atoum\scripts\phar\generator::phar, null, null)
                        ->once()
                    ->call('setMetadata')
                        ->withArguments(
                            [
                                'version' => atoum\version,
                                'author' => atoum\author,
                                'support' => atoum\mail,
                                'repository' => atoum\repository,
                                'description' => $description,
                                'licence' => $licence
                            ]
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
