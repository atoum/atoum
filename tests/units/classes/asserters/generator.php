<?php

namespace mageekguy\atoum\tests\units\asserters;

use mageekguy\atoum;
use mageekguy\atoum\asserter;
use mageekguy\atoum\tools\variable
;

require_once __DIR__ . '/../../runner.php';

class generator extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\asserters\iterator::class);
    }

    public function test__construct()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->getGenerator())->isEqualTo(new asserter\generator())
                ->object($this->testedInstance->getAnalyzer())->isEqualTo(new variable\analyzer())
                ->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
                ->variable($this->testedInstance->getValue())->isNull()
                ->boolean($this->testedInstance->wasSet())->isFalse()

            ->given($this->newTestedInstance($generator = new asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
            ->then
                ->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
                ->object($this->testedInstance->getAnalyzer())->isIdenticalTo($analyzer)
                ->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
                ->variable($this->testedInstance->getValue())->isNull()
                ->boolean($this->testedInstance->wasSet())->isFalse()
        ;
    }

    /**
     * @php < 7.0
     */
    public function testReturnsBeforePhp7()
    {
        $generator = function () {
            yield 1;
            yield 2;
        };

        $this
            ->assert('Use all yields then return')
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
            ->object($asserter->setWith($generator()))->isIdenticalTo($asserter)

            ->when($yieldAsserter = $asserter->yields)
                ->object($yieldAsserter)->isInstanceOf(atoum\asserters\variable::class)
            ->then($proxiedAsserter = $yieldAsserter->variable)
                ->object($proxiedAsserter)->isInstanceOf(atoum\asserters\generator\asserterProxy::class)
                ->integer($proxiedAsserter->getValue())->isEqualTo(1)

            ->when($yieldAsserter = $asserter->yields)
                ->object($yieldAsserter)->isInstanceOf(atoum\asserters\variable::class)
            ->then($proxiedAsserter = $yieldAsserter->variable)
                ->object($proxiedAsserter)->isInstanceOf(atoum\asserters\generator\asserterProxy::class)
                ->integer($proxiedAsserter->getValue())->isEqualTo(2)

            ->exception(function () use ($asserter) {
                $asserter->returns;
            })
                ->isInstanceOf(atoum\exceptions\logic::class)
                ->hasMessage('The returns asserter could only be used with PHP>=7.0')
        ;
    }



    /**
     * @php >= 7.0
     */
    public function testReturns()
    {
        if (version_compare(PHP_VERSION, '7.0') >= 0) {
            $generator = eval(<<<'PHP'
return function() {
    for ($i=0; $i<2; $i++) {
        yield ($i+1);
    }

    return 42;
};
PHP
            );
        }

        $this
            ->assert('Use all yields then return')
                ->given(
                    $asserter = $this->newTestedInstance
                        ->setLocale($locale = new \mock\atoum\locale())
                        ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
                )
                ->then
                ->object($asserter->setWith($generator()))->isIdenticalTo($asserter)

                ->when($yieldAsserter = $asserter->yields)
                    ->object($yieldAsserter)->isInstanceOf(atoum\asserters\generator::class)
                ->then($proxyfiedAsserter = $yieldAsserter->variable)
                    ->object($proxyfiedAsserter)->isInstanceOf(atoum\asserters\generator\asserterProxy::class)
                    ->integer($proxyfiedAsserter->getValue())->isEqualTo(1)

                ->when($yieldAsserter = $asserter->yields)
                    ->object($yieldAsserter)->isInstanceOf(atoum\asserters\generator::class)
                ->then($proxyfiedAsserter = $yieldAsserter->variable)
                    ->object($proxyfiedAsserter)->isInstanceOf(atoum\asserters\generator\asserterProxy::class)
                    ->integer($proxyfiedAsserter->getValue())->isEqualTo(2)

                ->when($returnedAsserter = $asserter->returns)
                    ->object($returnedAsserter)->isInstanceOf(atoum\asserters\generator::class)
                ->then($proxyfiedAsserter = $returnedAsserter->variable)
                    ->object($proxyfiedAsserter)->isInstanceOf(atoum\asserters\generator\asserterProxy::class)
                    ->integer($proxyfiedAsserter->getValue())->isEqualTo(42)

            ->assert('Use return before all yields')
                ->given(
                    $asserter = $this->newTestedInstance
                        ->setLocale($locale = new \mock\atoum\locale())
                        ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
                )
                ->then
                    ->object($asserter->setWith($generator()))->isIdenticalTo($asserter)

                ->when($yieldAsserter = $asserter->yields)
                    ->object($yieldAsserter)->isInstanceOf(atoum\asserters\generator::class)
                ->then($proxyfiedAsserter = $yieldAsserter->variable)
                    ->integer($proxyfiedAsserter->getValue())->isEqualTo(1)


                ->exception(function () use ($asserter) {
                    $asserter->returns;
                })
                    ->isInstanceOf(\exception::class)
                    ->hasMessage("Cannot get return value of a generator that hasn't returned")
        ;
    }

    public function testYields()
    {
        $generator = function () {
            for ($i=0; $i<10; $i++) {
                yield ($i+1);
            }
        };

        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->object($asserter->setWith($generator()))->isIdenticalTo($asserter)

            ->when($yieldAsserter = $asserter->yields)
                ->object($yieldAsserter)->isInstanceOf(atoum\asserters\variable::class)
            ->then($proxyfiedAsserter = $yieldAsserter->variable)
                ->object($proxyfiedAsserter)->isInstanceOf(atoum\asserters\generator\asserterProxy::class)
                ->integer($proxyfiedAsserter->getValue())->isEqualTo(1)

            ->when($yieldAsserter = $asserter->yields)
                ->object($yieldAsserter)->isInstanceOf(atoum\asserters\variable::class)
            ->then($proxyfiedAsserter = $yieldAsserter->variable)
                ->object($proxyfiedAsserter)->isInstanceOf(atoum\asserters\generator\asserterProxy::class)
                ->integer($proxyfiedAsserter->getValue())->isEqualTo(2)
        ;
    }

    public function testSetWith()
    {
        $generator = function () {
            for ($i=0; $i<10; $i++) {
                yield ($i+1);
            }
        };

        $notAGenerator = function () {
            for ($i=0; $i<10; $i++) {
            }
        };

        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->object($asserter->setWith($generator()))->isIdenticalTo($asserter)

            ->then
                ->exception(function () use ($asserter) {
                    $asserter->setWith(true);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage("boolean(true) is not an object")

            ->then
                ->exception(function () use ($asserter, $notAGenerator) {
                    $asserter->setWith($notAGenerator());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage("null is not an object")

            ->then
                ->exception(function () use ($asserter) {
                    $asserter->setWith(new \stdClass());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage("object(stdClass) is not an iterator")

            ->then
                ->exception(function () use ($asserter) {
                    $asserter->setWith(new \ArrayIterator());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage("object(ArrayIterator) is not a generator")
        ;
    }
}
