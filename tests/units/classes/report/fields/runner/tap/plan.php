<?php

namespace atoum\atoum\tests\units\report\fields\runner\tap;

use atoum\atoum;
use atoum\atoum\report\fields\runner\tap\plan as testedClass;
use atoum\atoum\runner;

require __DIR__ . '/../../../../../runner.php';

class plan extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\report\field::class);
    }

    public function test__construct()
    {
        $this
            ->if($field = new testedClass())
            ->then
                ->array($field->getEvents())->isEqualTo([runner::runStart])
        ;
    }

    public function test__toString()
    {
        $this
            ->if($runner = new \mock\atoum\atoum\runner())
            ->and($this->calling($runner)->getTestMethodNumber = $testMethodNumber = rand(1, PHP_INT_MAX))
            ->and($field = new testedClass())
            ->if($field->handleEvent(runner::runStop, $runner))
            ->then
                ->castToString($field)->isEmpty()
            ->if($field->handleEvent(runner::runStart, $runner))
            ->then
                ->castToString($field)->isEqualTo('1..' . $testMethodNumber . PHP_EOL)
        ;
    }
}
