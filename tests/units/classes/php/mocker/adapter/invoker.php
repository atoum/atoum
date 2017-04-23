<?php

namespace mageekguy\atoum\tests\units\php\mocker\adapter;

require_once __DIR__ . '/../../../../runner.php';

use atoum;
use atoum\php\mocker\adapter\invoker as testedClass;

class invoker extends atoum
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\test\adapter\invoker::class);
    }

    public function testInvoke()
    {
        $this
            ->if($invoker = new testedClass(uniqid()))
            ->then
                ->exception(function () use ($invoker) {
                    $invoker->invoke();
                })
                    ->isInstanceOf(atoum\exceptions\logic\invalidArgument::class)
                    ->hasMessage('There is no closure defined for call 0')
            ->if($invoker->setClosure(function ($string) {
                return md5($string);
            }))
            ->and($invoker->setClosure(function () use (& $md5) {
                return $md5 = uniqid();
            }, 1))
            ->and($invoker->setClosure(function () use (& $md5) {
                return $md5 = uniqid();
            }, $call = rand(2, PHP_INT_MAX)))
            ->then
                ->string($invoker->invoke([$string = uniqid()]))->isEqualTo(md5($string))
                ->string($invoker->invoke([$string = uniqid()], 0))->isEqualTo(md5($string))
                ->string($invoker->invoke([$string = uniqid()], 1))->isEqualTo($md5)
                ->string($invoker->invoke([$string = uniqid()]))->isEqualTo(md5($string))
                ->string($invoker->invoke([$string = uniqid()], 0))->isEqualTo(md5($string))
                ->string($invoker->invoke([$string = uniqid()], $call))->isEqualTo($md5)
        ;
    }
}
