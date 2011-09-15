<?php

namespace mageekguy\atoum\tests\units\mock;

use
	mageekguy\atoum,
	mageekguy\atoum\test
;

require_once __DIR__ . '/../../runner.php';

class stream extends atoum\test
{
	public function testClassConstants()
	{
		$this->assert
			->string(atoum\mock\stream::defaultProtocol)->isEqualTo('atoum')
		;
	}

	public function testGetAdapter()
	{
		$this->assert
			->object(atoum\mock\stream::getAdapter())->isEqualTo(new atoum\adapter())
		;

		atoum\mock\stream::setAdapter($adapter = new atoum\adapter());

		$this->assert
			->object(atoum\mock\stream::getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testGet()
	{
		atoum\mock\stream::setAdapter($adapter = new test\adapter());

		$this->assert
			->when(function() use ($adapter) {
					$adapter->stream_get_wrappers = array();
					$adapter->stream_wrapper_register = true;
				}
			)
				->object(atoum\mock\stream::get($stream = uniqid()))->isEqualTo(new atoum\mock\stream\controller())
				->adapter($adapter)
					->call('stream_get_wrappers')->once()
					->call('stream_wrapper_register')->withArguments(atoum\mock\stream::defaultProtocol, 'mageekguy\atoum\mock\stream')->once()
				->object(atoum\mock\stream::get($stream))->isIdenticalTo($streamController = atoum\mock\stream::get($stream))
		;

		$this->assert
				->object(atoum\mock\stream::get($otherStream = ($protocol = uniqid()) . '://' . uniqid()))->isNotIdenticalTo($streamController)
				->adapter($adapter)
					->call('stream_get_wrappers')->once()
					->call('stream_wrapper_register')->withArguments($protocol, 'mageekguy\atoum\mock\stream')->once()
				->object(atoum\mock\stream::get($otherStream))->isIdenticalTo(atoum\mock\stream::get($otherStream))
		;

		$this->assert
			->when(function() use ($adapter) {
					$adapter->stream_get_wrappers = array(atoum\mock\stream::defaultProtocol);
				}
			)
				->object(atoum\mock\stream::get(uniqid()))->isEqualTo(new atoum\mock\stream\controller())
				->adapter($adapter)
					->call('stream_get_wrappers')->once()
					->call('stream_wrapper_register')->withArguments(atoum\mock\stream::defaultProtocol, 'mageekguy\atoum\mock\stream')->never()
		;

		$this->assert
			->when(function() use ($adapter) {
					$adapter->stream_get_wrappers = array();
					$adapter->stream_wrapper_register = false;
				}
			)
				->exception(function() { atoum\mock\stream::get(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to register ' . atoum\mock\stream::defaultProtocol . ' stream')
				->adapter($adapter)
					->call('stream_get_wrappers')->once()
					->call('stream_wrapper_register')->withArguments(atoum\mock\stream::defaultProtocol, 'mageekguy\atoum\mock\stream')->once()
		;
	}

	public function testGetProtocol()
	{
		$this->assert
			->variable(atoum\mock\stream::getProtocol(uniqid()))->isNull()
			->string(atoum\mock\stream::getProtocol(($scheme = uniqid()) . '://' . uniqid()))->isEqualTo($scheme)
		;
	}
}

?>
