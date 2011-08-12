<?php

namespace mageekguy\atoum\tests\units\mock\stream;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\mock\stream
;

require_once(__DIR__ . '/../../../runner.php');

class controller extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\test\adapter')
		;
	}

	public function test__construct()
	{
		$streamController = new stream\controller();

		$this->assert
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

	public function test__get()
	{
		$streamController = new stream\controller();

		$this->assert
			->object($streamController->__construct)->isEqualTo(new test\adapter\callable())
			->object($streamController->dir_closedir)->isEqualTo(new test\adapter\callable())
			->object($streamController->closedir)->isEqualTo(new test\adapter\callable())
			->object($streamController->dir_opendir)->isEqualTo(new test\adapter\callable())
			->object($streamController->opendir)->isEqualTo(new test\adapter\callable())
			->object($streamController->dir_readdir)->isEqualTo(new test\adapter\callable())
			->object($streamController->readdir)->isEqualTo(new test\adapter\callable())
			->object($streamController->dir_rewinddir)->isEqualTo(new test\adapter\callable())
			->object($streamController->rewinddir)->isEqualTo(new test\adapter\callable())
			->object($streamController->mkdir)->isEqualTo(new test\adapter\callable())
			->object($streamController->rename)->isEqualTo(new test\adapter\callable())
			->object($streamController->rmdir)->isEqualTo(new test\adapter\callable())
			->object($streamController->stream_cast)->isEqualTo(new test\adapter\callable())
			->object($streamController->select)->isEqualTo(new test\adapter\callable())
			->object($streamController->stream_close)->isEqualTo(new test\adapter\callable())
			->object($streamController->fclose)->isEqualTo(new test\adapter\callable())
			->object($streamController->stream_eof)->isEqualTo(new test\adapter\callable())
			->object($streamController->feof)->isEqualTo(new test\adapter\callable())
			->object($streamController->stream_flush)->isEqualTo(new test\adapter\callable())
			->object($streamController->fflush)->isEqualTo(new test\adapter\callable())
			->object($streamController->stream_lock)->isEqualTo(new test\adapter\callable())
			->object($streamController->flock)->isEqualTo(new test\adapter\callable())
			->object($streamController->stream_metadata)->isEqualTo(new test\adapter\callable())
			->object($streamController->touch)->isEqualTo(new test\adapter\callable())
			->object($streamController->chmod)->isEqualTo(new test\adapter\callable())
			->object($streamController->chown)->isEqualTo(new test\adapter\callable())
			->object($streamController->chgrp)->isEqualTo(new test\adapter\callable())
			->object($streamController->stream_open)->isEqualTo(new test\adapter\callable())
			->object($streamController->fopen)->isEqualTo(new test\adapter\callable())
			->object($streamController->stream_read)->isEqualTo(new test\adapter\callable())
			->object($streamController->fread)->isEqualTo(new test\adapter\callable())
			->object($streamController->stream_seek)->isEqualTo(new test\adapter\callable())
			->object($streamController->fseek)->isEqualTo(new test\adapter\callable())
			->object($streamController->stream_set_option)->isEqualTo(new test\adapter\callable())
			->object($streamController->stream_stat)->isEqualTo(new test\adapter\callable())
			->object($streamController->fstat)->isEqualTo(new test\adapter\callable())
			->object($streamController->stream_tell)->isEqualTo(new test\adapter\callable())
			->object($streamController->ftell)->isEqualTo(new test\adapter\callable())
			->object($streamController->stream_write)->isEqualTo(new test\adapter\callable())
			->object($streamController->fwrite)->isEqualTo(new test\adapter\callable())
			->object($streamController->unlink)->isEqualTo(new test\adapter\callable())
			->object($streamController->url_stat)->isEqualTo(new test\adapter\callable())
			->object($streamController->stat)->isEqualTo(new test\adapter\callable())
		;

		$method = uniqid();

		$this->assert
			->exception(function() use ($streamController, $method) {
						$streamController->{$method};
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Method streamWrapper::' . $method . '() does not exist')
		;
	}

	public function test__set()
	{
		$streamController = new stream\controller();

		$streamController->__construct = $__construct = uniqid();

		$this->assert
			->string($streamController->invoke('__construct'))->isEqualTo($__construct)
		;

		$streamController->dir_closedir = $dir_closedir = uniqid();

		$this->assert
			->string($streamController->invoke('dir_closedir'))->isEqualTo($dir_closedir)
		;

		$streamController->closedir = $closedir = uniqid();

		$this->assert
			->string($streamController->invoke('closedir'))->isEqualTo($closedir)
		;

		$streamController->dir_opendir = $dir_opendir = uniqid();

		$this->assert
			->string($streamController->invoke('dir_opendir'))->isEqualTo($dir_opendir)
		;

		$streamController->opendir = $opendir = uniqid();

		$this->assert
			->string($streamController->invoke('opendir'))->isEqualTo($opendir)
		;

		$streamController->dir_readdir = $dir_readdir = uniqid();

		$this->assert
			->string($streamController->invoke('dir_readdir'))->isEqualTo($dir_readdir)
		;

		$streamController->readdir = $readdir = uniqid();

		$this->assert
			->string($streamController->invoke('readdir'))->isEqualTo($readdir)
		;

		$streamController->dir_rewinddir = $dir_rewinddir = uniqid();

		$this->assert
			->string($streamController->invoke('dir_rewinddir'))->isEqualTo($dir_rewinddir)
		;

		$streamController->rewinddir = $rewinddir = uniqid();

		$this->assert
			->string($streamController->invoke('rewinddir'))->isEqualTo($rewinddir)
		;

		$streamController->mkdir = $mkdir = uniqid();

		$this->assert
			->string($streamController->invoke('mkdir'))->isEqualTo($mkdir)
		;

		$streamController->rename = $rename = uniqid();

		$this->assert
			->string($streamController->invoke('rename'))->isEqualTo($rename)
		;

		$streamController->rmdir = $rmdir = uniqid();

		$this->assert
			->string($streamController->invoke('rmdir'))->isEqualTo($rmdir)
		;

		$streamController->stream_cast = $stream_cast = uniqid();

		$this->assert
			->string($streamController->invoke('stream_cast'))->isEqualTo($stream_cast)
		;

		$streamController->select = $select = uniqid();

		$this->assert
			->string($streamController->invoke('select'))->isEqualTo($select)
		;

		$streamController->stream_close = $stream_close = uniqid();

		$this->assert
			->string($streamController->invoke('stream_close'))->isEqualTo($stream_close)
		;

		$streamController->fclose = $fclose = uniqid();

		$this->assert
			->string($streamController->invoke('fclose'))->isEqualTo($fclose)
		;

		$streamController->stream_eof = $stream_eof = uniqid();

		$this->assert
			->string($streamController->invoke('stream_eof'))->isEqualTo($stream_eof)
		;

		$streamController->feof = $feof = uniqid();

		$this->assert
			->string($streamController->invoke('feof'))->isEqualTo($feof)
		;

		$streamController->stream_flush = $stream_flush = uniqid();

		$this->assert
			->string($streamController->invoke('stream_flush'))->isEqualTo($stream_flush)
		;

		$streamController->fflush = $fflush = uniqid();

		$this->assert
			->string($streamController->invoke('fflush'))->isEqualTo($fflush)
		;

		$streamController->stream_lock = $stream_lock = uniqid();

		$this->assert
			->string($streamController->invoke('stream_lock'))->isEqualTo($stream_lock)
		;

		$streamController->flock = $flock = uniqid();

		$this->assert
			->string($streamController->invoke('flock'))->isEqualTo($flock)
		;

		$streamController->stream_metadata = $stream_metadata = uniqid();

		$this->assert
			->string($streamController->invoke('stream_metadata'))->isEqualTo($stream_metadata)
		;

		$streamController->touch = $touch = uniqid();

		$this->assert
			->string($streamController->invoke('touch'))->isEqualTo($touch)
		;

		$streamController->chmod = $chmod = uniqid();

		$this->assert
			->string($streamController->invoke('chmod'))->isEqualTo($chmod)
		;

		$streamController->chown = $chown = uniqid();

		$this->assert
			->string($streamController->invoke('chown'))->isEqualTo($chown)
		;

		$streamController->chgrp = $chgrp = uniqid();

		$this->assert
			->string($streamController->invoke('chgrp'))->isEqualTo($chgrp)
		;

		$streamController->stream_open = $stream_open = uniqid();

		$this->assert
			->string($streamController->invoke('stream_open'))->isEqualTo($stream_open)
		;

		$streamController->fopen = $fopen = uniqid();

		$this->assert
			->string($streamController->invoke('fopen'))->isEqualTo($fopen)
		;

		$streamController->stream_read = $stream_read = uniqid();

		$this->assert
			->string($streamController->invoke('stream_read'))->isEqualTo($stream_read)
		;

		$streamController->fread = $fread = uniqid();

		$this->assert
			->string($streamController->invoke('fread'))->isEqualTo($fread)
		;

		$streamController->stream_seek = $stream_seek = uniqid();

		$this->assert
			->string($streamController->invoke('stream_seek'))->isEqualTo($stream_seek)
		;

		$streamController->fseek = $fseek = uniqid();

		$this->assert
			->string($streamController->invoke('fseek'))->isEqualTo($fseek)
		;

		$streamController->stream_set_option = $stream_set_option = uniqid();

		$this->assert
			->string($streamController->invoke('stream_set_option'))->isEqualTo($stream_set_option)
		;

		$streamController->stream_stat = $stream_stat = uniqid();

		$this->assert
			->string($streamController->invoke('stream_stat'))->isEqualTo($stream_stat)
		;

		$streamController->fstat = $fstat = uniqid();

		$this->assert
			->string($streamController->invoke('fstat'))->isEqualTo($fstat)
		;

		$streamController->stream_tell = $stream_tell = uniqid();

		$this->assert
			->string($streamController->invoke('stream_tell'))->isEqualTo($stream_tell)
		;

		$streamController->ftell = $ftell = uniqid();

		$this->assert
			->string($streamController->invoke('ftell'))->isEqualTo($ftell)
		;

		$streamController->stream_write = $stream_write = uniqid();

		$this->assert
			->string($streamController->invoke('stream_write'))->isEqualTo($stream_write)
		;

		$streamController->fwrite = $fwrite = uniqid();

		$this->assert
			->string($streamController->invoke('fwrite'))->isEqualTo($fwrite)
		;

		$streamController->unlink = $unlink = uniqid();

		$this->assert
			->string($streamController->invoke('unlink'))->isEqualTo($unlink)
		;

		$streamController->url_stat = $url_stat = uniqid();

		$this->assert
			->string($streamController->invoke('url_stat'))->isEqualTo($url_stat)
		;

		$streamController->stat = $stat = uniqid();

		$this->assert
			->string($streamController->invoke('stat'))->isEqualTo($stat)
		;

		$streamController->resetCalls()->file_get_contents = $contents = uniqid();

		$this->assert
			->boolean($streamController->invoke('fopen'))->isTrue()
			->string($streamController->invoke('fread'))->isEqualTo($contents)
			->string($streamController->invoke('fread'))->isEmpty()
			->boolean($streamController->invoke('fclose'))->isTrue()
		;

		$streamController->resetCalls()->file_put_contents = true;

		$this->assert
			->boolean($streamController->invoke('fopen'))->isTrue()
			->boolean($streamController->invoke('fwrite'))->isTrue()
			->boolean($streamController->invoke('fclose'))->isTrue()
		;

		$streamController->resetCalls()->file_put_contents = false;

		$this->assert
			->boolean($streamController->invoke('fopen'))->isTrue()
			->boolean($streamController->invoke('fwrite'))->isFalse()
			->boolean($streamController->invoke('fclose'))->isTrue()
		;

		$method = uniqid();

		$this->assert
			->exception(function() use ($streamController, $method) {
						$streamController->{$method} = uniqid();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Method streamWrapper::' . $method . '() does not exist')
		;
	}

	public function test__isset()
	{
		$streamController = new stream\controller();

		$this->assert
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
		;

		$streamController->__construct = uniqid();
		$streamController->dir_closedir = uniqid();
		$streamController->closedir = uniqid();
		$streamController->dir_opendir = uniqid();
		$streamController->opendir = uniqid();
		$streamController->dir_readdir = uniqid();
		$streamController->readdir = uniqid();
		$streamController->dir_rewinddir = uniqid();
		$streamController->rewinddir = uniqid();
		$streamController->mkdir = uniqid();
		$streamController->rename = uniqid();
		$streamController->rmdir = uniqid();
		$streamController->stream_cast = uniqid();
		$streamController->select = uniqid();
		$streamController->stream_close = uniqid();
		$streamController->fclose = uniqid();
		$streamController->stream_eof = uniqid();
		$streamController->feof = uniqid();
		$streamController->stream_flush = uniqid();
		$streamController->fflush = uniqid();
		$streamController->stream_lock = uniqid();
		$streamController->flock = uniqid();
		$streamController->stream_metadata = uniqid();
		$streamController->touch = uniqid();
		$streamController->chown = uniqid();
		$streamController->chmod = uniqid();
		$streamController->chgrp = uniqid();
		$streamController->stream_open = uniqid();
		$streamController->fopen = uniqid();
		$streamController->stream_read = uniqid();
		$streamController->fread = uniqid();
		$streamController->stream_seek = uniqid();
		$streamController->fseek = uniqid();
		$streamController->stream_set_option = uniqid();
		$streamController->stream_stat = uniqid();
		$streamController->fstat = uniqid();
		$streamController->stream_tell = uniqid();
		$streamController->ftell = uniqid();
		$streamController->stream_write = uniqid();
		$streamController->fwrite = uniqid();
		$streamController->unlink = uniqid();
		$streamController->url_stat = uniqid();
		$streamController->stat = uniqid();

		$this->assert
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
		;

		$method = uniqid();

		$this->assert
			->exception(function() use ($streamController, $method) {
						isset($streamController->{$method});
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Method streamWrapper::' . $method . '() does not exist')
		;
	}

	public function test__unset()
	{
		$streamController = new stream\controller();

		$this->assert
			->boolean(isset($streamController->__construct))->isFalse()
			->when(function() use ($streamController) { unset($streamController->__construct); })
				->boolean(isset($streamController->__construct))->isFalse()
				->boolean(isset($streamController->dir_closedir))->isFalse()
			->when(function() use ($streamController) { unset($streamController->dir_closedir); })
				->boolean(isset($streamController->dir_closedir))->isFalse()
				->boolean(isset($streamController->closedir))->isFalse()
			->when(function() use ($streamController) { unset($streamController->closedir); })
				->boolean(isset($streamController->closedir))->isFalse()
				->boolean(isset($streamController->dir_opendir))->isFalse()
			->when(function() use ($streamController) { unset($streamController->dir_opendir); })
				->boolean(isset($streamController->dir_opendir))->isFalse()
				->boolean(isset($streamController->opendir))->isFalse()
			->when(function() use ($streamController) { unset($streamController->opendir); })
				->boolean(isset($streamController->opendir))->isFalse()
				->boolean(isset($streamController->dir_readdir))->isFalse()
			->when(function() use ($streamController) { unset($streamController->dir_readdir); })
				->boolean(isset($streamController->dir_readdir))->isFalse()
				->boolean(isset($streamController->readdir))->isFalse()
			->when(function() use ($streamController) { unset($streamController->readdir); })
				->boolean(isset($streamController->readdir))->isFalse()
				->boolean(isset($streamController->dir_rewinddir))->isFalse()
			->when(function() use ($streamController) { unset($streamController->dir_rewinddir); })
				->boolean(isset($streamController->dir_rewinddir))->isFalse()
				->boolean(isset($streamController->rewinddir))->isFalse()
			->when(function() use ($streamController) { unset($streamController->rewinddir); })
				->boolean(isset($streamController->rewinddir))->isFalse()
				->boolean(isset($streamController->mkdir))->isFalse()
			->when(function() use ($streamController) { unset($streamController->mkdir); })
				->boolean(isset($streamController->mkdir))->isFalse()
				->boolean(isset($streamController->rename))->isFalse()
			->when(function() use ($streamController) { unset($streamController->rename); })
				->boolean(isset($streamController->rename))->isFalse()
				->boolean(isset($streamController->rmdir))->isFalse()
			->when(function() use ($streamController) { unset($streamController->rmdir); })
				->boolean(isset($streamController->rmdir))->isFalse()
				->boolean(isset($streamController->stream_cast))->isFalse()
			->when(function() use ($streamController) { unset($streamController->stream_cast); })
				->boolean(isset($streamController->stream_cast))->isFalse()
				->boolean(isset($streamController->select))->isFalse()
			->when(function() use ($streamController) { unset($streamController->select); })
				->boolean(isset($streamController->select))->isFalse()
				->boolean(isset($streamController->stream_close))->isFalse()
			->when(function() use ($streamController) { unset($streamController->stream_close); })
				->boolean(isset($streamController->stream_close))->isFalse()
				->boolean(isset($streamController->fclose))->isFalse()
			->when(function() use ($streamController) { unset($streamController->fclose); })
				->boolean(isset($streamController->fclose))->isFalse()
				->boolean(isset($streamController->stream_eof))->isFalse()
			->when(function() use ($streamController) { unset($streamController->stream_eof); })
				->boolean(isset($streamController->stream_eof))->isFalse()
				->boolean(isset($streamController->feof))->isFalse()
			->when(function() use ($streamController) { unset($streamController->feof); })
				->boolean(isset($streamController->feof))->isFalse()
				->boolean(isset($streamController->stream_flush))->isFalse()
			->when(function() use ($streamController) { unset($streamController->stream_flush); })
				->boolean(isset($streamController->stream_flush))->isFalse()
				->boolean(isset($streamController->fflush))->isFalse()
			->when(function() use ($streamController) { unset($streamController->fflush); })
				->boolean(isset($streamController->fflush))->isFalse()
				->boolean(isset($streamController->stream_lock))->isFalse()
			->when(function() use ($streamController) { unset($streamController->stream_lock); })
				->boolean(isset($streamController->stream_lock))->isFalse()
				->boolean(isset($streamController->flock))->isFalse()
			->when(function() use ($streamController) { unset($streamController->flock); })
				->boolean(isset($streamController->flock))->isFalse()
				->boolean(isset($streamController->stream_metadata))->isFalse()
			->when(function() use ($streamController) { unset($streamController->stream_metadata); })
				->boolean(isset($streamController->stream_metadata))->isFalse()
				->boolean(isset($streamController->touch))->isFalse()
			->when(function() use ($streamController) { unset($streamController->touch); })
				->boolean(isset($streamController->touch))->isFalse()
				->boolean(isset($streamController->chmod))->isFalse()
			->when(function() use ($streamController) { unset($streamController->chmod); })
				->boolean(isset($streamController->chmod))->isFalse()
				->boolean(isset($streamController->chown))->isFalse()
			->when(function() use ($streamController) { unset($streamController->chown); })
				->boolean(isset($streamController->chown))->isFalse()
				->boolean(isset($streamController->chgrp))->isFalse()
			->when(function() use ($streamController) { unset($streamController->chgrp); })
				->boolean(isset($streamController->chgrp))->isFalse()
				->boolean(isset($streamController->stream_open))->isFalse()
			->when(function() use ($streamController) { unset($streamController->stream_open); })
				->boolean(isset($streamController->stream_open))->isFalse()
				->boolean(isset($streamController->fopen))->isFalse()
			->when(function() use ($streamController) { unset($streamController->fopen); })
				->boolean(isset($streamController->fopen))->isFalse()
				->boolean(isset($streamController->stream_read))->isFalse()
			->when(function() use ($streamController) { unset($streamController->stream_read); })
				->boolean(isset($streamController->stream_read))->isFalse()
				->boolean(isset($streamController->fread))->isFalse()
			->when(function() use ($streamController) { unset($streamController->fread); })
				->boolean(isset($streamController->fread))->isFalse()
				->boolean(isset($streamController->stream_seek))->isFalse()
			->when(function() use ($streamController) { unset($streamController->stream_seek); })
				->boolean(isset($streamController->stream_seek))->isFalse()
				->boolean(isset($streamController->fseek))->isFalse()
			->when(function() use ($streamController) { unset($streamController->fseek); })
				->boolean(isset($streamController->fseek))->isFalse()
				->boolean(isset($streamController->stream_set_option))->isFalse()
			->when(function() use ($streamController) { unset($streamController->stream_set_option); })
				->boolean(isset($streamController->stream_set_option))->isFalse()
				->boolean(isset($streamController->stream_stat))->isFalse()
			->when(function() use ($streamController) { unset($streamController->stream_stat); })
				->boolean(isset($streamController->stream_stat))->isFalse()
				->boolean(isset($streamController->fstat))->isFalse()
			->when(function() use ($streamController) { unset($streamController->fstat); })
				->boolean(isset($streamController->fstat))->isFalse()
				->boolean(isset($streamController->stream_tell))->isFalse()
			->when(function() use ($streamController) { unset($streamController->stream_tell); })
				->boolean(isset($streamController->stream_tell))->isFalse()
				->boolean(isset($streamController->ftell))->isFalse()
			->when(function() use ($streamController) { unset($streamController->ftell); })
				->boolean(isset($streamController->ftell))->isFalse()
				->boolean(isset($streamController->stream_write))->isFalse()
			->when(function() use ($streamController) { unset($streamController->stream_write); })
				->boolean(isset($streamController->stream_write))->isFalse()
				->boolean(isset($streamController->fwrite))->isFalse()
			->when(function() use ($streamController) { unset($streamController->fwrite); })
				->boolean(isset($streamController->fwrite))->isFalse()
				->boolean(isset($streamController->unlink))->isFalse()
			->when(function() use ($streamController) { unset($streamController->unlink); })
				->boolean(isset($streamController->unlink))->isFalse()
				->boolean(isset($streamController->url_stat))->isFalse()
			->when(function() use ($streamController) { unset($streamController->url_stat); })
				->boolean(isset($streamController->url_stat))->isFalse()
				->boolean(isset($streamController->stat))->isFalse()
			->when(function() use ($streamController) { unset($streamController->stat); })
				->boolean(isset($streamController->stat))->isFalse()
		;

		$streamController->__construct = uniqid();
		$streamController->dir_closedir = uniqid();
		$streamController->closedir = uniqid();
		$streamController->dir_opendir = uniqid();
		$streamController->opendir = uniqid();
		$streamController->dir_readdir = uniqid();
		$streamController->readdir = uniqid();
		$streamController->dir_rewinddir = uniqid();
		$streamController->rewinddir = uniqid();
		$streamController->mkdir = uniqid();
		$streamController->rename = uniqid();
		$streamController->rmdir = uniqid();
		$streamController->stream_cast = uniqid();
		$streamController->select = uniqid();
		$streamController->stream_close = uniqid();
		$streamController->fclose = uniqid();
		$streamController->stream_eof = uniqid();
		$streamController->feof = uniqid();
		$streamController->stream_flush = uniqid();
		$streamController->fflush = uniqid();
		$streamController->stream_lock = uniqid();
		$streamController->flock = uniqid();
		$streamController->stream_metadata = uniqid();
		$streamController->touch = uniqid();
		$streamController->chown = uniqid();
		$streamController->chmod = uniqid();
		$streamController->chgrp = uniqid();
		$streamController->stream_open = uniqid();
		$streamController->fopen = uniqid();
		$streamController->stream_read = uniqid();
		$streamController->fread = uniqid();
		$streamController->stream_seek = uniqid();
		$streamController->fseek = uniqid();
		$streamController->stream_set_option = uniqid();
		$streamController->stream_stat = uniqid();
		$streamController->fstat = uniqid();
		$streamController->stream_tell = uniqid();
		$streamController->ftell = uniqid();
		$streamController->stream_write = uniqid();
		$streamController->fwrite = uniqid();
		$streamController->unlink = uniqid();
		$streamController->url_stat = uniqid();
		$streamController->stat = uniqid();

		$this->assert
				->boolean(isset($streamController->__construct))->isTrue()
			->when(function() use ($streamController) { unset($streamController->__construct); })
				->boolean(isset($streamController->__construct))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->dir_closedir))->isTrue()
			->boolean(isset($streamController->closedir))->isTrue()
			->when(function() use ($streamController) { unset($streamController->dir_closedir); })
				->boolean(isset($streamController->dir_closedir))->isFalse()
				->boolean(isset($streamController->closedir))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->dir_opendir))->isTrue()
			->boolean(isset($streamController->opendir))->isTrue()
			->when(function() use ($streamController) { unset($streamController->dir_opendir); })
				->boolean(isset($streamController->dir_opendir))->isFalse()
				->boolean(isset($streamController->opendir))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->dir_readdir))->isTrue()
			->boolean(isset($streamController->readdir))->isTrue()
			->when(function() use ($streamController) { unset($streamController->dir_readdir); })
				->boolean(isset($streamController->dir_readdir))->isFalse()
				->boolean(isset($streamController->readdir))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->dir_rewinddir))->isTrue()
			->boolean(isset($streamController->rewinddir))->isTrue()
			->when(function() use ($streamController) { unset($streamController->dir_rewinddir); })
				->boolean(isset($streamController->dir_rewinddir))->isFalse()
				->boolean(isset($streamController->rewinddir))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->mkdir))->isTrue()
			->when(function() use ($streamController) { unset($streamController->mkdir); })
				->boolean(isset($streamController->mkdir))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->rename))->isTrue()
			->when(function() use ($streamController) { unset($streamController->rename); })
				->boolean(isset($streamController->rename))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->rmdir))->isTrue()
			->when(function() use ($streamController) { unset($streamController->rmdir); })
				->boolean(isset($streamController->rmdir))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->stream_cast))->isTrue()
			->boolean(isset($streamController->select))->isTrue()
			->when(function() use ($streamController) { unset($streamController->stream_cast); })
				->boolean(isset($streamController->stream_cast))->isFalse()
				->boolean(isset($streamController->select))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->stream_close))->isTrue()
			->boolean(isset($streamController->fclose))->isTrue()
			->when(function() use ($streamController) { unset($streamController->stream_close); })
				->boolean(isset($streamController->stream_close))->isFalse()
				->boolean(isset($streamController->fclose))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->stream_eof))->isTrue()
			->boolean(isset($streamController->feof))->isTrue()
			->when(function() use ($streamController) { unset($streamController->stream_eof); })
				->boolean(isset($streamController->stream_eof))->isFalse()
				->boolean(isset($streamController->feof))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->stream_flush))->isTrue()
			->boolean(isset($streamController->fflush))->isTrue()
			->when(function() use ($streamController) { unset($streamController->stream_flush); })
				->boolean(isset($streamController->stream_flush))->isFalse()
				->boolean(isset($streamController->fflush))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->stream_lock))->isTrue()
			->boolean(isset($streamController->flock))->isTrue()
			->when(function() use ($streamController) { unset($streamController->stream_lock); })
				->boolean(isset($streamController->stream_lock))->isFalse()
				->boolean(isset($streamController->flock))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->stream_metadata))->isTrue()
			->boolean(isset($streamController->touch))->isTrue()
			->boolean(isset($streamController->chmod))->isTrue()
			->boolean(isset($streamController->chown))->isTrue()
			->boolean(isset($streamController->chgrp))->isTrue()
			->when(function() use ($streamController) { unset($streamController->stream_metadata); })
				->boolean(isset($streamController->stream_metadata))->isFalse()
				->boolean(isset($streamController->touch))->isFalse()
				->boolean(isset($streamController->chmod))->isFalse()
				->boolean(isset($streamController->chown))->isFalse()
				->boolean(isset($streamController->chgrp))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->stream_open))->isTrue()
			->boolean(isset($streamController->fopen))->isTrue()
			->when(function() use ($streamController) { unset($streamController->stream_open); })
				->boolean(isset($streamController->stream_open))->isFalse()
				->boolean(isset($streamController->fopen))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->stream_read))->isTrue()
			->boolean(isset($streamController->fread))->isTrue()
			->when(function() use ($streamController) { unset($streamController->stream_read); })
				->boolean(isset($streamController->stream_read))->isFalse()
				->boolean(isset($streamController->fread))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->stream_seek))->isTrue()
			->boolean(isset($streamController->fseek))->isTrue()
			->when(function() use ($streamController) { unset($streamController->stream_seek); })
				->boolean(isset($streamController->stream_seek))->isFalse()
				->boolean(isset($streamController->fseek))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->stream_set_option))->isTrue()
			->when(function() use ($streamController) { unset($streamController->stream_set_option); })
				->boolean(isset($streamController->stream_set_option))->isFalse()

		;

		$this->assert
			->boolean(isset($streamController->stream_stat))->isTrue()
			->boolean(isset($streamController->fstat))->isTrue()
			->when(function() use ($streamController) { unset($streamController->stream_stat); })
				->boolean(isset($streamController->stream_stat))->isFalse()
				->boolean(isset($streamController->fstat))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->stream_tell))->isTrue()
			->boolean(isset($streamController->ftell))->isTrue()
			->when(function() use ($streamController) { unset($streamController->stream_tell); })
				->boolean(isset($streamController->stream_tell))->isFalse()
				->boolean(isset($streamController->ftell))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->stream_write))->isTrue()
			->boolean(isset($streamController->fwrite))->isTrue()
			->when(function() use ($streamController) { unset($streamController->stream_write); })
				->boolean(isset($streamController->stream_write))->isFalse()
				->boolean(isset($streamController->fwrite))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->unlink))->isTrue()
			->when(function() use ($streamController) { unset($streamController->unlink); })
				->boolean(isset($streamController->unlink))->isFalse()
		;

		$this->assert
			->boolean(isset($streamController->url_stat))->isTrue()
			->boolean(isset($streamController->stat))->isTrue()
			->when(function() use ($streamController) { unset($streamController->url_stat); })
				->boolean(isset($streamController->url_stat))->isFalse()
				->boolean(isset($streamController->stat))->isFalse()
		;

		$method = uniqid();

		$this->assert
			->exception(function() use ($streamController, $method) {
						unset($streamController->{$method});
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Method streamWrapper::' . $method . '() does not exist')
		;
	}

	public function testInvoke()
	{
		$streamController = new stream\controller();

		$this->assert
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
		;

		$method = uniqid();

		$this->assert
			->exception(function() use ($streamController, $method) {
						$streamController->invoke($method);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Method streamWrapper::' . $method . '() does not exist')
		;
	}
}

?>
