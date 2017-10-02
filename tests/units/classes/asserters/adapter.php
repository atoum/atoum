<?php

namespace mageekguy\atoum\tests\units\asserters;

use mageekguy\atoum;
use mageekguy\atoum\asserter;
use mageekguy\atoum\test;
use mageekguy\atoum\tools\variable;

require_once __DIR__ . '/../../runner.php';

class adapter extends atoum\test
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
                ->object($this->testedInstance->getGenerator())->isEqualTo(new atoum\asserter\generator())
                ->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
                ->object($this->testedInstance->getAnalyzer())->isEqualTo(new atoum\tools\variable\analyzer())
                ->variable($this->testedInstance->getAdapter())->isNull()
                ->variable($this->testedInstance->getCall())->isEqualTo(new test\adapter\call())

            ->given($this->newTestedInstance($generator = new atoum\asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
            ->then
                ->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
                ->object($this->testedInstance->getAnalyzer())->isEqualTo($analyzer)
                ->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
                ->variable($this->testedInstance->getAdapter())->isNull()
                ->variable($this->testedInstance->getCall())->isEqualTo(new test\adapter\call())
        ;
    }

    public function testSetWith()
    {
        $this
            ->given(
                $this->newTestedInstance($generator = new asserter\generator())
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )

            ->if(
                $this->calling($locale)->_ = $notAnAdapter = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid()
            )
            ->then
                ->exception(function () use (& $value) {
                    $this->testedInstance->setWith($value = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notAnAdapter)
                ->mock($locale)->call('_')->withArguments('%s is not a test adapter', $type)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments($value)->once
                ->string($this->testedInstance->getAdapter())->isEqualTo($value)

                ->object($this->testedInstance->setWith($adapter = new test\adapter()))->isTestedInstance
                ->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
        ;
    }

    public function testReset()
    {
        $this
            ->if($this->newTestedInstance(new asserter\generator()))
            ->then
                ->variable($this->testedInstance->getAdapter())->isNull()
                ->object($this->testedInstance->reset())->isTestedInstance
                ->variable($this->testedInstance->getAdapter())->isNull()

            ->if($this->testedInstance->setWith($adapter = new atoum\test\adapter()))
            ->then
                ->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
                ->sizeOf($adapter->getCalls())->isZero()
                ->object($this->testedInstance->reset())->isTestedInstance
                ->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
                ->sizeOf($adapter->getCalls())->isZero()

            ->if($adapter->md5(uniqid()))
            ->then
                ->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
                ->sizeOf($adapter->getCalls())->isEqualTo(1)
                ->object($this->testedInstance->reset())->isTestedInstance
                ->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
                ->sizeOf($adapter->getCalls())->isZero()
        ;
    }

    public function testCall()
    {
        $this
            ->mockGenerator->orphanize('asserterFail')
            ->if($this->newTestedInstance(new \mock\mageekguy\atoum\asserter\generator()))
            ->then
                ->exception(function () {
                    $this->testedInstance->call(uniqid());
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

            ->if($this->testedInstance->setWith($adapter = new test\adapter()))
            ->then
                ->object($this->testedInstance->call($function = uniqid()))->isTestedInstance
                ->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function))

            ->if($this->testedInstance->withArguments())
            ->then
                ->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function, []))
                ->object($this->testedInstance->disableEvaluationChecking()->call($function = uniqid()))->isTestedInstance
                ->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function))
        ;
    }

    public function testWithArguments()
    {
        $this
            ->mockGenerator->orphanize('asserterFail')
            ->if($this->newTestedInstance(new \mock\mageekguy\atoum\asserter\generator()))
            ->then
                ->exception(function () {
                    $this->testedInstance->withArguments(uniqid());
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

            ->if($this->testedInstance->setWith($adapter = new test\adapter()))
            ->then
                ->exception(function () {
                    $this->testedInstance->withArguments(uniqid());
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

            ->if($this->testedInstance->call($function = uniqid()))
            ->then
                ->object($this->testedInstance->withArguments())->isTestedInstance
                ->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function, []))
                ->object($this->testedInstance->withArguments($arg1 = uniqid()))->isTestedInstance
                ->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function, [$arg1]))
                ->object($this->testedInstance->disableEvaluationChecking()->withArguments($arg1 = uniqid(), $arg2 = uniqid()))->isTestedInstance
                ->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function, [$arg1, $arg2]))
        ;
    }

    public function testWithAnyArguments()
    {
        $this
            ->mockGenerator->orphanize('asserterFail')
            ->if($this->newTestedInstance(new \mock\mageekguy\atoum\asserter\generator()))
            ->then
                ->exception(function () {
                    $this->testedInstance->withArguments(uniqid());
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

            ->if($this->testedInstance->setWith($adapter = new test\adapter()))
            ->then
                ->exception(function () {
                    $this->testedInstance->withArguments(uniqid());
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

            ->if($this->testedInstance->call($function = uniqid()))
            ->then
                ->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function))
                ->object($this->testedInstance->withAnyArguments())->isTestedInstance
                ->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function))

            ->if($this->testedInstance->disableEvaluationChecking()->withArguments($arg = uniqid()))
            ->then
                ->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function, [$arg]))
                ->object($this->testedInstance->withAnyArguments())->isTestedInstance
                ->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function))
        ;
    }

    public function testWithoutAnyArgument()
    {
        $this
            ->mockGenerator->orphanize('asserterFail')
            ->if($this->newTestedInstance(new \mock\mageekguy\atoum\asserter\generator()))
            ->then
                ->exception(function () {
                    $this->testedInstance->withoutAnyArgument();
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

            ->if($this->testedInstance->setWith($adapter = new test\adapter()))
            ->then
                ->exception(function () {
                    $this->testedInstance->withoutAnyArgument();
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

            ->if($this->testedInstance->call($function = uniqid()))
            ->then
                ->object($this->testedInstance->disableEvaluationChecking()->withoutAnyArgument())->isTestedInstance
                ->object($this->testedInstance->getCall())->isEqualTo(new test\adapter\call($function, []))
        ;
    }

    public function testOnce()
    {
        $this
            ->if($this->newTestedInstance($generator = new asserter\generator()))
            ->then
                ->exception(function () {
                    $this->testedInstance->once();
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

                ->exception(function () {
                    $this->testedInstance->once;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

                ->exception(function () {
                    $this->testedInstance->oNCE;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

            ->if($this->testedInstance->setWith($adapter = new \mock\atoum\test\adapter()))
            ->then
                ->exception(function () {
                    $this->testedInstance->once();
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

                ->exception(function () {
                    $this->testedInstance->once;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

                ->exception(function () {
                    $this->testedInstance->oNCE;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

            ->if(
                $this->testedInstance
                    ->call(uniqid())
                    ->setCall($call = new \mock\atoum\test\adapter\call())
                    ->setLocale($locale = new \mock\atoum\locale()),
                $this->calling($adapter)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
                $this->calling($calls)->count = 0,
                $this->calling($call)->__toString = $callAsString = uniqid(),
                $this->calling($locale)->__ = $notCalled = uniqid()
            )
            ->then
                ->exception(function () {
                    $this->testedInstance->once();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 1)->once

                ->exception(function () {
                    $this->testedInstance->once;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 1)->twice

                ->exception(function () {
                    $this->testedInstance->OncE;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 1)->thrice

                ->exception(function () use (& $failMessage) {
                    $this->testedInstance->once($failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->if($this->calling($calls)->count = 1)
            ->then
                ->object($this->testedInstance->once())->isTestedInstance
                ->object($this->testedInstance->once)->isTestedInstance
                ->object($this->testedInstance->oNCE)->isTestedInstance

            ->if(
                $this->calling($calls)->count = $count = rand(2, PHP_INT_MAX),
                $this->calling($adapter)->getCallsEqualTo = $callsEqualTo = new \mock\atoum\test\adapter\calls(),
                $this->calling($callsEqualTo)->count = rand(1, PHP_INT_MAX),
                $this->calling($callsEqualTo)->__toString = $callsEqualToAsString = uniqid()
            )
            ->then
                ->exception(function () {
                    $this->testedInstance->once();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 1)->once

                ->exception(function () {
                    $this->testedInstance->once;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 1)->twice

                ->exception(function () {
                    $this->testedInstance->OncE;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 1)->thrice

                ->exception(function () use (& $failMessage) {
                    $this->testedInstance->once($failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)
        ;
    }

    public function testTwice()
    {
        $this
            ->if($this->newTestedInstance($generator = new asserter\generator()))
            ->then
                ->exception(function () {
                    $this->testedInstance->twice();
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

                ->exception(function () {
                    $this->testedInstance->twice;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

                ->exception(function () {
                    $this->testedInstance->TWICe;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

            ->if($this->testedInstance->setWith($adapter = new \mock\atoum\test\adapter()))
            ->then
                ->exception(function () {
                    $this->testedInstance->twice();
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

                ->exception(function () {
                    $this->testedInstance->twice;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

                ->exception(function () {
                    $this->testedInstance->twICE;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

            ->if(
                $this->testedInstance
                    ->call(uniqid())
                    ->setCall($call = new \mock\atoum\test\adapter\call())
                    ->setLocale($locale = new \mock\atoum\locale()),
                $this->calling($adapter)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
                $this->calling($calls)->count = 0,
                $this->calling($call)->__toString = $callAsString = uniqid(),
                $this->calling($locale)->__ = $notCalled = uniqid()
            )
            ->then
                ->exception(function () {
                    $this->testedInstance->twice();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 2)->once

                ->exception(function () {
                    $this->testedInstance->twice;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 2)->twice

                ->exception(function () {
                    $this->testedInstance->TWICe;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 2)->thrice

                ->exception(function () use (& $failMessage) {
                    $this->testedInstance->twice($failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->if(
                $this->calling($calls)->count = 1,
                $this->calling($adapter)->getCallsEqualTo = $callsEqualTo = new \mock\atoum\test\adapter\calls(),
                $this->calling($callsEqualTo)->count = 1,
                $this->calling($callsEqualTo)->__toString = $callsEqualToAsString = uniqid()
            )
            ->then
                ->exception(function () {
                    $this->testedInstance->twice();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 1, $callAsString, 1, 2)->once

                ->exception(function () {
                    $this->testedInstance->twice;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 1, $callAsString, 1, 2)->twice

                ->exception(function () {
                    $this->testedInstance->TWICe;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 1, $callAsString, 1, 2)->thrice

                ->exception(function () use (& $failMessage) {
                    $this->testedInstance->twice($failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->if($this->calling($calls)->count = 2)
            ->then
                ->object($this->testedInstance->twice())->isTestedInstance
                ->object($this->testedInstance->twice)->isTestedInstance
                ->object($this->testedInstance->TWICe)->isTestedInstance

            ->if(
                $this->calling($calls)->count = $count = rand(3, PHP_INT_MAX),
                $this->calling($adapter)->getCallsEqualTo = $callsEqualTo = new \mock\atoum\test\adapter\calls(),
                $this->calling($callsEqualTo)->count = rand(1, PHP_INT_MAX),
                $this->calling($callsEqualTo)->__toString = $callsEqualToAsString = uniqid()
            )
            ->then
                ->exception(function () {
                    $this->testedInstance->twice();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 2)->once

                ->exception(function () {
                    $this->testedInstance->twice;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 2)->twice

                ->exception(function () {
                    $this->testedInstance->TWICe;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 2)->thrice

                ->exception(function () use (& $failMessage) {
                    $this->testedInstance->once($failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)
        ;
    }

    public function testThrice()
    {
        $this
            ->if($this->newTestedInstance($generator = new asserter\generator()))
            ->then
                ->exception(function () {
                    $this->testedInstance->thrice();
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

                ->exception(function () {
                    $this->testedInstance->thrice;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

                ->exception(function () {
                    $this->testedInstance->tHRICe;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

            ->if($this->testedInstance->setWith($adapter = new \mock\atoum\test\adapter()))
            ->then
                ->exception(function () {
                    $this->testedInstance->thrice();
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

                ->exception(function () {
                    $this->testedInstance->thrice;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

                ->exception(function () {
                    $this->testedInstance->thRICE;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

            ->if(
                $this->testedInstance
                    ->call(uniqid())
                    ->setCall($call = new \mock\atoum\test\adapter\call())
                    ->setLocale($locale = new \mock\atoum\locale()),
                $this->calling($adapter)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
                $this->calling($calls)->count = 0,
                $this->calling($call)->__toString = $callAsString = uniqid(),
                $this->calling($locale)->__ = $notCalled = uniqid()
            )
            ->then
                ->exception(function () {
                    $this->testedInstance->thrice();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 3)->once

                ->exception(function () {
                    $this->testedInstance->thrice;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 3)->twice

                ->exception(function () {
                    $this->testedInstance->THRIce;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, 3)->thrice

                ->exception(function () use (& $failMessage) {
                    $this->testedInstance->thrice($failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->if(
                $this->calling($calls)->count = $count = rand(1, 2),
                $this->calling($adapter)->getCallsEqualTo = $callsEqualTo = new \mock\atoum\test\adapter\calls(),
                $this->calling($callsEqualTo)->count = $count,
                $this->calling($callsEqualTo)->__toString = $callsEqualToAsString = uniqid()
            )
            ->then
                ->exception(function () {
                    $this->testedInstance->thrice();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 3)->once

                ->exception(function () {
                    $this->testedInstance->thrice;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 3)->twice

                ->exception(function () {
                    $this->testedInstance->tHRICe;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 3)->thrice

                ->exception(function () use (& $failMessage) {
                    $this->testedInstance->thrice($failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->if($this->calling($calls)->count = 3)
            ->then
                ->object($this->testedInstance->thrice())->isTestedInstance
                ->object($this->testedInstance->thrice)->isTestedInstance
                ->object($this->testedInstance->THRIcE)->isTestedInstance

            ->if(
                $this->calling($calls)->count = $count = rand(3, PHP_INT_MAX),
                $this->calling($adapter)->getCallsEqualTo = $callsEqualTo = new \mock\atoum\test\adapter\calls(),
                $this->calling($callsEqualTo)->count = $count,
                $this->calling($callsEqualTo)->__toString = $callsEqualToAsString = uniqid()
            )
            ->then
                ->exception(function () {
                    $this->testedInstance->thrice();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 3)->once

                ->exception(function () {
                    $this->testedInstance->thrice;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 3)->twice

                ->exception(function () {
                    $this->testedInstance->tHRICe;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled . PHP_EOL . $callsEqualTo)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 3)->thrice

                ->exception(function () use (& $failMessage) {
                    $this->testedInstance->thrice($failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)
        ;
    }

    public function testAtLeastOnce()
    {
        $this
            ->if($this->newTestedInstance($generator = new asserter\generator()))
            ->then
                ->exception(function () {
                    $this->testedInstance->atLeastOnce();
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

                ->exception(function () {
                    $this->testedInstance->atLeastOnce;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

                ->exception(function () {
                    $this->testedInstance->atLEASToNce;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

            ->if($this->testedInstance->setWith($adapter = new \mock\atoum\test\adapter()))
            ->then
                ->exception(function () {
                    $this->testedInstance->atLeastOnce();
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

                ->exception(function () {
                    $this->testedInstance->atLeastOnce;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

                ->exception(function () {
                    $this->testedInstance->atLeASTonce;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

            ->if(
                $this->testedInstance
                    ->call(uniqid())
                    ->setCall($call = new \mock\atoum\test\adapter\call())
                    ->setLocale($locale = new \mock\atoum\locale()),
                $this->calling($adapter)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
                $this->calling($calls)->count = 0,
                $this->calling($call)->__toString = $callAsString = uniqid(),
                $this->calling($locale)->_ = $notCalled = uniqid()
            )
            ->then
                ->exception(function () {
                    $this->testedInstance->atLeastOnce();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled)
                ->mock($locale)->call('_')->withArguments('%s is called 0 time', $callAsString)->once

                ->exception(function () {
                    $this->testedInstance->atLeastOnce;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled)
                ->mock($locale)->call('_')->withArguments('%s is called 0 time', $callAsString)->twice

                ->exception(function () {
                    $this->testedInstance->atLEASToNCE;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled)
                ->mock($locale)->call('_')->withArguments('%s is called 0 time', $callAsString)->thrice

                ->exception(function () use (& $failMessage) {
                    $this->testedInstance->atLeastOnce($failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->if($this->calling($calls)->count = rand(1, PHP_INT_MAX))
            ->then
                ->object($this->testedInstance->atLeastOnce())->isTestedInstance
                ->object($this->testedInstance->atLeastOnce)->isTestedInstance
                ->object($this->testedInstance->atLEASToNCe)->isTestedInstance
        ;
    }

    public function testExactly()
    {
        $this
            ->if($this->newTestedInstance($generator = new asserter\generator()))
            ->then
                ->exception(function () {
                    $this->testedInstance->exactly(2);
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

            ->if($this->testedInstance->setWith($adapter = new \mock\atoum\test\adapter()))
            ->then
                ->exception(function () {
                    $this->testedInstance->exactly(2);
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

            ->if(
                $this->testedInstance
                    ->call(uniqid())
                    ->setCall($call = new \mock\atoum\test\adapter\call())
                    ->setLocale($locale = new \mock\atoum\locale()),
                $this->calling($adapter)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
                $this->calling($calls)->count = 0,
                $this->calling($call)->__toString = $callAsString = uniqid(),
                $this->calling($locale)->__ = $notCalled = uniqid()
            )
            ->then
                ->exception(function () use (& $callNumber) {
                    $this->testedInstance->exactly($callNumber = rand(1, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', 0, $callAsString, 0, $callNumber)->once

                ->exception(function () use (& $callNumber, & $failMessage) {
                    $this->testedInstance->exactly($callNumber = rand(1, PHP_INT_MAX), $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->object($this->testedInstance->exactly(0))->isTestedInstance

            ->if(
                $this->calling($calls)->count = $count = rand(1, PHP_INT_MAX),
                $this->calling($adapter)->getCallsEqualTo = $callsEqualTo = new \mock\atoum\test\adapter\calls(),
                $this->calling($callsEqualTo)->count = $count,
                $this->calling($callsEqualTo)->__toString = $callsEqualToAsString = uniqid()
            )
            ->then
                ->exception(function () {
                    $this->testedInstance->exactly(0);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notCalled . PHP_EOL . $callsEqualToAsString)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 0)->once

                ->exception(function () use (& $failMessage) {
                    $this->testedInstance->exactly(0, $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->object($this->testedInstance->exactly($count))->isTestedInstance
        ;
    }

    public function testNever()
    {
        $this
            ->if($this->newTestedInstance($generator = new asserter\generator()))
            ->then
                ->exception(function () {
                    $this->testedInstance->never();
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

                ->exception(function () {
                    $this->testedInstance->never;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Adapter is undefined')

            ->if($this->testedInstance->setWith($adapter = new \mock\atoum\test\adapter()))
            ->then
                ->exception(function () {
                    $this->testedInstance->never();
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

                ->exception(function () {
                    $this->testedInstance->never;
                })
                    ->isInstanceOf(atoum\asserters\adapter\exceptions\logic::class)
                    ->hasMessage('Call is undefined')

            ->if(
                $this->testedInstance
                    ->call(uniqid())
                    ->setCall($call = new \mock\atoum\test\adapter\call())
                    ->setLocale($locale = new \mock\atoum\locale()),
                $this->calling($adapter)->getCalls = $calls = new \mock\atoum\test\adapter\calls(),
                $this->calling($calls)->count = $count = rand(1, PHP_INT_MAX),
                $this->calling($call)->__toString = $callAsString = uniqid(),
                $this->calling($locale)->__ = $wasCalled = uniqid()
            )
            ->then
                ->exception(function () use (& $callNumber) {
                    $this->testedInstance->never();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($wasCalled)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 0)->once

                ->exception(function () use (& $callNumber) {
                    $this->testedInstance->never;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($wasCalled)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 0)->twice

                ->exception(function () use (& $callNumber) {
                    $this->testedInstance->NEvEr;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($wasCalled)
                ->mock($locale)->call('__')->withArguments('%s is called %d time instead of %d', '%s is called %d times instead of %d', $count, $callAsString, $count, 0)->thrice

                ->exception(function () use (& $failMessage) {
                    $this->testedInstance->never($failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->if($this->calling($calls)->count = 0)
            ->then
                ->object($this->testedInstance->never())->isTestedInstance
                ->object($this->testedInstance->never)->isTestedInstance
                ->object($this->testedInstance->nEVER)->isTestedInstance
        ;
    }

    public function testBefore()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance($generator = new atoum\asserter\generator()),
                $adapter = new test\adapter(),
                $adapter->shouldBeCallBefore = uniqid(),
                $asserter->setWith($adapter),
                $beforeAsserter = $this->newTestedInstance(new atoum\asserter\generator()),
                $beforeAdapter = new test\adapter(),
                $beforeAdapter->wasCalledAfter = uniqid(),
                $beforeAsserter->setWith($beforeAdapter),
                $asserter->call('shouldBeCallBefore')->before($beforeAsserter->call('wasCalledAfter'))
            )

            ->if(
                $adapter->shouldBeCallBefore(),
                $beforeAdapter->wasCalledAfter()
            )
            ->then
                ->object($asserter->once())->isIdenticalTo($asserter)

            ->if(
                $adapter->resetCalls(),
                $beforeAdapter->resetCalls(),
                $beforeAdapter->wasCalledAfter(),
                $adapter->shouldBeCallBefore()
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->once();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage(sprintf($generator->getLocale()->_('%s is not called before %s'), $asserter->getCall(), $beforeAsserter->getCall()))

            ->if(
                $adapter->resetCalls(),
                $beforeAdapter->resetCalls(),
                $beforeAdapter->wasCalledAfter(),
                $beforeAdapter->wasCalledAfter(),
                $adapter->shouldBeCallBefore()
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->once();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage(sprintf($generator->getLocale()->_('%s is not called before %s'), $asserter->getCall(), $beforeAsserter->getCall()))

            ->if(
                $adapter->resetCalls(),
                $beforeAdapter->resetCalls(),
                $adapter->shouldBeCallBefore(),
                $beforeAdapter->wasCalledAfter(),
                $beforeAdapter->wasCalledAfter(),
                $adapter->shouldBeCallBefore()
            )
            ->then
                ->object($asserter->once())->isIdenticalTo($asserter)
                ->exception(function () use ($asserter) {
                    $asserter->twice();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2 before %s'), $asserter->getCall(), $beforeAsserter->getCall()))

            ->if(
                $adapter->resetCalls(),
                $beforeAdapter->resetCalls(),
                $adapter->shouldBeCallBefore(),
                $beforeAdapter->wasCalledAfter(),
                $adapter->shouldBeCallBefore(),
                $beforeAdapter->wasCalledAfter()
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->once();
                })

            ->if(
                $adapter->resetCalls(),
                $beforeAdapter->resetCalls(),
                $adapter->shouldBeCallBefore(),
                $beforeAdapter->wasCalledAfter(),
                $beforeAdapter->wasCalledAfter()
            )
            ->then
                ->object($asserter->once())->isIdenticalTo($asserter)

            ->if(
                $adapter->resetCalls(),
                $beforeAdapter->resetCalls(),
                $adapter->shouldBeCallBefore(),
                $adapter->shouldBeCallBefore(),
                $beforeAdapter->wasCalledAfter(),
                $beforeAdapter->wasCalledAfter()
            )
            ->then
                ->object($asserter->twice())->isIdenticalTo($asserter)
        ;
    }

    public function testAfter()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance($generator = new atoum\asserter\generator()),
                $adapter = new test\adapter(),
                $adapter->shouldBeCallafter = uniqid(),
                $asserter->setWith($adapter),
                $afterAsserter = $this->newTestedInstance(new atoum\asserter\generator()),
                $afterAdapter = new test\adapter(),
                $afterAdapter->wasCalledBefore = uniqid(),
                $afterAsserter->setWith($afterAdapter),
                $asserter->call('shouldBeCallAfter')->after($afterAsserter->call('wasCalledBefore')),
                $afterAdapter->wasCalledBefore(),
                $adapter->shouldBeCallAfter()
            )
            ->then
                ->object($asserter->once())->isIdenticalTo($asserter)

            ->if(
                $adapter->resetCalls(),
                $afterAdapter->resetCalls(),
                $adapter->shouldBeCallAfter(),
                $afterAdapter->wasCalledBefore()
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->once();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage(sprintf($generator->getLocale()->_('%s is not called after %s'), $asserter->getCall(), $afterAsserter->getCall()))

            ->if(
                $adapter->resetCalls(),
                $afterAdapter->resetCalls(),
                $adapter->shouldBeCallAfter(),
                $adapter->shouldBeCallAfter(),
                $afterAdapter->wasCalledBefore()
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->once();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage(sprintf($generator->getLocale()->_('%s is not called after %s'), $asserter->getCall(), $afterAsserter->getCall()))

            ->if(
                $adapter->resetCalls(),
                $afterAdapter->resetCalls(),
                $adapter->shouldBeCallAfter(),
                $afterAdapter->wasCalledBefore(),
                $adapter->shouldBeCallAfter()
            )
            ->then
                ->object($asserter->once())->isIdenticalTo($asserter)
                ->exception(function () use ($asserter) {
                    $asserter->twice();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage(sprintf($generator->getLocale()->_('%s is called 1 time instead of 2 after %s'), $asserter->getCall(), $afterAsserter->getCall()))

            ->if(
                $adapter->resetCalls(),
                $afterAdapter->resetCalls(),
                $afterAdapter->wasCalledBefore(),
                $adapter->shouldBeCallAfter(),
                $afterAdapter->wasCalledBefore(),
                $adapter->shouldBeCallAfter()
            )
            ->then
                ->object($asserter->twice())->isIdenticalTo($asserter)

            ->if(
                $adapter->resetCalls(),
                $afterAdapter->resetCalls(),
                $afterAdapter->wasCalledBefore(),
                $adapter->shouldBeCallAfter(),
                $afterAdapter->wasCalledBefore()
            )
            ->then
                ->object($asserter->once())->isIdenticalTo($asserter)

            ->if(
                $adapter->resetCalls(),
                $afterAdapter->resetCalls(),
                $afterAdapter->wasCalledBefore(),
                $adapter->shouldBeCallAfter(),
                $afterAdapter->wasCalledBefore(),
                $adapter->shouldBeCallAfter()
            )
            ->then
                ->object($asserter->twice())->isIdenticalTo($asserter)

            ->if(
                $adapter->resetCalls(),
                $afterAdapter->resetCalls(),
                $afterAdapter->wasCalledBefore(),
                $adapter->shouldBeCallAfter(),
                $adapter->shouldBeCallAfter(),
                $afterAdapter->wasCalledBefore()
            )
            ->then
                ->object($asserter->twice())->isIdenticalTo($asserter)

            ->if(
                $adapter->resetCalls(),
                $afterAdapter->resetCalls(),
                $afterAdapter->wasCalledBefore(),
                $afterAdapter->wasCalledBefore(),
                $adapter->shouldBeCallAfter()
            )
            ->then
                ->object($asserter->once())->isIdenticalTo($asserter)
        ;
    }
}
