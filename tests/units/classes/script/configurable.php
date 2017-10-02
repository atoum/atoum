<?php

namespace mageekguy\atoum\tests\units\script;

use mageekguy\atoum;
use mock\mageekguy\atoum\script\configurable as testedClass;

require_once __DIR__ . '/../../runner.php';

class configurable extends atoum\test
{
    public function testClass()
    {
        $this->testedClass
            ->isAbstract()
            ->extends(atoum\script::class)
        ;
    }

    public function testClassConstants()
    {
        $this->string(testedClass::defaultConfigFile)->isEqualTo('.config.php');
    }

    public function test__construct()
    {
        $this
            ->if($configurable = new testedClass($name = uniqid()))
            ->then
                ->string($configurable->getName())->isEqualTo($name)
                ->object($configurable->getAdapter())->isEqualTo(new atoum\adapter())
                ->object($configurable->getIncluder())->isInstanceOf(atoum\includer::class)
                ->array($configurable->getConfigFiles())->isEmpty()
                ->array($configurable->getHelp())->isEqualTo(
                    [
                        [
                            ['-h', '--help'],
                            null,
                            'Display this help'
                        ],
                        [
                            ['-c', '--configurations'],
                            '<file>...',
                            'Use all configuration files <file>'
                        ]
                    ]
                )
            ->if($configurable = new testedClass($name = uniqid(), $adapter = new atoum\adapter()))
            ->then
                ->string($configurable->getName())->isEqualTo($name)
                ->object($configurable->getAdapter())->isIdenticalTo($adapter)
                ->object($configurable->getIncluder())->isInstanceOf(atoum\includer::class)
                ->array($configurable->getConfigFiles())->isEmpty()
                ->array($configurable->getHelp())->isEqualTo(
                    [
                        [
                            ['-h', '--help'],
                            null,
                            'Display this help'
                        ],
                        [
                            ['-c', '--configurations'],
                            '<file>...',
                            'Use all configuration files <file>'
                        ]
                    ]
                )
        ;
    }

    public function testSetIncluder()
    {
        $this
            ->if($configurable = new testedClass(uniqid()))
            ->then
                ->object($configurable->setIncluder($includer = new atoum\includer()))->isIdenticalTo($configurable)
                ->object($configurable->getIncluder())->isIdenticalTo($includer)
                ->object($configurable->setIncluder())->isIdenticalTo($configurable)
                ->object($configurable->getIncluder())
                    ->isEqualTo(new atoum\includer())
                    ->isNotIdenticalTo($includer)
        ;
    }

    public function testUseConfigFile()
    {
        $this
            ->if($configurable = new testedClass(uniqid()))
            ->then
                ->exception(function () use ($configurable, & $file) {
                    $configurable->useConfigFile($file = uniqid());
                })
                    ->isInstanceOf(atoum\includer\exception::class)
                    ->hasMessage('Unable to find configuration file \'' . $file . '\'')
            ->if($includer = new \mock\mageekguy\atoum\includer())
            ->and($this->calling($includer)->includePath = function () {
            })
            ->and($configurable->setIncluder($includer))
            ->then
                ->object($configurable->useConfigFile($file = uniqid()))->isIdenticalTo($configurable)
                ->mock($includer)->call('includePath')->withArguments($file)->once()
                ->array($configurable->getConfigFiles())->isEqualTo([$file])
        ;
    }

    public function testUseDefaultConfigFiles()
    {
        $this
            ->if($configurable = new testedClass(uniqid()))
            ->and($this->calling($configurable)->useConfigFile = function () {
            })
            ->then
                ->object($configurable->useDefaultConfigFiles(atoum\directory))->isIdenticalTo($configurable)
                ->mock($configurable)
                    ->foreach(testedClass::getSubDirectoryPath(atoum\directory), function ($mock, $path) {
                        $mock->call('useConfigFile')->withArguments($path . testedClass::defaultConfigFile)->atLeastOnce();
                    })
            ->if($configurable = new testedClass(($directory = uniqid() . DIRECTORY_SEPARATOR . uniqid() . DIRECTORY_SEPARATOR . uniqid()) . DIRECTORY_SEPARATOR . uniqid()))
            ->and($this->calling($configurable)->useConfigFile = function () {
            })
            ->and($configurable->setAdapter($adapter = new atoum\test\adapter()))
            ->and($adapter->getcwd = $workingDirectory = uniqid() . DIRECTORY_SEPARATOR . uniqid() . DIRECTORY_SEPARATOR . uniqid())
            ->then
                ->object($configurable->useDefaultConfigFiles())->isIdenticalTo($configurable)
                ->mock($configurable)
                    ->foreach(testedClass::getSubDirectoryPath($workingDirectory), function ($mock, $path) {
                        $mock->call('useConfigFile')->withArguments($path . testedClass::defaultConfigFile)->atLeastOnce();
                    })
            ->and($adapter->getcwd = $otherWorkingDirectory = uniqid() . DIRECTORY_SEPARATOR . uniqid() . DIRECTORY_SEPARATOR . uniqid())
            ->and($this->calling($configurable)->useConfigFile->throw = new atoum\includer\exception())
            ->then
                ->object($configurable->useDefaultConfigFiles(uniqid()))->isIdenticalTo($configurable)
                ->mock($configurable)
                    ->foreach(testedClass::getSubDirectoryPath($otherWorkingDirectory), function ($mock, $path) {
                        $mock->call('useConfigFile')->withArguments($path . testedClass::defaultConfigFile)->atLeastOnce();
                    })
        ;
    }

    public function testGetSubDirectoryPath()
    {
        $this
            ->array(testedClass::getSubDirectoryPath(''))->isEmpty()
            ->array(testedClass::getSubDirectoryPath('', '/'))->isEmpty()
            ->array(testedClass::getSubDirectoryPath('', '\\'))->isEmpty()
            ->array(testedClass::getSubDirectoryPath('/', '/'))->isEqualTo(['/'])
            ->array(testedClass::getSubDirectoryPath('/foo', '/'))->isEqualTo(['/', '/foo/'])
            ->array(testedClass::getSubDirectoryPath('/foo/', '/'))->isEqualTo(['/', '/foo/'])
            ->array(testedClass::getSubDirectoryPath('/foo/bar', '/'))->isEqualTo(['/', '/foo/', '/foo/bar/'])
            ->array(testedClass::getSubDirectoryPath('/foo/bar/', '/'))->isEqualTo(['/', '/foo/', '/foo/bar/'])
            ->array(testedClass::getSubDirectoryPath('c:\\', '\\'))->isEqualTo(['c:\\'])
            ->array(testedClass::getSubDirectoryPath('c:\foo', '\\'))->isEqualTo(['c:\\', 'c:\foo\\'])
            ->array(testedClass::getSubDirectoryPath('c:\foo\\', '\\'))->isEqualTo(['c:\\', 'c:\foo\\'])
            ->array(testedClass::getSubDirectoryPath('c:\foo\bar', '\\'))->isEqualTo(['c:\\', 'c:\foo\\', 'c:\foo\bar\\'])
            ->array(testedClass::getSubDirectoryPath('c:\foo\bar\\', '\\'))->isEqualTo(['c:\\', 'c:\foo\\', 'c:\foo\bar\\'])
        ;
    }
}
