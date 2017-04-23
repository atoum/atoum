<?php

namespace mageekguy\atoum\tests\units\asserters;

use mageekguy\atoum;
use mageekguy\atoum\asserter;
use mageekguy\atoum\tools\variable;

require_once __DIR__ . '/../../runner.php';

class stream extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\asserter::class);
    }

    public function test__construct()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->getGenerator())->isEqualTo(new asserter\generator())
                ->object($this->testedInstance->getAnalyzer())->isEqualTo(new variable\analyzer())
                ->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())

            ->given($this->newTestedInstance($generator = new asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
            ->then
                ->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
                ->object($this->testedInstance->getAnalyzer())->isIdenticalTo($analyzer)
                ->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
        ;
    }

    public function testSetWith()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->setWith($stream = uniqid()))->isTestedInstance
                ->object($this->testedInstance->getStreamController())->isEqualTo(atoum\mock\stream::get($stream))

            ->if(atoum\mock\stream::get($stream = uniqid()))
            ->then
                ->object($this->testedInstance->setWith($stream))->isTestedInstance
                ->object($this->testedInstance->getStreamController())->isIdenticalTo(atoum\mock\stream::get($stream))
        ;
    }

    public function testIsRead()
    {
        $this
            ->given($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isRead();
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Stream is undefined')

            ->if(
                $streamController = atoum\mock\stream::get($streamName = uniqid()),
                $streamController->file_get_contents = uniqid(),
                $asserter
                    ->setWith($streamName)
                    ->setLocale($locale = new \mock\atoum\locale()),
                $this->calling($locale)->_ = $streamNotRead = uniqid()
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isRead();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($streamNotRead)
                ->mock($locale)->call('_')->withArguments('stream %s is not read', $streamController)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->isRead($failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->when(function () use ($streamName) {
                    file_get_contents('atoum://' . $streamName);
                })
                    ->object($asserter->isRead())->isIdenticalTo($asserter)
                    ->object($asserter->isRead)->isIdenticalTo($asserter)
        ;
    }

    public function testIsWritten()
    {
        $this
            ->if($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isWritten();
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Stream is undefined')

            ->if(
                $streamController = atoum\mock\stream::get($streamName = uniqid()),
                $streamController->file_put_contents = strlen($contents = uniqid()),
                $asserter
                    ->setWith($streamName)
                    ->setLocale($locale = new \mock\atoum\locale()),
                $this->calling($locale)->_ = $streamNotWritten = uniqid()
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isWritten();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($streamNotWritten)
                ->mock($locale)->call('_')->withArguments('stream %s is not written', $streamController)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->isWritten($failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->when(function () use ($streamName, $contents) {
                file_put_contents('atoum://' . $streamName, $contents);
            })
                ->object($asserter->isWritten())->isIdenticalTo($asserter)
                ->object($asserter->isWritten)->isIdenticalTo($asserter)

            ->if(
                $streamController = atoum\mock\stream::get(uniqid()),
                $streamController->file_put_contents = strlen($contents = uniqid()),
                $asserter->setWith($streamController)
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isWritten();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($streamNotWritten)
                ->mock($locale)->call('_')->withArguments('stream %s is not written', $streamController)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->isWritten($failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->when(function () use ($streamController, $contents) {
                file_put_contents($streamController, $contents);
            })
                ->object($asserter->isWritten())->isIdenticalTo($asserter)
        ;
    }
}
