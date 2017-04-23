<?php

namespace mageekguy\atoum\tests\units\factory\builder;

require __DIR__ . '/../../../runner.php';

use mageekguy\atoum;

class classWithoutConstructor
{
}

class classWithProtectedConstructor
{
    protected function __construct()
    {
    }
}

class classWithPrivateConstructor
{
    private function __construct()
    {
    }
}

class classWithFinalConstructor
{
    final public function __construct()
    {
    }
}

interface isAnInterface
{
}

abstract class abstractClass
{
}

class classWithConstructor
{
    public $a = null;
    public $b = null;
    public $c = null;
    public $reference = null;
    public $array = null;

    public function __construct($a, $b, $c, & $reference, $array = [])
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
        $this->reference = $reference = uniqid();
        $this->array = $array;
    }
}

class classWithConstructorWithOptionalArguments
{
    public function __construct($a = null, $b = null)
    {
    }
}

class classWithConstructorWithVariadicArgument
{
    public $variadicArguments;

    public function __construct(...$a)
    {
        $this->variadicArguments = $a;
    }
}

class closure extends atoum
{
    public function testClass()
    {
        $this->testedClass->implements(atoum\factory\builder::class);
    }

    public function testBuild()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->build(new \reflectionClass(classWithoutConstructor::class)))->isTestedInstance
                ->object($factory = $this->testedInstance->get())->isInstanceOf(\closure::class)
                ->object($factory())->isInstanceOf(classWithoutConstructor::class)

                ->object($this->testedInstance->build(new \reflectionClass(classWithoutConstructor::class), $instance))->isTestedInstance
                ->object($factory = $this->testedInstance->get())->isInstanceOf(\closure::class)
                ->object($builtInstance = $factory())->isInstanceOf(classWithoutConstructor::class)
                ->object($instance)->isIdenticalTo($builtInstance)

                ->object($this->testedInstance->build(new \reflectionClass(classWithProtectedConstructor::class)))->isTestedInstance
                ->variable($this->testedInstance->get())->isNull

                ->object($this->testedInstance->build(new \reflectionClass(classWithProtectedConstructor::class), $instance))->isTestedInstance
                ->variable($this->testedInstance->get())->isNull

                ->object($this->testedInstance->build(new \reflectionClass(classWithPrivateConstructor::class)))->isTestedInstance
                ->variable($this->testedInstance->get())->isNull

                ->object($this->testedInstance->build(new \reflectionClass(classWithPrivateConstructor::class), $instance))->isTestedInstance
                ->variable($this->testedInstance->get())->isNull

                ->object($this->testedInstance->build(new \reflectionClass(classWithFinalConstructor::class)))->isTestedInstance
                ->object($factory = $this->testedInstance->get())->isInstanceOf(\closure::class)
                ->object($factory())->isInstanceOf(classWithFinalConstructor::class)

                ->object($this->testedInstance->build(new \reflectionClass(classWithFinalConstructor::class), $instance))->isTestedInstance
                ->object($factory = $this->testedInstance->get())->isInstanceOf(\closure::class)
                ->object($builtInstance = $factory())->isInstanceOf(classWithFinalConstructor::class)
                ->object($instance)->isIdenticalTo($builtInstance)

                ->object($this->testedInstance->build(new \reflectionClass(abstractClass::class)))->isTestedInstance
                ->variable($this->testedInstance->get())->isNull

                ->object($this->testedInstance->build(new \reflectionClass(abstractClass::class), $instance))->isTestedInstance
                ->variable($this->testedInstance->get())->isNull

                ->object($this->testedInstance->build(new \reflectionClass(isAnInterface::class)))->isTestedInstance
                ->variable($this->testedInstance->get())->isNull

                ->object($this->testedInstance->build(new \reflectionClass(isAnInterface::class), $instance))->isTestedInstance
                ->variable($this->testedInstance->get())->isNull

                ->object($this->testedInstance->build(new \reflectionClass(classWithConstructor::class)))->isTestedInstance
                ->object($factory = $this->testedInstance->get())->isInstanceOf(\closure::class)
                ->object($builtInstance = $factory('a', 'b', 'c', $reference))->isInstanceOf(classWithConstructor::class)
                ->string($builtInstance->a)->isEqualTo('a')
                ->string($builtInstance->b)->isEqualTo('b')
                ->string($builtInstance->c)->isEqualTo('c')
                ->string($builtInstance->reference)->isNotEmpty
                ->string($builtInstance->reference)->isEqualTo($reference)
                ->array($builtInstance->array)->isEmpty

                ->object($this->testedInstance->build(new \reflectionClass(classWithConstructor::class)))->isTestedInstance
                ->object($factory = $this->testedInstance->get())->isInstanceOf(\closure::class)
                ->object($builtInstance = $factory('a', 'b', 'c', $reference, $array = range(1, 5)))->isInstanceOf(classWithConstructor::class)
                ->string($builtInstance->a)->isEqualTo('a')
                ->string($builtInstance->b)->isEqualTo('b')
                ->string($builtInstance->c)->isEqualTo('c')
                ->string($builtInstance->reference)->isNotEmpty
                ->string($builtInstance->reference)->isEqualTo($reference)
                ->array($builtInstance->array)->isEqualTo($array)

                ->object($this->testedInstance->build(new \reflectionClass(classWithConstructor::class), $instance))->isTestedInstance
                ->object($factory = $this->testedInstance->get())->isInstanceOf(\closure::class)
                ->object($builtInstance = $factory('a', 'b', 'c', $reference))->isInstanceOf(classWithConstructor::class)
                ->object($instance)->isIdenticalTo($builtInstance)
                ->string($instance->a)->isEqualTo('a')
                ->string($instance->b)->isEqualTo('b')
                ->string($instance->c)->isEqualTo('c')
                ->string($instance->reference)->isNotEmpty
                ->string($instance->reference)->isEqualTo($reference)
                ->array($instance->array)->isEmpty
                ->object($builtInstance = $factory('a', 'b', 'c', $reference, $array = range(1, 5)))->isInstanceOf(classWithConstructor::class)
                ->object($instance)->isIdenticalTo($builtInstance)
                ->string($instance->a)->isEqualTo('a')
                ->string($instance->b)->isEqualTo('b')
                ->string($instance->c)->isEqualTo('c')
                ->string($instance->reference)->isNotEmpty
                ->string($instance->reference)->isEqualTo($reference)
                ->array($instance->array)->isEqualTo($array)
        ;
    }

    public function testBuildWithVariadicArguments()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->build(new \reflectionClass(classWithConstructorWithVariadicArgument::class), $instance))->isTestedInstance
                ->object($factory = $this->testedInstance->get())->isInstanceOf(\closure::class)
                ->object($builtInstance = $factory('a', 'b', 'c'))->isInstanceOf(classWithConstructorWithVariadicArgument::class)
                ->array($builtInstance->variadicArguments)->isEqualTo(['a', 'b', 'c'])
        ;
    }

    public function testAddToAssertionManager()
    {
        $this
            ->given(
                $this->newTestedInstance,
                $assertionManager = new \mock\atoum\test\assertion\manager()
            )
            ->then
                ->object($this->testedInstance->addToAssertionManager($assertionManager, $factoryName = uniqid(), $defaultHandler = function () {
                }))->isTestedInstance
                ->mock($assertionManager)
                    ->call('setMethodHandler')->withIdenticalArguments($factoryName, $defaultHandler)->once
                    ->call('setPropertyHandler')->withIdenticalArguments($factoryName, $defaultHandler)->once

            ->if($this->testedInstance->build(new \reflectionClass(classWithConstructor::class)))
            ->then
                ->object($this->testedInstance->addToAssertionManager($assertionManager, $factoryName = uniqid(), $defaultHandler = function () {
                }))->isTestedInstance
                ->mock($assertionManager)
                    ->call('setMethodHandler')->withArguments($factoryName, $this->testedInstance->get())->once
                    ->call('setPropertyHandler')->withArguments($factoryName, $this->testedInstance->get())->never
                    ->call('setPropertyHandler')->withArguments($factoryName, $defaultHandler)->once

            ->if($this->testedInstance->build(new \reflectionClass(classWithConstructorWithOptionalArguments::class)))
            ->then
                ->object($this->testedInstance->addToAssertionManager($assertionManager, $factoryName = uniqid(), function () {
                }))->isTestedInstance
                ->mock($assertionManager)
                    ->call('setMethodHandler')->withArguments($factoryName, $this->testedInstance->get())->once
                    ->call('setPropertyHandler')->withArguments($factoryName, $this->testedInstance->get())->once
        ;
    }
}
