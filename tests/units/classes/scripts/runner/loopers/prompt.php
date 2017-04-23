<?php

namespace mageekguy\atoum\tests\units\scripts\runner\loopers;

use mageekguy\atoum;

class prompt extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\scripts\runner\looper::class);
    }

    public function test__construct(atoum\cli $cli, atoum\writer $writer, atoum\script\prompt $prompt, atoum\locale $locale)
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->getCli())->isInstanceof(atoum\cli::class)
                ->object($this->testedInstance->getOutputWriter())->isInstanceof(atoum\writer::class)
                ->object($this->testedInstance->getPrompt())->isInstanceOf(atoum\script\prompt::class)
                ->object($this->testedInstance->getLocale())->isInstanceOf(atoum\locale::class)
            ->if($this->newTestedInstance($prompt, $writer, $cli, $locale))
            ->then
                ->object($this->testedInstance->getCli())->isIdenticalTo($cli)
                ->object($this->testedInstance->getOutputWriter())->isIdenticalTo($writer)
                ->object($this->testedInstance->getPrompt())->isIdenticalTo($prompt)
                ->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
        ;
    }

    public function testGetSetCli(atoum\cli $cli)
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->setCli())->isTestedInstance
                ->object($this->testedInstance->getCli())->isInstanceof(atoum\cli::class)
                ->object($this->testedInstance->setCli($cli))->isTestedInstance
                ->object($this->testedInstance->getCli())->isIdenticalTo($cli)
        ;
    }

    public function testGetSetOutputWriter(atoum\cli $cli, atoum\writer $writer)
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->setOutputWriter())->isTestedInstance
                ->object($this->testedInstance->getOutputWriter())->isInstanceof(atoum\writer::class)
                ->object($this->testedInstance->setOutputWriter($writer))->isTestedInstance
                ->object($this->testedInstance->getOutputWriter())->isIdenticalTo($writer)
            ->if($this->testedInstance->setCli($cli))
            ->then
                ->object($this->testedInstance->setOutputWriter())->isTestedInstance
                ->object($this->testedInstance->getOutputWriter())->isInstanceof(atoum\writer::class)
                ->object($this->testedInstance->getOutputWriter()->getCli())->isIdenticalTo($cli)
        ;
    }

    public function testGetSetPrompt(atoum\writer $writer, atoum\script\prompt $prompt)
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->setPrompt())->isTestedInstance
                ->object($this->testedInstance->getPrompt())->isInstanceof(atoum\script\prompt::class)
                ->object($this->testedInstance->getPrompt()->getOutputWriter())->isInstanceof(atoum\writer::class)
                ->object($this->testedInstance->setPrompt($prompt))->isTestedInstance
                ->object($this->testedInstance->getPrompt())->isIdenticalTo($prompt)
                ->object($this->testedInstance->getPrompt()->getOutputWriter())->isInstanceof(atoum\writer::class)
            ->if($this->testedInstance->setOutputWriter($writer))
            ->then
                ->object($this->testedInstance->setPrompt())->isTestedInstance
                ->object($this->testedInstance->getPrompt())->isInstanceof(atoum\script\prompt::class)
                ->object($this->testedInstance->getPrompt()->getOutputWriter())->isIdenticalTo($writer)
        ;
    }

    public function testGetSetLocale(atoum\locale $locale)
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->setLocale())->isTestedInstance
                ->object($this->testedInstance->getLocale())->isInstanceof(atoum\locale::class)
                ->object($this->testedInstance->setLocale($locale))->isTestedInstance
                ->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
        ;
    }

    public function testRunAgain(atoum\script\prompt $prompt, atoum\locale $locale)
    {
        $this
            ->given(
                $this->newTestedInstance($prompt, null, null, $locale),
                $this->calling($locale)->_ = $promptMessage = uniqid()
            )
            ->if($this->calling($prompt)->ask = uniqid())
            ->then
                ->boolean($this->testedInstance->runAgain())->isFalse
                ->mock($prompt)
                    ->call('ask')->withArguments($promptMessage)->once
                ->mock($locale)
                    ->call('_')->withArguments('Press <Enter> to reexecute, press any other key and <Enter> to stop...')->once
            ->if($this->calling($prompt)->ask = '')
            ->then
                ->boolean($this->testedInstance->runAgain())->isTrue
                ->mock($prompt)
                    ->call('ask')->withArguments($promptMessage)->twice
                ->mock($locale)
                    ->call('_')->withArguments('Press <Enter> to reexecute, press any other key and <Enter> to stop...')->twice
        ;
    }
}
