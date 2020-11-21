<?php

namespace atoum\atoum\tests\units\test\mock;

require __DIR__ . '/../../../runner.php';

use atoum;
use atoum\atoum\test\mock\generator as testedClass;

class generator extends atoum
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\atoum\mock\generator::class);
    }

    public function test__construct()
    {
        $this
            ->if($generator = new testedClass($this))
            ->then
                ->object($generator->getTest())->isIdenticalTo($this)
        ;
    }

    public function test__get()
    {
        $this
            ->if($generator = new testedClass($this))
            ->and($generator->setTest($test = new \mock\atoum\atoum\test()))
            ->and($this->calling($test)->__get = function () {
            })
            ->when($generator->{$property = uniqid()})
            ->then
                ->mock($test)->call('__get')->withArguments($property)->once()
        ;
    }

    public function test__call()
    {
        $this
            ->if($generator = new testedClass($this))
            ->and($generator->setTest($test = new \mock\atoum\atoum\test()))
            ->and($this->calling($test)->__call = function () {
            })
            ->when($generator->{$property = uniqid()}())
            ->then
                ->mock($test)->call('__call')->withArguments($property, [])->once()
        ;
    }

    public function testSetTest()
    {
        $this
            ->if($generator = new testedClass($this))
            ->then
                ->object($generator->setTest($test = clone $this))->isIdenticalTo($generator)
                ->object($generator->getTest())->isIdenticalTo($test)
        ;
    }
}
