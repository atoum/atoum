<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\atoum;

use mageekguy\atoum\cli\colorizer;
use mageekguy\atoum\cli\prompt;
use mageekguy\atoum\locale;
use mageekguy\atoum\report\fields\runner\atoum\cli as testedClass;
use mageekguy\atoum\runner;
use mageekguy\atoum\runner\score;

require_once __DIR__ . '/../../../../../runner.php';

class cli extends \mageekguy\atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends('mageekguy\atoum\report\field');
    }

    public function test__construct()
    {
        $this
            ->if($field = new testedClass())
            ->then
                ->object($field->getPrompt())->isEqualTo(new prompt())
                ->object($field->getColorizer())->isEqualTo(new colorizer())
                ->object($field->getLocale())->isEqualTo(new locale())
                ->variable($field->getAuthor())->isNull()
                ->variable($field->getPath())->isNull()
                ->variable($field->getVersion())->isNull()
                ->array($field->getEvents())->isEqualTo([runner::runStart])
        ;
    }

    public function testSetPrompt()
    {
        $this
            ->if($field = new testedClass())
            ->then
                ->object($field->setPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
                ->object($field->getPrompt())->isIdenticalTo($prompt)
                ->object($field->setPrompt())->isIdenticalTo($field)
                ->object($field->getPrompt())
                    ->isNotIdenticalTo($prompt)
                    ->isEqualTo(new prompt())
        ;
    }

    public function testSetColorizer()
    {
        $this
            ->if($field = new testedClass())
            ->then
                ->object($field->setColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
                ->object($field->getColorizer())->isIdenticalTo($colorizer)
                ->object($field->setColorizer())->isIdenticalTo($field)
                ->object($field->getColorizer())
                    ->isNotIdenticalTo($colorizer)
                    ->isEqualTo(new colorizer())
        ;
    }

    public function testHandleEvent()
    {
        $this
            ->if($score = new score())
            ->and($score
                ->setAtoumPath($atoumPath = uniqid())
                ->setAtoumVersion($atoumVersion = uniqid())
            )
            ->and($runner = new runner())
            ->and($runner->setScore($score))
            ->and($field = new testedClass())
            ->then
                ->variable($field->getAuthor())->isNull()
                ->variable($field->getPath())->isNull()
                ->variable($field->getVersion())->isNull()
                ->boolean($field->handleEvent(runner::runStart, $runner))->isTrue()
                ->string($field->getAuthor())->isEqualTo(\mageekguy\atoum\author)
                ->string($field->getPath())->isEqualTo($atoumPath)
                ->string($field->getVersion())->isEqualTo($atoumVersion)
        ;
    }

    public function test__toString()
    {
        $this
            ->if($score = new score())
            ->and($score
                ->setAtoumPath($atoumPath = uniqid())
                ->setAtoumVersion($atoumVersion = uniqid())
            )
            ->and($runner = new runner())
            ->and($runner->setScore($score))
            ->and($field = new testedClass())
            ->and($field->handleEvent(runner::runStop, $runner))
            ->then
                ->castToString($field)->isEmpty()
            ->if($field->handleEvent(runner::runStart, $runner))
            ->then
                ->castToString($field)->isEqualTo($field->getPrompt() . $field->getColorizer()->colorize(sprintf($field->getLocale()->_('atoum version %s by %s (%s)'), $atoumVersion, \mageekguy\atoum\author, $atoumPath)) . PHP_EOL)
        ;
    }
}
