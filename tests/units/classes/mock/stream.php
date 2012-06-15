<?php

namespace mageekguy\atoum\tests\units\mock;

use
	mageekguy\atoum\mock,
	mageekguy\atoum\test,
	mageekguy\atoum\adapter
;

require_once __DIR__ . '/../../runner.php';

class stream extends test
{
	public function testClassConstants()
	{
		$this->assert
			->string(mock\stream::defaultProtocol)->isEqualTo('atoum')
			->string(mock\stream::protocolSeparator)->isEqualTo('://')
		;
	}

	public function testGetAdapter()
	{
		$this
			->object(mock\stream::getAdapter())->isEqualTo(new adapter())
			->if(mock\stream::setAdapter($adapter = new adapter()))
			->then
				->object(mock\stream::getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testGet()
	{
		$this
			->if(mock\stream::setAdapter($adapter = new test\adapter()))
			->and($adapter->stream_get_wrappers = array())
			->and($adapter->stream_wrapper_register = true)
			->then
				->object(mock\stream::get($stream = uniqid()))->isEqualTo(new mock\stream\controller())
				->adapter($adapter)
					->call('stream_get_wrappers')->once()
					->call('stream_wrapper_register')->withArguments(mock\stream::defaultProtocol, 'mageekguy\atoum\mock\stream')->once()
				->object(mock\stream::get($stream))->isIdenticalTo($streamController = mock\stream::get($stream))
				->adapter($adapter)
					->call('stream_get_wrappers')->once()
					->call('stream_wrapper_register')->withArguments(mock\stream::defaultProtocol, 'mageekguy\atoum\mock\stream')->once()
				->object(mock\stream::get($otherStream = ($protocol = uniqid()) . '://' . uniqid()))->isNotIdenticalTo($streamController)
				->adapter($adapter)
					->call('stream_get_wrappers')->exactly(2)
					->call('stream_wrapper_register')->withArguments($protocol, 'mageekguy\atoum\mock\stream')->once()
				->object(mock\stream::get($otherStream))->isIdenticalTo(mock\stream::get($otherStream))
				->object(mock\stream::get($otherStream))->isIdenticalTo(mock\stream::get($otherStream))
				->adapter($adapter)
					->call('stream_get_wrappers')->exactly(2)
					->call('stream_wrapper_register')->withArguments($protocol, 'mageekguy\atoum\mock\stream')->once()
			->if($adapter->stream_get_wrappers = array($alreadyRegisteredProtocol = uniqid()))
			->then
				->exception(function() use ($alreadyRegisteredProtocol) { mock\stream::get($alreadyRegisteredProtocol . '://' . uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Stream ' . $alreadyRegisteredProtocol . ' is already registered')
			->if($adapter->stream_get_wrappers = array())
			->and($adapter->stream_wrapper_register = false)
			->then
				->exception(function() use ($alreadyRegisteredProtocol) { mock\stream::get($alreadyRegisteredProtocol . '://' . uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to register ' . $alreadyRegisteredProtocol . ' stream')
		;
	}

	public function testGetProtocol()
	{
		$this
			->variable(mock\stream::getProtocol(uniqid()))->isNull()
			->string(mock\stream::getProtocol(($scheme = uniqid()) . '://' . uniqid()))->isEqualTo($scheme)
		;
	}

	public function testCleanStream()
	{
		$this
			->string(mock\stream::slashize('foo/bar'))->isEqualTo('foo/bar')
			->string(mock\stream::slashize('foo\bar'))->isEqualTo('foo/bar')
		;
	}
}

?>
