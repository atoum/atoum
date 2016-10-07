<?php

namespace mageekguy\atoum\tests\units\mock\stream;

use mageekguy\atoum;
use mageekguy\atoum\mock\stream;
use mageekguy\atoum\mock\stream\controller as testedClass;

require_once __DIR__ . '/../../../runner.php';

class controller extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass->isSubclassOf('mageekguy\atoum\test\adapter')
        ;
    }

    public function test__construct()
    {
        $this
            ->if($streamController = new testedClass($stream = uniqid()))
            ->then
                ->string($streamController->getPath())->isEqualTo($stream)
                ->variable($streamController->invoke('__construct'))->isNull()
                ->variable($streamController->invoke('dir_closedir'))->isNull()
                ->variable($streamController->invoke('dir_opendir'))->isNull()
                ->variable($streamController->invoke('dir_readdir'))->isNull()
                ->variable($streamController->invoke('dir_rewinddir'))->isNull()
                ->variable($streamController->invoke('mkdir'))->isNull()
                ->variable($streamController->invoke('rename'))->isNull()
                ->variable($streamController->invoke('rmdir'))->isNull()
                ->variable($streamController->invoke('stream_cast'))->isNull()
                ->variable($streamController->invoke('stream_close'))->isNull()
                ->variable($streamController->invoke('stream_eof'))->isNull()
                ->variable($streamController->invoke('stream_flush'))->isNull()
                ->variable($streamController->invoke('stream_lock'))->isNull()
                ->variable($streamController->invoke('stream_metadata'))->isNull()
                ->variable($streamController->invoke('stream_open'))->isNull()
                ->variable($streamController->invoke('stream_read'))->isNull()
                ->variable($streamController->invoke('stream_seek'))->isNull()
                ->variable($streamController->invoke('stream_set_option'))->isNull()
                ->variable($streamController->invoke('stream_stat'))->isNull()
                ->variable($streamController->invoke('stream_tell'))->isNull()
                ->variable($streamController->invoke('stream_write'))->isNull()
                ->variable($streamController->invoke('unlink'))->isNull()
                ->variable($streamController->invoke('url_stat'))->isNull()
        ;
    }

    public function test__toString()
    {
        $this
            ->if($streamController = new testedClass($stream = uniqid()))
            ->then
                ->castToString($streamController)->isEqualTo($stream)
        ;
    }

    public function test__get()
    {
        $this
            ->if($streamController = new testedClass(uniqid()))
            ->then
                ->object($streamController->__construct)->isEqualTo(new stream\invoker('__construct'))
                ->object($streamController->dir_closedir)->isEqualTo(new stream\invoker('dir_closedir'))
                ->object($streamController->closedir)->isEqualTo(new stream\invoker('dir_closedir'))
                ->object($streamController->dir_opendir)->isEqualTo(new stream\invoker('dir_opendir'))
                ->object($streamController->opendir)->isEqualTo(new stream\invoker('dir_opendir'))
                ->object($streamController->dir_readdir)->isEqualTo(new stream\invoker('dir_readdir'))
                ->object($streamController->readdir)->isEqualTo(new stream\invoker('dir_readdir'))
                ->object($streamController->dir_rewinddir)->isEqualTo(new stream\invoker('dir_rewinddir'))
                ->object($streamController->rewinddir)->isEqualTo(new stream\invoker('dir_rewinddir'))
                ->object($streamController->mkdir)->isEqualTo(new stream\invoker('mkdir'))
                ->object($streamController->rename)->isEqualTo(new stream\invoker('rename'))
                ->object($streamController->rmdir)->isEqualTo(new stream\invoker('rmdir'))
                ->object($streamController->stream_cast)->isEqualTo(new stream\invoker('stream_cast'))
                ->object($streamController->select)->isEqualTo(new stream\invoker('stream_cast'))
                ->object($streamController->stream_close)->isEqualTo(new stream\invoker('stream_close'))
                ->object($streamController->fclose)->isEqualTo(new stream\invoker('stream_close'))
                ->object($streamController->stream_eof)->isEqualTo(new stream\invoker('stream_eof'))
                ->object($streamController->feof)->isEqualTo(new stream\invoker('stream_eof'))
                ->object($streamController->stream_flush)->isEqualTo(new stream\invoker('stream_flush'))
                ->object($streamController->fflush)->isEqualTo(new stream\invoker('stream_flush'))
                ->object($streamController->stream_lock)->isEqualTo(new stream\invoker('stream_lock'))
                ->object($streamController->flock)->isEqualTo(new stream\invoker('stream_lock'))
                ->object($streamController->stream_metadata)->isEqualTo(new stream\invoker('stream_metadata'))
                ->object($streamController->touch)->isEqualTo(new stream\invoker('stream_metadata'))
                ->object($streamController->chmod)->isEqualTo(new stream\invoker('stream_metadata'))
                ->object($streamController->chown)->isEqualTo(new stream\invoker('stream_metadata'))
                ->object($streamController->chgrp)->isEqualTo(new stream\invoker('stream_metadata'))
                ->object($streamController->stream_open)->isEqualTo(new stream\invoker('stream_open'))
                ->object($streamController->fopen)->isEqualTo(new stream\invoker('stream_open'))
                ->object($streamController->stream_read)->isEqualTo(new stream\invoker('stream_read'))
                ->object($streamController->fread)->isEqualTo(new stream\invoker('stream_read'))
                ->object($streamController->stream_seek)->isEqualTo(new stream\invoker('stream_seek'))
                ->object($streamController->fseek)->isEqualTo(new stream\invoker('stream_seek'))
                ->object($streamController->stream_set_option)->isEqualTo(new stream\invoker('stream_set_option'))
                ->object($streamController->stream_stat)->isEqualTo(new stream\invoker('stream_stat'))
                ->object($streamController->fstat)->isEqualTo(new stream\invoker('stream_stat'))
                ->object($streamController->stream_tell)->isEqualTo(new stream\invoker('stream_tell'))
                ->object($streamController->ftell)->isEqualTo(new stream\invoker('stream_tell'))
                ->object($streamController->stream_write)->isEqualTo(new stream\invoker('stream_write'))
                ->object($streamController->fwrite)->isEqualTo(new stream\invoker('stream_write'))
                ->object($streamController->unlink)->isEqualTo(new stream\invoker('unlink'))
                ->object($streamController->url_stat)->isEqualTo(new stream\invoker('url_stat'))
                ->object($streamController->stat)->isEqualTo(new stream\invoker('url_stat'))
            ->if($method = uniqid())
            ->then
                ->exception(function () use ($streamController, $method) {
                    $streamController->{$method};
                }
                    )
                        ->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
                        ->hasMessage('Method streamWrapper::' . $method . '() does not exist')
        ;
    }

    public function test__set()
    {
        $this
            ->if($streamController = new testedClass(uniqid()))
            ->and($streamController->__construct = $__construct = uniqid())
            ->then
                ->string($streamController->invoke('__construct'))->isEqualTo($__construct)
            ->if($streamController->dir_closedir = $dir_closedir = uniqid())
            ->then
                ->string($streamController->invoke('dir_closedir'))->isEqualTo($dir_closedir)
            ->if($streamController->closedir = $closedir = uniqid())
            ->then
                ->string($streamController->invoke('closedir'))->isEqualTo($closedir)
            ->if($streamController->dir_opendir = $dir_opendir = uniqid())
            ->then
                ->string($streamController->invoke('dir_opendir'))->isEqualTo($dir_opendir)
            ->if($streamController->opendir = $opendir = uniqid())
            ->then
                ->string($streamController->invoke('opendir'))->isEqualTo($opendir)
            ->if($streamController->dir_readdir = $dir_readdir = uniqid())
            ->then
                ->string($streamController->invoke('dir_readdir'))->isEqualTo($dir_readdir)
            ->if($streamController->readdir = $readdir = uniqid())
            ->then
                ->string($streamController->invoke('readdir'))->isEqualTo($readdir)
            ->if($streamController->dir_rewinddir = $dir_rewinddir = uniqid())
            ->then
                ->string($streamController->invoke('dir_rewinddir'))->isEqualTo($dir_rewinddir)
            ->if($streamController->rewinddir = $rewinddir = uniqid())
            ->then
                ->string($streamController->invoke('rewinddir'))->isEqualTo($rewinddir)
            ->if($streamController->mkdir = $mkdir = uniqid())
            ->then
                ->string($streamController->invoke('mkdir'))->isEqualTo($mkdir)
            ->if($streamController->rename = $rename = uniqid())
            ->then
                ->string($streamController->invoke('rename'))->isEqualTo($rename)
            ->if($streamController->rmdir = $rmdir = uniqid())
            ->then
                ->string($streamController->invoke('rmdir'))->isEqualTo($rmdir)
            ->if($streamController->stream_cast = $stream_cast = uniqid())
            ->then
                ->string($streamController->invoke('stream_cast'))->isEqualTo($stream_cast)
            ->if($streamController->select = $select = uniqid())
            ->then
                ->string($streamController->invoke('select'))->isEqualTo($select)
            ->if($streamController->stream_close = $stream_close = uniqid())
            ->then
                ->string($streamController->invoke('stream_close'))->isEqualTo($stream_close)
            ->if($streamController->fclose = $fclose = uniqid())
            ->then
                ->string($streamController->invoke('fclose'))->isEqualTo($fclose)
            ->if($streamController->stream_eof = $stream_eof = uniqid())
            ->then
                ->string($streamController->invoke('stream_eof'))->isEqualTo($stream_eof)
            ->if($streamController->feof = $feof = uniqid())
            ->then
                ->string($streamController->invoke('feof'))->isEqualTo($feof)
            ->if($streamController->stream_flush = $stream_flush = uniqid())
            ->then
                ->string($streamController->invoke('stream_flush'))->isEqualTo($stream_flush)
            ->if($streamController->fflush = $fflush = uniqid())
            ->then
                ->string($streamController->invoke('fflush'))->isEqualTo($fflush)
            ->if($streamController->stream_lock = $stream_lock = uniqid())
            ->then
                ->string($streamController->invoke('stream_lock'))->isEqualTo($stream_lock)
            ->if($streamController->flock = $flock = uniqid())
            ->then
                ->string($streamController->invoke('flock'))->isEqualTo($flock)
            ->if($streamController->stream_metadata = $stream_metadata = uniqid())
            ->then
                ->string($streamController->invoke('stream_metadata'))->isEqualTo($stream_metadata)
            ->if($streamController->touch = $touch = uniqid())
            ->then
                ->string($streamController->invoke('touch'))->isEqualTo($touch)
            ->if($streamController->chmod = $chmod = uniqid())
            ->then
                ->string($streamController->invoke('chmod'))->isEqualTo($chmod)
            ->if($streamController->chown = $chown = uniqid())
            ->then
                ->string($streamController->invoke('chown'))->isEqualTo($chown)
            ->if($streamController->chgrp = $chgrp = uniqid())
            ->then
                ->string($streamController->invoke('chgrp'))->isEqualTo($chgrp)
            ->if($streamController->stream_open = $stream_open = uniqid())
            ->then
                ->string($streamController->invoke('stream_open'))->isEqualTo($stream_open)
            ->if($streamController->fopen = $fopen = uniqid())
            ->then
                ->string($streamController->invoke('fopen'))->isEqualTo($fopen)
            ->if($streamController->stream_read = $stream_read = uniqid())
            ->then
                ->string($streamController->invoke('stream_read'))->isEqualTo($stream_read)
            ->if($streamController->fread = $fread = uniqid())
            ->then
                ->string($streamController->invoke('fread'))->isEqualTo($fread)
            ->if($streamController->stream_seek = $stream_seek = uniqid())
            ->then
                ->string($streamController->invoke('stream_seek'))->isEqualTo($stream_seek)
            ->if($streamController->fseek = $fseek = uniqid())
            ->then
                ->string($streamController->invoke('fseek'))->isEqualTo($fseek)
            ->if($streamController->stream_set_option = $stream_set_option = uniqid())
            ->then
                ->string($streamController->invoke('stream_set_option'))->isEqualTo($stream_set_option)
            ->if($streamController->stream_stat = $stream_stat = uniqid())
            ->then
                ->string($streamController->invoke('stream_stat'))->isEqualTo($stream_stat)
            ->if($streamController->fstat = $fstat = uniqid())
            ->then
                ->string($streamController->invoke('fstat'))->isEqualTo($fstat)
            ->if($streamController->stream_tell = $stream_tell = uniqid())
            ->then
                ->string($streamController->invoke('stream_tell'))->isEqualTo($stream_tell)
            ->if($streamController->ftell = $ftell = uniqid())
            ->then
                ->string($streamController->invoke('ftell'))->isEqualTo($ftell)
            ->if($streamController->stream_write = $stream_write = uniqid())
            ->then
                ->string($streamController->invoke('stream_write'))->isEqualTo($stream_write)
            ->if($streamController->fwrite = $fwrite = uniqid())
            ->then
                ->string($streamController->invoke('fwrite'))->isEqualTo($fwrite)
            ->if($streamController->unlink = $unlink = uniqid())
            ->then
                ->string($streamController->invoke('unlink'))->isEqualTo($unlink)
            ->if($streamController->url_stat = $url_stat = uniqid())
            ->then
                ->string($streamController->invoke('url_stat'))->isEqualTo($url_stat)
            ->if($streamController->stat = $stat = uniqid())
            ->then
                ->string($streamController->invoke('stat'))->isEqualTo($stat)
            ->if($streamController->resetCalls()->file_get_contents = $contents = uniqid())
            ->then
                ->boolean($streamController->invoke('fopen'))->isTrue()
                ->string($streamController->invoke('fread'))->isEqualTo($contents)
                ->boolean($streamController->invoke('fread'))->isFalse()
                ->boolean($streamController->invoke('fclose'))->isTrue()
            ->if($streamController->resetCalls()->file_put_contents = true)
            ->then
                ->boolean($streamController->invoke('fopen'))->isTrue()
                ->boolean($streamController->invoke('fwrite'))->isTrue()
                ->boolean($streamController->invoke('fclose'))->isTrue()
            ->if($streamController->resetCalls()->file_put_contents = false)
            ->then
                ->boolean($streamController->invoke('fopen'))->isTrue()
                ->boolean($streamController->invoke('fwrite'))->isFalse()
                ->boolean($streamController->invoke('fclose'))->isTrue()
            ->if($method = uniqid())
            ->then
                ->exception(function () use ($streamController, $method) {
                    $streamController->{$method} = uniqid();
                }
                    )
                        ->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
                        ->hasMessage('Method streamWrapper::' . $method . '() does not exist')
        ;
    }

    public function test__isset()
    {
        $this
            ->if($streamController = new testedClass(uniqid()))
            ->then
                ->boolean(isset($streamController->__construct))->isFalse()
                ->boolean(isset($streamController->dir_closedir))->isFalse()
                ->boolean(isset($streamController->closedir))->isFalse()
                ->boolean(isset($streamController->dir_opendir))->isFalse()
                ->boolean(isset($streamController->opendir))->isFalse()
                ->boolean(isset($streamController->dir_readdir))->isFalse()
                ->boolean(isset($streamController->readdir))->isFalse()
                ->boolean(isset($streamController->dir_rewinddir))->isFalse()
                ->boolean(isset($streamController->rewinddir))->isFalse()
                ->boolean(isset($streamController->mkdir))->isFalse()
                ->boolean(isset($streamController->rename))->isFalse()
                ->boolean(isset($streamController->rmdir))->isFalse()
                ->boolean(isset($streamController->stream_cast))->isFalse()
                ->boolean(isset($streamController->select))->isFalse()
                ->boolean(isset($streamController->stream_close))->isFalse()
                ->boolean(isset($streamController->fclose))->isFalse()
                ->boolean(isset($streamController->stream_eof))->isFalse()
                ->boolean(isset($streamController->feof))->isFalse()
                ->boolean(isset($streamController->stream_flush))->isFalse()
                ->boolean(isset($streamController->fflush))->isFalse()
                ->boolean(isset($streamController->stream_lock))->isFalse()
                ->boolean(isset($streamController->flock))->isFalse()
                ->boolean(isset($streamController->stream_metadata))->isFalse()
                ->boolean(isset($streamController->touch))->isFalse()
                ->boolean(isset($streamController->chmod))->isFalse()
                ->boolean(isset($streamController->chown))->isFalse()
                ->boolean(isset($streamController->chgrp))->isFalse()
                ->boolean(isset($streamController->stream_open))->isFalse()
                ->boolean(isset($streamController->fopen))->isFalse()
                ->boolean(isset($streamController->stream_read))->isFalse()
                ->boolean(isset($streamController->fread))->isFalse()
                ->boolean(isset($streamController->stream_seek))->isFalse()
                ->boolean(isset($streamController->fseek))->isFalse()
                ->boolean(isset($streamController->stream_set_option))->isFalse()
                ->boolean(isset($streamController->stream_stat))->isFalse()
                ->boolean(isset($streamController->fstat))->isFalse()
                ->boolean(isset($streamController->stream_tell))->isFalse()
                ->boolean(isset($streamController->ftell))->isFalse()
                ->boolean(isset($streamController->stream_write))->isFalse()
                ->boolean(isset($streamController->fwrite))->isFalse()
                ->boolean(isset($streamController->unlink))->isFalse()
                ->boolean(isset($streamController->url_stat))->isFalse()
                ->boolean(isset($streamController->stat))->isFalse()
            ->if($streamController->__construct = uniqid())
            ->and($streamController->dir_closedir = uniqid())
            ->and($streamController->closedir = uniqid())
            ->and($streamController->dir_opendir = uniqid())
            ->and($streamController->opendir = uniqid())
            ->and($streamController->dir_readdir = uniqid())
            ->and($streamController->readdir = uniqid())
            ->and($streamController->dir_rewinddir = uniqid())
            ->and($streamController->rewinddir = uniqid())
            ->and($streamController->mkdir = uniqid())
            ->and($streamController->rename = uniqid())
            ->and($streamController->rmdir = uniqid())
            ->and($streamController->stream_cast = uniqid())
            ->and($streamController->select = uniqid())
            ->and($streamController->stream_close = uniqid())
            ->and($streamController->fclose = uniqid())
            ->and($streamController->stream_eof = uniqid())
            ->and($streamController->feof = uniqid())
            ->and($streamController->stream_flush = uniqid())
            ->and($streamController->fflush = uniqid())
            ->and($streamController->stream_lock = uniqid())
            ->and($streamController->flock = uniqid())
            ->and($streamController->stream_metadata = uniqid())
            ->and($streamController->touch = uniqid())
            ->and($streamController->chown = uniqid())
            ->and($streamController->chmod = uniqid())
            ->and($streamController->chgrp = uniqid())
            ->and($streamController->stream_open = uniqid())
            ->and($streamController->fopen = uniqid())
            ->and($streamController->stream_read = uniqid())
            ->and($streamController->fread = uniqid())
            ->and($streamController->stream_seek = uniqid())
            ->and($streamController->fseek = uniqid())
            ->and($streamController->stream_set_option = uniqid())
            ->and($streamController->stream_stat = uniqid())
            ->and($streamController->fstat = uniqid())
            ->and($streamController->stream_tell = uniqid())
            ->and($streamController->ftell = uniqid())
            ->and($streamController->stream_write = uniqid())
            ->and($streamController->fwrite = uniqid())
            ->and($streamController->unlink = uniqid())
            ->and($streamController->url_stat = uniqid())
            ->and($streamController->stat = uniqid())
            ->then
                ->boolean(isset($streamController->__construct))->isTrue()
                ->boolean(isset($streamController->dir_closedir))->isTrue()
                ->boolean(isset($streamController->closedir))->isTrue()
                ->boolean(isset($streamController->dir_opendir))->isTrue()
                ->boolean(isset($streamController->opendir))->isTrue()
                ->boolean(isset($streamController->dir_readdir))->isTrue()
                ->boolean(isset($streamController->readdir))->isTrue()
                ->boolean(isset($streamController->dir_rewinddir))->isTrue()
                ->boolean(isset($streamController->rewinddir))->isTrue()
                ->boolean(isset($streamController->mkdir))->isTrue()
                ->boolean(isset($streamController->rename))->isTrue()
                ->boolean(isset($streamController->rmdir))->isTrue()
                ->boolean(isset($streamController->stream_cast))->isTrue()
                ->boolean(isset($streamController->select))->isTrue()
                ->boolean(isset($streamController->stream_close))->isTrue()
                ->boolean(isset($streamController->fclose))->isTrue()
                ->boolean(isset($streamController->stream_eof))->isTrue()
                ->boolean(isset($streamController->feof))->isTrue()
                ->boolean(isset($streamController->stream_flush))->isTrue()
                ->boolean(isset($streamController->fflush))->isTrue()
                ->boolean(isset($streamController->stream_lock))->isTrue()
                ->boolean(isset($streamController->flock))->isTrue()
                ->boolean(isset($streamController->stream_metadata))->isTrue()
                ->boolean(isset($streamController->touch))->isTrue()
                ->boolean(isset($streamController->chmod))->isTrue()
                ->boolean(isset($streamController->chown))->isTrue()
                ->boolean(isset($streamController->chgrp))->isTrue()
                ->boolean(isset($streamController->stream_open))->isTrue()
                ->boolean(isset($streamController->fopen))->isTrue()
                ->boolean(isset($streamController->stream_read))->isTrue()
                ->boolean(isset($streamController->fread))->isTrue()
                ->boolean(isset($streamController->stream_seek))->isTrue()
                ->boolean(isset($streamController->fseek))->isTrue()
                ->boolean(isset($streamController->stream_set_option))->isTrue()
                ->boolean(isset($streamController->stream_stat))->isTrue()
                ->boolean(isset($streamController->fstat))->isTrue()
                ->boolean(isset($streamController->stream_tell))->isTrue()
                ->boolean(isset($streamController->ftell))->isTrue()
                ->boolean(isset($streamController->stream_write))->isTrue()
                ->boolean(isset($streamController->fwrite))->isTrue()
                ->boolean(isset($streamController->unlink))->isTrue()
                ->boolean(isset($streamController->url_stat))->isTrue()
                ->boolean(isset($streamController->stat))->isTrue()
            ->if($method = uniqid())
            ->then
                ->exception(function () use ($streamController, $method) {
                    isset($streamController->{$method});
                }
                    )
                        ->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
                        ->hasMessage('Method streamWrapper::' . $method . '() does not exist')
        ;
    }

    public function test__unset()
    {
        $this
            ->if($streamController = new testedClass(uniqid()))
            ->then
                ->boolean(isset($streamController->__construct))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->__construct);
            })
                ->boolean(isset($streamController->__construct))->isFalse()
                ->boolean(isset($streamController->dir_closedir))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->dir_closedir);
            })
                ->boolean(isset($streamController->dir_closedir))->isFalse()
                ->boolean(isset($streamController->closedir))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->closedir);
            })
                ->boolean(isset($streamController->closedir))->isFalse()
                ->boolean(isset($streamController->dir_opendir))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->dir_opendir);
            })
                ->boolean(isset($streamController->dir_opendir))->isFalse()
                ->boolean(isset($streamController->opendir))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->opendir);
            })
                ->boolean(isset($streamController->opendir))->isFalse()
                ->boolean(isset($streamController->dir_readdir))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->dir_readdir);
            })
                ->boolean(isset($streamController->dir_readdir))->isFalse()
                ->boolean(isset($streamController->readdir))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->readdir);
            })
                ->boolean(isset($streamController->readdir))->isFalse()
                ->boolean(isset($streamController->dir_rewinddir))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->dir_rewinddir);
            })
                ->boolean(isset($streamController->dir_rewinddir))->isFalse()
                ->boolean(isset($streamController->rewinddir))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->rewinddir);
            })
                ->boolean(isset($streamController->rewinddir))->isFalse()
                ->boolean(isset($streamController->mkdir))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->mkdir);
            })
                ->boolean(isset($streamController->mkdir))->isFalse()
                ->boolean(isset($streamController->rename))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->rename);
            })
                ->boolean(isset($streamController->rename))->isFalse()
                ->boolean(isset($streamController->rmdir))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->rmdir);
            })
                ->boolean(isset($streamController->rmdir))->isFalse()
                ->boolean(isset($streamController->stream_cast))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_cast);
            })
                ->boolean(isset($streamController->stream_cast))->isFalse()
                ->boolean(isset($streamController->select))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->select);
            })
                ->boolean(isset($streamController->select))->isFalse()
                ->boolean(isset($streamController->stream_close))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_close);
            })
                ->boolean(isset($streamController->stream_close))->isFalse()
                ->boolean(isset($streamController->fclose))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->fclose);
            })
                ->boolean(isset($streamController->fclose))->isFalse()
                ->boolean(isset($streamController->stream_eof))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_eof);
            })
                ->boolean(isset($streamController->stream_eof))->isFalse()
                ->boolean(isset($streamController->feof))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->feof);
            })
                ->boolean(isset($streamController->feof))->isFalse()
                ->boolean(isset($streamController->stream_flush))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_flush);
            })
                ->boolean(isset($streamController->stream_flush))->isFalse()
                ->boolean(isset($streamController->fflush))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->fflush);
            })
                ->boolean(isset($streamController->fflush))->isFalse()
                ->boolean(isset($streamController->stream_lock))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_lock);
            })
                ->boolean(isset($streamController->stream_lock))->isFalse()
                ->boolean(isset($streamController->flock))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->flock);
            })
                ->boolean(isset($streamController->flock))->isFalse()
                ->boolean(isset($streamController->stream_metadata))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_metadata);
            })
                ->boolean(isset($streamController->stream_metadata))->isFalse()
                ->boolean(isset($streamController->touch))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->touch);
            })
                ->boolean(isset($streamController->touch))->isFalse()
                ->boolean(isset($streamController->chmod))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->chmod);
            })
                ->boolean(isset($streamController->chmod))->isFalse()
                ->boolean(isset($streamController->chown))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->chown);
            })
                ->boolean(isset($streamController->chown))->isFalse()
                ->boolean(isset($streamController->chgrp))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->chgrp);
            })
                ->boolean(isset($streamController->chgrp))->isFalse()
                ->boolean(isset($streamController->stream_open))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_open);
            })
                ->boolean(isset($streamController->stream_open))->isFalse()
                ->boolean(isset($streamController->fopen))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->fopen);
            })
                ->boolean(isset($streamController->fopen))->isFalse()
                ->boolean(isset($streamController->stream_read))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_read);
            })
                ->boolean(isset($streamController->stream_read))->isFalse()
                ->boolean(isset($streamController->fread))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->fread);
            })
                ->boolean(isset($streamController->fread))->isFalse()
                ->boolean(isset($streamController->stream_seek))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_seek);
            })
                ->boolean(isset($streamController->stream_seek))->isFalse()
                ->boolean(isset($streamController->fseek))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->fseek);
            })
                ->boolean(isset($streamController->fseek))->isFalse()
                ->boolean(isset($streamController->stream_set_option))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_set_option);
            })
                ->boolean(isset($streamController->stream_set_option))->isFalse()
                ->boolean(isset($streamController->stream_stat))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_stat);
            })
                ->boolean(isset($streamController->stream_stat))->isFalse()
                ->boolean(isset($streamController->fstat))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->fstat);
            })
                ->boolean(isset($streamController->fstat))->isFalse()
                ->boolean(isset($streamController->stream_tell))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_tell);
            })
                ->boolean(isset($streamController->stream_tell))->isFalse()
                ->boolean(isset($streamController->ftell))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->ftell);
            })
                ->boolean(isset($streamController->ftell))->isFalse()
                ->boolean(isset($streamController->stream_write))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_write);
            })
                ->boolean(isset($streamController->stream_write))->isFalse()
                ->boolean(isset($streamController->fwrite))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->fwrite);
            })
                ->boolean(isset($streamController->fwrite))->isFalse()
                ->boolean(isset($streamController->unlink))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->unlink);
            })
                ->boolean(isset($streamController->unlink))->isFalse()
                ->boolean(isset($streamController->url_stat))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->url_stat);
            })
                ->boolean(isset($streamController->url_stat))->isFalse()
                ->boolean(isset($streamController->stat))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stat);
            })
                ->boolean(isset($streamController->stat))->isFalse()
            ->if($streamController->__construct = uniqid())
            ->and($streamController->dir_closedir = uniqid())
            ->and($streamController->closedir = uniqid())
            ->and($streamController->dir_opendir = uniqid())
            ->and($streamController->opendir = uniqid())
            ->and($streamController->dir_readdir = uniqid())
            ->and($streamController->readdir = uniqid())
            ->and($streamController->dir_rewinddir = uniqid())
            ->and($streamController->rewinddir = uniqid())
            ->and($streamController->mkdir = uniqid())
            ->and($streamController->rename = uniqid())
            ->and($streamController->rmdir = uniqid())
            ->and($streamController->stream_cast = uniqid())
            ->and($streamController->select = uniqid())
            ->and($streamController->stream_close = uniqid())
            ->and($streamController->fclose = uniqid())
            ->and($streamController->stream_eof = uniqid())
            ->and($streamController->feof = uniqid())
            ->and($streamController->stream_flush = uniqid())
            ->and($streamController->fflush = uniqid())
            ->and($streamController->stream_lock = uniqid())
            ->and($streamController->flock = uniqid())
            ->and($streamController->stream_metadata = uniqid())
            ->and($streamController->touch = uniqid())
            ->and($streamController->chown = uniqid())
            ->and($streamController->chmod = uniqid())
            ->and($streamController->chgrp = uniqid())
            ->and($streamController->stream_open = uniqid())
            ->and($streamController->fopen = uniqid())
            ->and($streamController->stream_read = uniqid())
            ->and($streamController->fread = uniqid())
            ->and($streamController->stream_seek = uniqid())
            ->and($streamController->fseek = uniqid())
            ->and($streamController->stream_set_option = uniqid())
            ->and($streamController->stream_stat = uniqid())
            ->and($streamController->fstat = uniqid())
            ->and($streamController->stream_tell = uniqid())
            ->and($streamController->ftell = uniqid())
            ->and($streamController->stream_write = uniqid())
            ->and($streamController->fwrite = uniqid())
            ->and($streamController->unlink = uniqid())
            ->and($streamController->url_stat = uniqid())
            ->and($streamController->stat = uniqid())
            ->then
                ->boolean(isset($streamController->__construct))->isTrue()
            ->when(function () use ($streamController) {
                unset($streamController->__construct);
            })
                ->boolean(isset($streamController->__construct))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->dir_closedir);
            })
                ->boolean(isset($streamController->dir_closedir))->isFalse()
                ->boolean(isset($streamController->closedir))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->dir_opendir);
            })
                ->boolean(isset($streamController->dir_opendir))->isFalse()
                ->boolean(isset($streamController->opendir))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->dir_readdir);
            })
                ->boolean(isset($streamController->dir_readdir))->isFalse()
                ->boolean(isset($streamController->readdir))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->dir_rewinddir);
            })
                ->boolean(isset($streamController->dir_rewinddir))->isFalse()
                ->boolean(isset($streamController->rewinddir))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->mkdir);
            })
                ->boolean(isset($streamController->mkdir))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->rename);
            })
                ->boolean(isset($streamController->rename))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->rmdir);
            })
                ->boolean(isset($streamController->rmdir))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_cast);
            })
                ->boolean(isset($streamController->stream_cast))->isFalse()
                ->boolean(isset($streamController->select))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_close);
            })
                ->boolean(isset($streamController->stream_close))->isFalse()
                ->boolean(isset($streamController->fclose))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_eof);
            })
                ->boolean(isset($streamController->stream_eof))->isFalse()
                ->boolean(isset($streamController->feof))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_flush);
            })
                ->boolean(isset($streamController->stream_flush))->isFalse()
                ->boolean(isset($streamController->fflush))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_lock);
            })
                ->boolean(isset($streamController->stream_lock))->isFalse()
                ->boolean(isset($streamController->flock))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_metadata);
            })
                ->boolean(isset($streamController->stream_metadata))->isFalse()
                ->boolean(isset($streamController->touch))->isFalse()
                ->boolean(isset($streamController->chmod))->isFalse()
                ->boolean(isset($streamController->chown))->isFalse()
                ->boolean(isset($streamController->chgrp))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_open);
            })
                ->boolean(isset($streamController->stream_open))->isFalse()
                ->boolean(isset($streamController->fopen))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_read);
            })
                ->boolean(isset($streamController->stream_read))->isFalse()
                ->boolean(isset($streamController->fread))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_seek);
            })
                ->boolean(isset($streamController->stream_seek))->isFalse()
                ->boolean(isset($streamController->fseek))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_set_option);
            })
                ->boolean(isset($streamController->stream_set_option))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_stat);
            })
                ->boolean(isset($streamController->stream_stat))->isFalse()
                ->boolean(isset($streamController->fstat))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_tell);
            })
                ->boolean(isset($streamController->stream_tell))->isFalse()
                ->boolean(isset($streamController->ftell))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->stream_write);
            })
                ->boolean(isset($streamController->stream_write))->isFalse()
                ->boolean(isset($streamController->fwrite))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->unlink);
            })
                ->boolean(isset($streamController->unlink))->isFalse()
            ->when(function () use ($streamController) {
                unset($streamController->url_stat);
            })
                ->boolean(isset($streamController->url_stat))->isFalse()
                ->boolean(isset($streamController->stat))->isFalse()
            ->if($method = uniqid())
            ->then
            ->exception(function () use ($streamController, $method) {
                unset($streamController->{$method});
            }
                )
                    ->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
                    ->hasMessage('Method streamWrapper::' . $method . '() does not exist')
        ;
    }

    public function testSetPath()
    {
        $this
            ->if($streamController = new testedClass(uniqid()))
            ->then
                ->object($streamController->setPath($newName = uniqid()))->isIdenticalTo($streamController)
                ->string($streamController->getPath())->isEqualTo($newName)
        ;
    }

    public function testGetBasename()
    {
        $this
            ->if($streamController = new testedClass($basename = uniqid()))
            ->then
                ->string($streamController->getBasename())->isEqualTo($basename)
            ->if($streamController = new testedClass(uniqid() . '://' . ($basename = uniqid())))
            ->then
                ->string($streamController->getBasename())->isEqualTo($basename)
            ->if($streamController = new testedClass(uniqid() . '://' . uniqid() . DIRECTORY_SEPARATOR . ($basename = uniqid())))
            ->then
                ->string($streamController->getBasename())->isEqualTo($basename)
        ;
    }

    public function testInvoke()
    {
        $this
            ->if($streamController = new testedClass(uniqid()))
            ->then
                ->variable($streamController->invoke('__construct'))->isNull()
                ->variable($streamController->invoke('dir_closedir'))->isNull()
                ->variable($streamController->invoke('closedir'))->isNull()
                ->variable($streamController->invoke('dir_opendir'))->isNull()
                ->variable($streamController->invoke('opendir'))->isNull()
                ->variable($streamController->invoke('dir_readdir'))->isNull()
                ->variable($streamController->invoke('readdir'))->isNull()
                ->variable($streamController->invoke('dir_rewinddir'))->isNull()
                ->variable($streamController->invoke('rewinddir'))->isNull()
                ->variable($streamController->invoke('mkdir'))->isNull()
                ->variable($streamController->invoke('rename'))->isNull()
                ->variable($streamController->invoke('rmdir'))->isNull()
                ->variable($streamController->invoke('stream_cast'))->isNull()
                ->variable($streamController->invoke('select'))->isNull()
                ->variable($streamController->invoke('stream_close'))->isNull()
                ->variable($streamController->invoke('fclose'))->isNull()
                ->variable($streamController->invoke('stream_eof'))->isNull()
                ->variable($streamController->invoke('feof'))->isNull()
                ->variable($streamController->invoke('stream_flush'))->isNull()
                ->variable($streamController->invoke('fflush'))->isNull()
                ->variable($streamController->invoke('stream_lock'))->isNull()
                ->variable($streamController->invoke('flock'))->isNull()
                ->variable($streamController->invoke('stream_metadata'))->isNull()
                ->variable($streamController->invoke('touch'))->isNull()
                ->variable($streamController->invoke('chown'))->isNull()
                ->variable($streamController->invoke('chmod'))->isNull()
                ->variable($streamController->invoke('chgrp'))->isNull()
                ->variable($streamController->invoke('stream_open'))->isNull()
                ->variable($streamController->invoke('fopen'))->isNull()
                ->variable($streamController->invoke('stream_read'))->isNull()
                ->variable($streamController->invoke('fread'))->isNull()
                ->variable($streamController->invoke('stream_seek'))->isNull()
                ->variable($streamController->invoke('fseek'))->isNull()
                ->variable($streamController->invoke('stream_set_option'))->isNull()
                ->variable($streamController->invoke('stream_stat'))->isNull()
                ->variable($streamController->invoke('fstat'))->isNull()
                ->variable($streamController->invoke('stream_tell'))->isNull()
                ->variable($streamController->invoke('ftell'))->isNull()
                ->variable($streamController->invoke('stream_write'))->isNull()
                ->variable($streamController->invoke('fwrite'))->isNull()
                ->variable($streamController->invoke('unlink'))->isNull()
                ->variable($streamController->invoke('url_stat'))->isNull()
                ->variable($streamController->invoke('stat'))->isNull()
            ->if($method = uniqid())
            ->then
                ->exception(function () use ($streamController, $method) {
                    $streamController->invoke($method);
                }
                    )
                        ->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
                        ->hasMessage('Method streamWrapper::' . $method . '() does not exist')
        ;
    }

    public function testDuplicate()
    {
        $this
            ->if($streamController = new testedClass(uniqid()))
            ->then
                ->object($duplicatedController = $streamController->duplicate())->isCloneOf($streamController)
            ->if($streamController->setPath($path = uniqid()))
            ->then
                ->string($duplicatedController->getPath())->isEqualTo($path)
            ->if($streamController->stream_lock())
            ->then
                ->object($duplicatedController->getCalls())->isEqualTo($streamController->getCalls())
            ->if($streamController->stream_lock = function () {
            })
            ->then
                ->array($duplicatedController->getInvokers())->isEqualTo($streamController->getInvokers())
        ;
    }
}
