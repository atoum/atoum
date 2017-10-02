<?php

namespace mageekguy\atoum\tests\units\cli\progressBar;

use mageekguy\atoum;
use mageekguy\atoum\cli;

require_once __DIR__ . '/../../../runner.php';

class dot extends atoum\test
{
    public function testClassConstants()
    {
        $this
            ->string(cli\progressBar::defaultCounterFormat)->isEqualTo('[%s/%s]')
        ;
    }

    public function test__construct()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->castToString($this->testedInstance)->isEmpty
            ->if($this->newTestedInstance(1))
            ->then
                ->castToString($this->testedInstance)->isEmpty
            ->if($this->newTestedInstance($count = rand(2, 9)))
            ->then
                ->castToString($this->testedInstance)->isEmpty
            ->if($this->newTestedInstance($count = rand(10, 60)))
            ->then
                ->castToString($this->testedInstance)->isEmpty
            ->if($this->newTestedInstance(61))
            ->then
                ->castToString($this->testedInstance)->isEmpty
            ->if($this->newTestedInstance($count = rand(100, PHP_INT_MAX)))
            ->then
                ->castToString($this->testedInstance)->isEmpty
        ;
    }

    public function testRefresh()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->refresh('F'))->isTestedInstance
                ->castToString($this->testedInstance)->isEmpty
            ->if($this->newTestedInstance(1))
            ->then
                ->castToString($this->testedInstance)->isEmpty()
                ->object($this->testedInstance->refresh('F'))->isTestedInstance
                ->castToString($this->testedInstance)->isEqualTo('F' . str_repeat(' ', 59) . ' [1/1]' . PHP_EOL)
                ->castToString($this->testedInstance)->isEmpty
            ->if($this->newTestedInstance(1))
            ->then
                ->object($this->testedInstance->refresh('F'))->isTestedInstance
                ->castToString($this->testedInstance)->isEqualTo('F' . str_repeat(' ', 59) . ' [1/1]' . PHP_EOL)
                ->castToString($this->testedInstance)->isEmpty()
                ->object($this->testedInstance->refresh('F'))->isTestedInstance
                ->castToString($this->testedInstance)->isEmpty()
            ->if($this->newTestedInstance(60))
            ->then
                ->castToString($this->testedInstance)->isEmpty()
            ->if($this->testedInstance->refresh('F'))
            ->then
                ->castToString($this->testedInstance)->isEqualTo('F')
                ->castToString($this->testedInstance)->isEmpty()
        ;

        for ($i = 0; $i < 58; $i++) {
            $this
                ->object($this->testedInstance->refresh('F'))->isTestedInstance
                ->castToString($this->testedInstance)->isEqualTo('F')
            ;
        }

        $this
            ->then
                ->object($this->testedInstance->refresh('F'))->isTestedInstance
                ->castToString($this->testedInstance)->isEqualTo('F [60/60]' . PHP_EOL)
                ->castToString($this->testedInstance)->isEmpty()
        ;

        $this
            ->if($this->newTestedInstance(61))
            ->then
                ->castToString($this->testedInstance)->isEmpty()
            ->if($this->testedInstance->refresh('F'))
            ->then
                ->castToString($this->testedInstance)->isEqualTo('F')
                ->castToString($this->testedInstance)->isEmpty()
        ;

        for ($i = 0; $i < 58; $i++) {
            $this
                ->object($this->testedInstance->refresh('F'))->isTestedInstance
                ->castToString($this->testedInstance)->isEqualTo('F')
            ;
        }

        $this
            ->then
                ->object($this->testedInstance->refresh('F'))->isTestedInstance
                ->castToString($this->testedInstance)->isEqualTo('F [60/61]' . PHP_EOL)
                ->castToString($this->testedInstance)->isEmpty()
            ->if($this->testedInstance->refresh('F'))
            ->then
                ->castToString($this->testedInstance)->isEqualTo('F' . str_repeat(' ', 59) . ' [61/61]' . PHP_EOL)
            ->if($this->newTestedInstance(120))
            ->then
                ->castToString($this->testedInstance)->isEmpty()
            ->if($this->testedInstance->refresh('F'))
            ->then
                ->castToString($this->testedInstance)->isEqualTo('F')
                ->castToString($this->testedInstance)->isEmpty()
        ;

        for ($i = 0; $i < 58; $i++) {
            $this
                ->object($this->testedInstance->refresh('F'))->isTestedInstance
                ->castToString($this->testedInstance)->isEqualTo('F')
            ;
        }

        $this
            ->then
                ->object($this->testedInstance->refresh('F'))->isTestedInstance
                ->castToString($this->testedInstance)->isEqualTo('F [ 60/120]' . PHP_EOL)
                ->castToString($this->testedInstance)->isEmpty()
        ;

        for ($i = 0; $i < 59; $i++) {
            $this
                ->object($this->testedInstance->refresh('F'))->isTestedInstance
                ->castToString($this->testedInstance)->isEqualTo('F')
            ;
        }

        $this
            ->then
                ->object($this->testedInstance->refresh('F'))->isTestedInstance
                ->castToString($this->testedInstance)->isEqualTo('F [120/120]' . PHP_EOL)
                ->castToString($this->testedInstance)->isEmpty()
            ->if($this->newTestedInstance(113))
            ->then
                ->castToString($this->testedInstance)->isEmpty()
            ->if($this->testedInstance->refresh('F'))
            ->then
                ->castToString($this->testedInstance)->isEqualTo('F')
                ->castToString($this->testedInstance)->isEmpty()
        ;

        for ($i = 0; $i < 58; $i++) {
            $this
                ->object($this->testedInstance->refresh('F'))->isTestedInstance
                ->castToString($this->testedInstance)->isEqualTo('F')
            ;
        }

        $this
            ->then
                ->object($this->testedInstance->refresh('F'))->isTestedInstance
                ->castToString($this->testedInstance)->isEqualTo('F [ 60/113]' . PHP_EOL)
                ->castToString($this->testedInstance)->isEmpty()
        ;

        for ($i = 0; $i < 52; $i++) {
            $this
                ->object($this->testedInstance->refresh('F'))->isTestedInstance
                ->castToString($this->testedInstance)->isEqualTo('F')
            ;
        }

        $this
            ->then
                ->object($this->testedInstance->refresh('F'))->isTestedInstance
                ->castToString($this->testedInstance)->isEqualTo('F' . str_repeat(' ', 7) . ' [113/113]' . PHP_EOL)
                ->castToString($this->testedInstance)->isEmpty()
        ;
    }
}
