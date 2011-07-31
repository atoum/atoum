<?php

namespace mageekguy\atoum\tests\units\mock\stream;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\stream
;

require_once(__DIR__ . '/../../../runner.php');

class controller extends atoum\test
{
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

		$streamController->dir_opendir = $dir_opendir = uniqid();

		$this->assert
			->string($streamController->invoke('dir_opendir'))->isEqualTo($dir_opendir)
		;

		$streamController->dir_readdir = $dir_readdir = uniqid();

		$this->assert
			->string($streamController->invoke('dir_readdir'))->isEqualTo($dir_readdir)
		;

		$streamController->dir_rewinddir = $dir_rewinddir = uniqid();

		$this->assert
			->string($streamController->invoke('dir_rewinddir'))->isEqualTo($dir_rewinddir)
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

		$streamController->stream_close = $stream_close = uniqid();

		$this->assert
			->string($streamController->invoke('stream_close'))->isEqualTo($stream_close)
		;

		$streamController->stream_eof = $stream_eof = uniqid();

		$this->assert
			->string($streamController->invoke('stream_eof'))->isEqualTo($stream_eof)
		;

		$streamController->stream_flush = $stream_flush = uniqid();

		$this->assert
			->string($streamController->invoke('stream_flush'))->isEqualTo($stream_flush)
		;

		$streamController->stream_lock = $stream_lock = uniqid();

		$this->assert
			->string($streamController->invoke('stream_lock'))->isEqualTo($stream_lock)
		;

		$streamController->stream_metadata = $stream_metadata = uniqid();

		$this->assert
			->string($streamController->invoke('stream_metadata'))->isEqualTo($stream_metadata)
		;

		$streamController->stream_open = $stream_open = uniqid();

		$this->assert
			->string($streamController->invoke('stream_open'))->isEqualTo($stream_open)
		;

		$streamController->stream_read = $stream_read = uniqid();

		$this->assert
			->string($streamController->invoke('stream_read'))->isEqualTo($stream_read)
		;

		$streamController->stream_seek = $stream_seek = uniqid();

		$this->assert
			->string($streamController->invoke('stream_seek'))->isEqualTo($stream_seek)
		;

		$streamController->stream_set_option = $stream_set_option = uniqid();

		$this->assert
			->string($streamController->invoke('stream_set_option'))->isEqualTo($stream_set_option)
		;

		$streamController->stream_stat = $stream_stat = uniqid();

		$this->assert
			->string($streamController->invoke('stream_stat'))->isEqualTo($stream_stat)
		;

		$streamController->stream_tell = $stream_tell = uniqid();

		$this->assert
			->string($streamController->invoke('stream_tell'))->isEqualTo($stream_tell)
		;

		$streamController->stream_write = $stream_write = uniqid();

		$this->assert
			->string($streamController->invoke('stream_write'))->isEqualTo($stream_write)
		;

		$streamController->unlink = $unlink = uniqid();

		$this->assert
			->string($streamController->invoke('unlink'))->isEqualTo($unlink)
		;

		$streamController->url_stat = $url_stat = uniqid();

		$this->assert
			->string($streamController->invoke('url_stat'))->isEqualTo($url_stat)
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

	public function testInvoke()
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
