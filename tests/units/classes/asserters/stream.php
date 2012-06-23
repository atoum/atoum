<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class stream extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new asserters\stream($generator = new asserter\generator()))
			->then
				->variable($asserter->getStreamName())->isNull()
				->variable($asserter->getStreamController())->isNull()
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new asserters\stream($generator = new asserter\generator()))
			->then
				->object($asserter->setWith($stream = uniqid()))->isIdenticalTo($asserter)
				->string($asserter->getStreamName())->isEqualTo($stream)
				->object($asserter->getStreamController())->isEqualTo(atoum\mock\stream::get($stream))
			->if(atoum\mock\stream::get($stream = uniqid()))
			->then
				->object($asserter->setWith($stream))->isIdenticalTo($asserter)
				->string($asserter->getStreamName())->isEqualTo($stream)
				->object($asserter->getStreamController())->isIdenticalTo(atoum\mock\stream::get($stream))
		;
	}

	public function testIsRead()
	{
		$this
			->if($asserter = new asserters\stream($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isRead(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Stream is undefined')
			->if($streamController = atoum\mock\stream::get($streamName = uniqid()))
			->and($streamController->file_get_contents = uniqid())
			->and($asserter->setWith($streamName))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->isRead(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage = sprintf($generator->getLocale()->_('stream %s is not read'), $streamName))
				->when(function() use ($streamName) { file_get_contents('atoum://' . $streamName); })
					->object($asserter->isRead())->isIdenticalTo($asserter)
		;
	}

	public function testIsWrited()
	{
		$this
			->if($asserter = new asserters\stream($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isWrited(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Stream is undefined')
			->if($streamController = atoum\mock\stream::get($streamName = uniqid()))
			->and($streamController->file_put_contents = strlen($contents = uniqid()))
			->and($asserter->setWith($streamName))
			->then
				->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->isWrited(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage = sprintf($generator->getLocale()->_('stream %s is not writed'), $streamName))
				->when(function() use ($streamName, $contents) { file_put_contents('atoum://' . $streamName, $contents); })
					->object($asserter->isWrited())->isIdenticalTo($asserter)
		;
	}
}
