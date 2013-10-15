<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters\stream as sut
;

require_once __DIR__ . '/../../runner.php';

class stream extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserter');
	}

	public function test__construct()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->variable($asserter->getStreamController())->isNull()
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new sut(new asserter\generator()))
			->then
				->object($asserter->setWith($stream = uniqid()))->isIdenticalTo($asserter)
				->object($asserter->getStreamController())->isEqualTo(atoum\mock\stream::get($stream))
			->if(atoum\mock\stream::get($stream = uniqid()))
			->then
				->object($asserter->setWith($stream))->isIdenticalTo($asserter)
				->object($asserter->getStreamController())->isIdenticalTo(atoum\mock\stream::get($stream))
		;
	}

	public function testIsRead()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isRead(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Stream is undefined')
			->if($streamController = atoum\mock\stream::get($streamName = uniqid()))
			->and($streamController->file_get_contents = uniqid())
			->and($asserter->setWith($streamName))
			->then
				->exception(function() use ($asserter) { $asserter->isRead(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage = sprintf($generator->getLocale()->_('stream %s is not read'), $streamController))
				->when(function() use ($streamName) { file_get_contents('atoum://' . $streamName); })
					->object($asserter->isRead())->isIdenticalTo($asserter)
		;
	}

	public function testIsWrited()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isWrited(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Stream is undefined')
			->if($streamController = atoum\mock\stream::get($streamName = uniqid()))
			->and($streamController->file_put_contents = strlen($contents = uniqid()))
			->and($asserter->setWith($streamName))
			->then
				->exception(function() use ($asserter) { $asserter->isWrited(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage = sprintf($generator->getLocale()->_('stream %s is not writed'), $streamController))
			->when(function() use ($streamName, $contents) { file_put_contents('atoum://' . $streamName, $contents); })
				->object($asserter->isWrited())->isIdenticalTo($asserter)
			->if($streamController = atoum\mock\stream::get(uniqid()))
			->and($streamController->file_put_contents = strlen($contents = uniqid()))
			->and($asserter->setWith($streamController))
			->then
				->exception(function() use ($asserter) { $asserter->isWrited(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage = sprintf($generator->getLocale()->_('stream %s is not writed'), $streamController))
			->when(function() use ($streamController, $contents) { file_put_contents($streamController, $contents); })
				->object($asserter->isWrited())->isIdenticalTo($asserter)
		;
	}
}
