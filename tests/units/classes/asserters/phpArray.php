<?php

namespace mageekguy\atoum\tests\units\asserters;

use mageekguy\atoum;
use mageekguy\atoum\asserter;
use mageekguy\atoum\tools\variable;

require_once __DIR__ . '/../../runner.php';

class phpArray extends atoum\test
{
    public function testClass()
    {
        $this->testedClass
            ->extends(atoum\asserters\variable::class)
            ->implements(\arrayAccess::class)
        ;
    }

    public function test__construct()
    {
        $this
            ->if($this->newTestedInstance())
            ->then
                ->object($this->testedInstance->getGenerator())->isEqualTo(new atoum\asserter\generator())
                ->object($this->testedInstance->getAnalyzer())->isEqualTo(new variable\analyzer())
                ->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
                ->variable($this->testedInstance->getValue())->isNull()
                ->boolean($this->testedInstance->wasSet())->isFalse()
                ->variable($this->testedInstance->getKey())->isNull()
                ->variable($this->testedInstance->getInnerAsserter())->isNull()
                ->variable($this->testedInstance->getInnerValue())->isNull()

            ->if($this->newTestedInstance($generator = new asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
            ->then
                ->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
                ->object($this->testedInstance->getAnalyzer())->isIdenticalTo($analyzer)
                ->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
                ->variable($this->testedInstance->getValue())->isNull()
                ->boolean($this->testedInstance->wasSet())->isFalse()
                ->variable($this->testedInstance->getKey())->isNull()
                ->variable($this->testedInstance->getInnerAsserter())->isNull()
                ->variable($this->testedInstance->getInnerValue())->isNull()
        ;
    }

    public function test__get()
    {
        $this
            ->given($this->newTestedInstance($generator = new \mock\mageekguy\atoum\asserter\generator()))
            ->then
                ->object($this->testedInstance->object)->isTestedInstance
                ->object($this->testedInstance->getInnerAsserter())->isEqualTo($generator->object)
                ->object($this->testedInstance->object->phpString)->isTestedInstance
                ->object($this->testedInstance->getInnerAsserter())->isEqualTo($generator->phpString)
                ->object($this->testedInstance->error)->isInstanceOf($generator->error)
                ->variable($this->testedInstance->getInnerAsserter())->isNull()

            ->if(
                $this->testedInstance->setWith(
                [
                        0 => [
                            0 => [
                                1 => ['foo', 'bar']
                            ],
                            1 => [1, new \mock\phpObject()]
                        ],
                        1 => 'foobar'
                    ]
                )
            )
            ->then
                ->object($this->testedInstance->phpArray[0][0][1]->isEqualTo(['foo', 'bar']))->isTestedInstance
                ->object($this->testedInstance->phpString[1]->isEqualTo('foobar'))->isTestedInstance

            ->given($this->newTestedInstance->setWith([$array1 = ['foo', 'bar'], $array2 = [1, new \mock\phpObject()]]))
            ->then
                ->object($this->testedInstance->phpArray[0]->phpString[0]->isEqualTo('foo'))->isInstanceOf(atoum\asserters\phpArray::class)
                ->object($this->testedInstance->phpArray[1]->isEqualTo($array2))->isInstanceOf(atoum\asserters\phpArray::class)
        ;
    }

    public function testReset()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->reset())->isTestedInstance
                ->variable($this->testedInstance->getValue())->isNull()
                ->boolean($this->testedInstance->wasSet())->isFalse()
                ->boolean($this->testedInstance->isSetByReference())->isFalse()
                ->variable($this->testedInstance->getKey())->isNull()
                ->variable($this->testedInstance->getInnerAsserter())->isNull()
                ->variable($this->testedInstance->getInnerValue())->isNull()

            ->if($this->testedInstance->setWith([]))
            ->then
                ->object($this->testedInstance->reset())->isTestedInstance
                ->variable($this->testedInstance->getValue())->isNull()
                ->boolean($this->testedInstance->wasSet())->isFalse()
                ->boolean($this->testedInstance->isSetByReference())->isFalse()
                ->variable($this->testedInstance->getKey())->isNull()
                ->variable($this->testedInstance->getInnerAsserter())->isNull()
                ->variable($this->testedInstance->getInnerValue())->isNull()

            ->if(
                $reference = range(1, 5),
                $this->testedInstance->setByReferenceWith($reference)
            )
            ->then
                ->object($this->testedInstance->reset())->isTestedInstance
                ->variable($this->testedInstance->getValue())->isNull()
                ->boolean($this->testedInstance->wasSet())->isFalse()
                ->boolean($this->testedInstance->isSetByReference())->isFalse()
                ->variable($this->testedInstance->getKey())->isNull()
                ->variable($this->testedInstance->getInnerAsserter())->isNull()
                ->variable($this->testedInstance->getInnerValue())->isNull()

            ->if($this->testedInstance->object)
            ->then
                ->object($this->testedInstance->reset())->isTestedInstance
                ->variable($this->testedInstance->getValue())->isNull()
                ->boolean($this->testedInstance->wasSet())->isFalse()
                ->boolean($this->testedInstance->isSetByReference())->isFalse()
                ->variable($this->testedInstance->getKey())->isNull()
                ->variable($this->testedInstance->getInnerAsserter())->isNull()
                ->variable($this->testedInstance->getInnerValue())->isNull()

            ->if(
                $this->testedInstance->setWith(range(1, 5)),
                $this->testedInstance->atKey(2)
            )
            ->then
                ->object($this->testedInstance->reset())->isTestedInstance
                ->variable($this->testedInstance->getValue())->isNull()
                ->boolean($this->testedInstance->wasSet())->isFalse()
                ->boolean($this->testedInstance->isSetByReference())->isFalse()
                ->variable($this->testedInstance->getKey())->isNull()
                ->variable($this->testedInstance->getInnerAsserter())->isNull()
                ->variable($this->testedInstance->getInnerValue())->isNull()
        ;
    }

    public function testSetWith()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )

            ->if(
                $this->calling($locale)->_ = $notAnArray = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, & $value) {
                    $asserter->setWith($value = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notAnArray)
                ->mock($locale)->call('_')->withArguments('%s is not an array', $type)->once

                ->object($asserter->setWith($value = []))->isIdenticalTo($asserter)
                ->array($asserter->getValue())->isEqualTo($value)
                ->variable($asserter->getKey())->isNull()
                ->variable($asserter->getInnerAsserter())->isNull()
                ->variable($asserter->getInnerValue())->isNull()
                ->boolean($asserter->isSetByReference())->isFalse()

            ->if($asserter->object)
            ->then
                ->variable($innerAsserter = $asserter->getInnerAsserter())->isNotNull()
                ->object($objectAsserter = $asserter->setWith($object = new \mock\phpObject()))->isIdenticalTo($innerAsserter)
                ->object($objectAsserter->getValue())->isIdenticalTo($object)
                ->variable($asserter->getValue())->isNull()
                ->boolean($asserter->wasSet())->isFalse()
                ->boolean($asserter->isSetByReference())->isFalse()
                ->variable($asserter->getKey())->isNull()
                ->variable($asserter->getInnerAsserter())->isNull()
                ->variable($asserter->getInnerValue())->isNull()
        ;
    }

    public function testOffsetGet()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter[2];
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if(
                $this->calling($locale)->_ = $notAnArray = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid(),
                $asserter->setWith([1, 2, $object = new \mock\phpObject(), clone $object])
            )
            ->then
                ->exception(function () use ($asserter, & $value) {
                    $asserter[2];
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notAnArray)
                ->mock($locale)->call('_')->withArguments('Value %s at key %s is not an array', $type, 2)->once

            ->if($asserter->setWith([1, 2, $object = new \mock\phpObject(), clone $object]))
            ->then
                ->object($asserter->object[2]->isIdenticalTo($object))->isIdenticalTo($asserter)
                ->object($asserter->object[2]->isIdenticalTo($object))->isIdenticalTo($asserter)
                ->object($asserter->object[3]->isCloneOf($object))->isIdenticalTo($asserter)
                ->object($asserter->object[2])->isIdenticalTo($asserter)

            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale)
                    ->setAnalyzer($analyzer)
            )

            ->if($asserter->setWith($array = [$integer = rand(1, PHP_INT_MAX), 2, $innerArray = [3, 4, 5, $object]]))
            ->then
                ->object($asserter[2])
                    ->isIdenticalTo($asserter)
                    ->array($asserter->getValue())->isEqualTo($innerArray)

            ->if($asserter->setWith($array))
            ->then
                ->object($asserter->integer[0]->isEqualTo($integer))->isIdenticalTo($asserter)
                ->object($asserter->object[2][3]->isIdenticalTo($object))->isIdenticalTo($asserter)
                ->object($asserter->object[2][3]->isIdenticalTo($object)->integer[0]->isEqualTo($integer))->isIdenticalTo($asserter)
                ->object($asserter->object[2][3]->isIdenticalTo($object)->integer($integer)->isEqualTo($integer))
                    ->isNotIdenticalTo($asserter)
                    ->isInstanceOf(atoum\asserters\integer::class)

            ->if(
                $this->calling($locale)->_ = $unknownKey = uniqid(),
                $this->calling($analyzer)->getTypeOf = function ($value) use ($innerArray, & $innerArrayType, & $keyType) {
                    return ($innerArray === $value ? ($innerArrayType = uniqid()) : ($keyType = uniqid()));
                }
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->object[2][4];
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($unknownKey)
                ->mock($locale)->call('_')->withArguments('%s has no key %s', $innerArrayType, $keyType)->once
        ;
    }

    public function testOffsetSet()
    {
        $this
            ->given($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    $asserter[rand(0, PHP_INT_MAX)] = rand(0, PHP_INT_MAX);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Tested array is read only')
        ;
    }

    public function testOffsetUnset()
    {
        $this
            ->given($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    unset($asserter[rand(0, PHP_INT_MAX)]);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is read only')
        ;
    }

    public function testOffsetExists()
    {
        $this
            ->given($asserter = $this->newTestedInstance)
            ->then
                ->boolean(isset($asserter[rand(0, PHP_INT_MAX)]))->isFalse()

            ->if($asserter->setWith([]))
            ->then
                ->boolean(isset($asserter[rand(0, PHP_INT_MAX)]))->isFalse()

            ->if($asserter->setWith([uniqid()]))
            ->then
                ->boolean(isset($asserter[0]))->isTrue()
                ->boolean(isset($asserter[rand(1, PHP_INT_MAX)]))->isFalse()

            ->if($asserter->setWith([$key = uniqid() => uniqid()]))
            ->then
                ->boolean(isset($asserter[$key]))->isTrue()
                ->boolean(isset($asserter[0]))->isFalse()
                ->boolean(isset($asserter[rand(1, PHP_INT_MAX)]))->isFalse()
        ;
    }

    public function testHasSize()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasSize(rand(0, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if(
                $this->calling($locale)->_ = $badSize = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid(),
                $asserter->setWith([])
            )
            ->then
                ->exception(function () use ($asserter, & $size) {
                    $asserter->hasSize($size = rand(1, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($badSize)
                ->mock($locale)->call('_')->withArguments('%s has size %d, expected size %d', $asserter, 0, $size)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->hasSize(rand(1, PHP_INT_MAX), $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->object($asserter->hasSize(0))->isIdenticalTo($asserter)

            ->if($asserter->setWith(range(1, 5)))
            ->then
                ->object($asserter->hasSize(5))->isIdenticalTo($asserter)
        ;
    }

    public function testHasSizeOnInnerAsserter()
    {
        $this
            ->given($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isEmpty();
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')
            ->if($asserter->setWith([range(1, 5)]))
            ->then
                ->object($childAsserter = $asserter->child[0](function ($child) {
                    $child->hasSize(5);
                }))->isInstanceOf(atoum\asserters\phpArray\child::class)
                ->object($childAsserter->hasSize(1))->isIdenticalTo($asserter)

            ->given($asserter = $this->newTestedInstance)
            ->if($asserter->setWith([[range(1, 5), range(1, 3)]]))
            ->then
                ->object($childAsserter = $asserter->child[0][1](function ($child) {
                    $child->hasSize(3);
                }))->isInstanceOf(atoum\asserters\phpArray\child::class)
                ->object($childAsserter->hasSize(1))->isIdenticalTo($asserter)
        ;
    }

    public function testIsEmpty()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isEmpty();
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if(
                $this->calling($locale)->_ = $notEmpty = uniqid(),
                $asserter->setWith([uniqid()])
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isEmpty();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notEmpty)
                ->mock($locale)->call('_')->withArguments('%s is not empty', $asserter)->once

                ->exception(function () use ($asserter) {
                    $asserter->isEmpty;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notEmpty)
                ->mock($locale)->call('_')->withArguments('%s is not empty', $asserter)->twice

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->isEmpty($failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->if($asserter->setWith([]))
            ->then
                ->object($asserter->isEmpty())->isIdenticalTo($asserter)
        ;
    }

    public function testIsNotEmpty()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isNotEmpty();
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if(
                $this->calling($locale)->_ = $isEmpty = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid(),
                $asserter->setWith([])
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isNotEmpty();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($isEmpty)
                ->mock($locale)->call('_')->withArguments('%s is empty', $asserter)->once

                ->exception(function () use ($asserter) {
                    $asserter->isNotEmpty;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($isEmpty)
                ->mock($locale)->call('_')->withArguments('%s is empty', $asserter)->twice

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->isNotEmpty($failMessage = uniqid());
                })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($failMessage)

                ->if($asserter->setWith([uniqid()]))
                ->then
                    ->object($asserter->isNotEmpty())->isIdenticalTo($asserter)
        ;
    }

    public function testAtKey()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->atKey(uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if($asserter->setWith([uniqid(), uniqid(), $data = rand(1, PHP_INT_MAX), uniqid(), uniqid()]))
                ->object($asserter->atKey(0))->isIdenticalTo($asserter)
                ->object($asserter->atKey('0'))->isIdenticalTo($asserter)
                ->object($asserter->atKey(1))->isIdenticalTo($asserter)
                ->object($asserter->atKey(2))->isIdenticalTo($asserter)
                ->object($asserter->atKey(3))->isIdenticalTo($asserter)
                ->object($asserter->atKey(4))->isIdenticalTo($asserter)

            ->if(
                $this->calling($locale)->_ = $unknownKey = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, & $key) {
                    $asserter->atKey($key = rand(5, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($unknownKey)
                ->mock($locale)->call('_')->withArguments('%s has no key %s', $asserter, $type)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments($key)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->atKey(rand(5, PHP_INT_MAX), $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)
        ;
    }

    public function testContains()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->contains(uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if(
                $this->calling($locale)->_ = $notInArray = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid(),
                $asserter->setWith([uniqid(), uniqid(), $data = rand(1, PHP_INT_MAX), uniqid(), uniqid()])
            )
            ->then
                ->exception(function () use ($asserter, & $unknownValue) {
                    $asserter->contains($unknownValue = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notInArray)
                ->mock($locale)->call('_')->withArguments('%s does not contain %s', $asserter, $type)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments($unknownValue)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->contains(uniqid(), $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->object($asserter->contains($data))->isIdenticalTo($asserter)
                ->object($asserter->contains((string) $data))->isIdenticalTo($asserter)

            ->if(
                $this->calling($locale)->_ = $notInArrayAtKey = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid(),
                $asserter->setWith([uniqid(), uniqid(), $data = rand(1, PHP_INT_MAX), uniqid(), uniqid()])
            )
            ->then
                ->exception(function () use ($asserter, $data) {
                    $asserter->atKey(0)->contains($data);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notInArrayAtKey)

                ->object($asserter->contains($data))->isIdenticalTo($asserter)
                ->object($asserter->atKey(2)->contains($data))->isIdenticalTo($asserter)

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->atKey(0)->contains(uniqid(), $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)
        ;
    }

    public function testStrictlyContains()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->strictlyContains(uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if(
                $this->calling($locale)->_ = $notInArray = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid(),
                $asserter->setWith([1, 2, 3, 4, 5, '3'])
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->strictlyContains('1');
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notInArray)
                ->mock($locale)->call('_')->withArguments('%s does not strictly contain %s', $asserter, $type)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments('1')->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->strictlyContains('1', $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->object($asserter->strictlyContains(1))->isIdenticalTo($asserter)

            ->if($this->calling($analyzer)->getTypeOf = function ($value) use (& $notInArrayType, & $keyType) {
                return ($value === 2 ? ($notInArrayType = uniqid()) : ($keyType = uniqid()));
            })
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->atKey(0)->strictlyContains(2);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notInArray)
                ->mock($locale)->call('_')->withArguments('%s does not strictly contain %s at key %s', $asserter, $notInArrayType, $keyType)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments(2)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->atKey(0)->strictlyContains(2, $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)
        ;
    }

    public function testNotContains()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->notContains(uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if($asserter->setWith([uniqid(), uniqid(), $isInArray = uniqid(), uniqid(), uniqid()]))
            ->then
                ->object($asserter->notContains(uniqid()))->isIdenticalTo($asserter)

            ->if(
                $this->calling($locale)->_ = $inArray = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, $isInArray) {
                    $asserter->notContains($isInArray);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($inArray)
                ->mock($locale)->call('_')->withArguments('%s contains %s', $asserter, $type)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments($isInArray)->once

                ->exception(function () use ($asserter, $isInArray, & $failMessage) {
                    $asserter->notContains($isInArray, $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->object($asserter->atKey(0)->notContains($inArray))->isIdenticalTo($asserter)
                ->object($asserter->atKey(1)->notContains($inArray))->isIdenticalTo($asserter)
                ->object($asserter->atKey(3)->notContains($inArray))->isIdenticalTo($asserter)
                ->object($asserter->atKey(4)->notContains($inArray))->isIdenticalTo($asserter)

            ->if(
                $this->calling($locale)->_ = $inArray = uniqid(),
                $this->calling($analyzer)->getTypeOf = function ($value) use ($isInArray, & $isInArrayType, & $keyType) {
                    return ($isInArray === $value ? ($isInArrayType = uniqid()) : ($keyType = uniqid()));
                }
            )
            ->then
                ->exception(function () use ($asserter, $isInArray) {
                    $asserter->atKey(2)->notContains($isInArray);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($inArray)
                ->mock($locale)->call('_')->withArguments('%s contains %s at key %s', $asserter, $isInArrayType, $keyType)->once
                ->mock($analyzer)
                    ->call('getTypeOf')
                        ->withArguments($isInArray)->twice
                        ->withArguments(2)->once

                ->exception(function () use ($asserter, $isInArray, & $failMessage) {
                    $asserter->atKey(2)->notContains($isInArray, $failMessage = 'FAIL');
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)
        ;
    }

    public function testStrictlyNotContains()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->strictlyNotContains(uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if(
                $this->calling($locale)->_ = $strictlyNotInArray = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid(),
                $asserter->setWith([1, 2, 3, 4, 5, '6'])
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->strictlyNotContains(1);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($strictlyNotInArray)
                ->mock($locale)->call('_')->withArguments('%s strictly contains %s', $asserter, $type)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments(1)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->strictlyNotContains(1, $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->object($asserter->strictlyNotContains('1'))->isIdenticalTo($asserter)
                ->object($asserter->atKey(1)->strictlyNotContains(1))->isIdenticalTo($asserter)
                ->object($asserter->atKey(2)->strictlyNotContains(1))->isIdenticalTo($asserter)
                ->object($asserter->atKey(3)->strictlyNotContains(1))->isIdenticalTo($asserter)
                ->object($asserter->atKey(4)->strictlyNotContains(1))->isIdenticalTo($asserter)

            ->if($this->calling($analyzer)->getTypeOf = function ($value) use (& $strictlyNotInArrayType, & $keyType) {
                return ($value === 1 ? ($strictlyNotInArrayType = uniqid()) : ($keyType = uniqid()));
            })
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->atKey(0)->strictlyNotContains(1);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($strictlyNotInArray)
                ->mock($locale)->call('_')->withArguments('%s strictly contains %s at key %s', $asserter, $strictlyNotInArrayType, $keyType)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments(0)->once
        ;
    }

    public function testContainsValues()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->containsValues([6]);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if(
                $this->calling($locale)->_ = $notContainsValues = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid(),
                $asserter->setWith([1, 2, 3, 4, 5])
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->containsValues([6]);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notContainsValues)
                ->mock($locale)->call('_')->withArguments('%s does not contain values %s', $asserter, $type)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments([6])->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->containsValues([6], $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->object($asserter->containsValues([1]))->isIdenticalTo($asserter)
                ->object($asserter->containsValues(['1']))->isIdenticalTo($asserter)
                ->object($asserter->containsValues([1, 2, 4]))->isIdenticalTo($asserter)
                ->object($asserter->containsValues(['1', 2, '4']))->isIdenticalTo($asserter)
        ;
    }

    public function testStrictlyContainsValues()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->strictlyContainsValues([6]);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if(
                $this->calling($locale)->_ = $strictlyNotContainsValues = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid(),
                $asserter->setWith([1, 2, 3, 4, 5])
            )
                ->then
                    ->exception(function () use ($asserter) {
                        $asserter->strictlyContainsValues([1, '5']);
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($strictlyNotContainsValues)
                ->mock($locale)->call('_')->withArguments('%s does not contain strictly values %s', $asserter, $type)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments(['5'])->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->strictlyContainsValues(['5'], $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->object($asserter->strictlyContainsValues([1]))->isIdenticalTo($asserter)
                ->object($asserter->strictlyContainsValues([1, 2]))->isIdenticalTo($asserter)
                ->object($asserter->strictlyContainsValues([1, 2, 3]))->isIdenticalTo($asserter)
                ->object($asserter->strictlyContainsValues([1, 2, 3, 4]))->isIdenticalTo($asserter)
                ->object($asserter->strictlyContainsValues([1, 2, 3, 4, 5]))->isIdenticalTo($asserter)
        ;
    }

    public function testNotContainsValues()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->notContainsValues([1, 6]);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if(
                $this->calling($locale)->_ = $containsValues = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid(),
                $asserter->setWith([1, 2, 3, 4, 5])
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->notContainsValues([1, 6]);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($containsValues)
                ->mock($locale)->call('_')->withArguments('%s contains values %s', $asserter, $type)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments([1])->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->notContainsValues([1, 6], $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->object($asserter->notContainsValues([6]))->isIdenticalTo($asserter)
                ->object($asserter->notContainsValues(['6']))->isIdenticalTo($asserter)
                ->object($asserter->notContainsValues([6, 7]))->isIdenticalTo($asserter)
                ->object($asserter->notContainsValues(['6', '7']))->isIdenticalTo($asserter)
                ->object($asserter->notContainsValues([6, 7, 8]))->isIdenticalTo($asserter)
                ->object($asserter->notContainsValues(['6', 7, '8']))->isIdenticalTo($asserter)
        ;
    }

    public function testStrictlyNotContainsValues()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->strictlyNotContainsValues([1, 6]);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if(
                $this->calling($locale)->_ = $containsStrictlyValues = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid(),
                $asserter->setWith([1, 2, 3, 4, 5])
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->strictlyNotContainsValues([1, '2', '4']);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($containsStrictlyValues)
                ->mock($locale)->call('_')->withArguments('%s contains strictly values %s', $asserter, $type)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments([1])->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->strictlyNotContainsValues([1], $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->object($asserter->strictlyNotContainsValues(['1']))->isIdenticalTo($asserter)
                ->object($asserter->strictlyNotContainsValues(['1', '2']))->isIdenticalTo($asserter)
                ->object($asserter->strictlyNotContainsValues(['1', '2', '3']))->isIdenticalTo($asserter)
                ->object($asserter->strictlyNotContainsValues(['1', '2', '3', '4']))->isIdenticalTo($asserter)
                ->object($asserter->strictlyNotContainsValues(['1', '2', '3', '4', '5']))->isIdenticalTo($asserter)
                ->object($asserter->strictlyNotContainsValues(['6', '7']))->isIdenticalTo($asserter)
        ;
    }

    public function testHasKey()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasKey(rand(0, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if(
                $this->calling($locale)->_ = $notHasKey = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid(),
                $asserter->setWith([])
            )
            ->then
                ->exception(function () use ($asserter, & $key) {
                    $asserter->hasKey($key = rand(1, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notHasKey)
                ->mock($locale)->call('_')->withArguments('%s has no key %s', $asserter, $type)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments($key)->once

                ->exception(function () use ($asserter, & $key, & $failMessage) {
                    $asserter->hasKey($key = rand(1, PHP_INT_MAX), $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->if($asserter->setWith([uniqid(), uniqid(), uniqid(), uniqid(), uniqid(), '5' => uniqid()]))
            ->then
                ->object($asserter->hasKey(0))->isIdenticalTo($asserter)
                ->object($asserter->hasKey(1))->isIdenticalTo($asserter)
                ->object($asserter->hasKey(2))->isIdenticalTo($asserter)
                ->object($asserter->hasKey(3))->isIdenticalTo($asserter)
                ->object($asserter->hasKey(4))->isIdenticalTo($asserter)
                ->object($asserter->hasKey(5))->isIdenticalTo($asserter)
                ->object($asserter->hasKey('5'))->isIdenticalTo($asserter)
        ;
    }

    public function testNotHasKey()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->notHasKey(rand(0, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if($asserter->setWith([]))
            ->then
                ->object($asserter->notHasKey(rand(-PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($asserter)
                ->object($asserter->notHasKey(uniqid()))->isIdenticalTo($asserter)

            ->if(
                $this->calling($locale)->_ = $hasKey = uniqid(),
                $this->calling($analyzer)->getTypeOf = $keyType = uniqid(),
                $asserter->setWith([uniqid(), uniqid(), uniqid(), uniqid(), uniqid()])
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->notHasKey(0);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($hasKey)
                ->mock($locale)->call('_')->withArguments('%s has key %s', $asserter, $keyType)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments(0)->once

                ->exception(function () use ($asserter) {
                    $asserter->notHasKey('0');
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($hasKey)
                ->mock($locale)->call('_')->withArguments('%s has key %s', $asserter, $keyType)->twice
                ->mock($analyzer)->call('getTypeOf')->withIdenticalArguments('0')->once
        ;
    }

    public function testNotHasKeys()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->notHasKeys([rand(0, PHP_INT_MAX)]);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if($asserter->setWith([]))
            ->then
                ->object($asserter->notHasKeys(range(1, 5)))->isIdenticalTo($asserter)
                ->object($asserter->notHasKeys([uniqid(), uniqid()]))->isIdenticalTo($asserter)

            ->if(
                $this->calling($locale)->_ = $hasKeys = uniqid(),
                $this->calling($analyzer)->getTypeOf = $keysType = uniqid(),
                $asserter->setWith([uniqid(), uniqid(), uniqid(), uniqid(), uniqid()])
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->notHasKeys([0, 'premier', '2']);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($hasKeys)
                ->mock($locale)->call('_')->withArguments('%s has keys %s', $asserter, $keysType)->once
                ->mock($analyzer)->call('getTypeOf')->withIdenticalArguments([0 => 0, 2 => '2'])->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->notHasKeys([0, 'premier', 2], $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->object($asserter->notHasKeys([5, '6']))->isIdenticalTo($asserter)
        ;
    }

    public function testHasKeys()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasKeys([rand(0, PHP_INT_MAX)]);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if(
                $this->calling($locale)->_ = $notHasKeys = uniqid(),
                $this->calling($analyzer)->getTypeOf = $keysType = uniqid(),
                $asserter->setWith([])
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasKeys([0, 1, 2]);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notHasKeys)
                ->mock($locale)->call('_')->withArguments('%s has no keys %s', $asserter, $keysType)->once
                ->mock($analyzer)->call('getTypeOf')->withIdenticalArguments([0, 1, 2])->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->hasKeys([0], $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->if($asserter->setWith([uniqid(), uniqid(), uniqid(), uniqid(), uniqid()]))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasKeys([0, 'first', 2, 'second']);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notHasKeys)
                ->mock($locale)->call('_')->withArguments('%s has no keys %s', $asserter, $keysType)->twice
                ->mock($analyzer)->call('getTypeOf')->withIdenticalArguments([1 => 'first', 3 => 'second'])->once

                ->object($asserter->hasKeys([0, 2, 4]))->isIdenticalTo($asserter)
        ;
    }

    public function testKeys()
    {
        $this
            ->given($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->keys;
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if($asserter->setWith([]))
            ->then
                ->object($array = $asserter->keys)->isInstanceOf(atoum\asserters\phpArray::class)
                ->array($array->getValue())->isEqualTo([])

            ->if($asserter->setWith([$key1 = uniqid() => uniqid(), $key2 = uniqid() => uniqid()]))
            ->then
                ->object($array = $asserter->keys)->isInstanceOf(atoum\asserters\phpArray::class)
                ->array($array->getValue())->isEqualTo([$key1, $key2])
        ;
    }

    public function testValues()
    {
        $this
            ->given($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->values;
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if($asserter->setWith([]))
            ->then
                ->object($array = $asserter->values)->isInstanceOf(atoum\asserters\phpArray::class)
                ->array($array->getValue())->isEqualTo([])

            ->if($asserter->setWith(['one' => 'first value', 'two' => 'second value']))
            ->then
                ->object($array = $asserter->values)->isInstanceOf(atoum\asserters\phpArray::class)
                ->array($array->getValue())->isEqualTo(['first value', 'second value'])
        ;
    }

    public function testSize()
    {
        $this
            ->given($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->size;
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if($asserter->setWith([]))
            ->then
                ->object($integer = $asserter->size)->isInstanceOf(atoum\asserters\integer::class)
                ->integer($integer->getValue())->isEqualTo(0)

            ->if($asserter->setWith([uniqid(), uniqid()]))
            ->then
                ->object($integer = $asserter->size)->isInstanceOf(atoum\asserters\integer::class)
                ->integer($integer->getValue())->isEqualTo(2)
        ;
    }

    public function testIsEqualTo()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setDiff($diff = new \mock\atoum\tools\diffs\variable())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
                    ->setGenerator($generator = new \mock\atoum\asserter\generator())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isEqualTo([]);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if($asserter->setWith([]))
            ->then
                ->object($asserter->isEqualTo([]))->isIdenticalTo($asserter)

            ->if($asserter->setWith($array = range(1, 5)))
            ->then
                ->object($asserter->isEqualTo($array))->isIdenticalTo($asserter)

            ->if(
                $this->calling($locale)->_ = $localizedMessage = uniqid(),
                $this->calling($diff)->__toString = $diffValue = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, & $notEqualValue) {
                    $asserter->isEqualTo($notEqualValue = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($localizedMessage . PHP_EOL . $diffValue)
                ->mock($locale)->call('_')->withArguments('%s is not equal to %s', $asserter, $type)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments($notEqualValue)->once

                ->object($asserter->isEqualTo($array))->isIdenticalTo($asserter)

            ->given(
                $this->calling($generator)->__get = $integerAsserter = new \mock\atoum\asserters\integer(),
                $this->calling($integerAsserter)->isEqualTo->throw = $exception = new \exception(uniqid())
            )

            ->if($asserter->integer)
            ->then
                ->exception(function () use ($asserter, & $notEqualValue) {
                    $asserter->isEqualTo($notEqualValue = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($localizedMessage . PHP_EOL . $diffValue)
                ->mock($locale)->call('_')->withArguments('%s is not equal to %s', $asserter, $type)->twice
                ->mock($analyzer)->call('getTypeOf')->withArguments($notEqualValue)->once
                ->mock($integerAsserter)->call('isEqualTo')->never

            ->if($asserter->integer[2])
            ->then
                ->exception(function () use ($asserter, & $expectedValue) {
                    $asserter->isEqualTo($expectedValue = rand(4, PHP_INT_MAX));
                })
                    ->isIdenticalTo($exception)
                ->mock($integerAsserter)->call('isEqualTo')->withArguments($expectedValue)->once

            ->if($this->method($integerAsserter)->isEqualTo->isFluent())
            ->then
                ->object($asserter->isEqualTo(3))->isIdenticalTo($asserter)
                ->mock($integerAsserter)->call('isEqualTo')->withArguments(3)->once
        ;
    }

    public function testIsNotEqualTo()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
                    ->setGenerator($generator = new \mock\atoum\asserter\generator())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isNotEqualTo([]);
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if($asserter->setWith([]))
            ->then
                ->object($asserter->isNotEqualTo(range(1, 2)))->isIdenticalTo($asserter)

            ->if(
                $this->calling($locale)->_ = $localizedMessage = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid()
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isNotEqualTo([]);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($localizedMessage)
                ->mock($locale)->call('_')->withArguments('%s is equal to %s', $asserter, $type)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments([])->once

            ->if($asserter->setWith($array = range(1, 5)))
            ->then
                ->object($asserter->isNotEqualTo([]))->isIdenticalTo($asserter)

                ->exception(function () use ($asserter, $array) {
                    $asserter->isNotEqualTo($array);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($localizedMessage)
                ->mock($locale)->call('_')->withArguments('%s is equal to %s', $asserter, $type)->twice
                ->mock($analyzer)->call('getTypeOf')->withArguments($array)->once

            ->given(
                $this->calling($generator)->__get = $integerAsserter = new \mock\atoum\asserters\integer(),
                $this->calling($integerAsserter)->isNotEqualTo->throw = $exception = new \exception(uniqid())
            )

            ->if($asserter->integer)
            ->then
                ->object($asserter->isNotEqualTo([]))->isIdenticalTo($asserter)

                ->exception(function () use ($asserter, $array) {
                    $asserter->isNotEqualTo($array);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($localizedMessage)
                ->mock($locale)->call('_')->withArguments('%s is equal to %s', $asserter, $type)->thrice
                ->mock($analyzer)->call('getTypeOf')->withArguments($array)->twice
                ->mock($integerAsserter)->call('isNotIdenticalTo')->never

            ->if($asserter->integer[2])
            ->then
                ->exception(function () use ($asserter, & $expectedValue) {
                    $asserter->isNotEqualTo($expectedValue = rand(4, PHP_INT_MAX));
                })
                    ->isIdenticalTo($exception)
                ->mock($integerAsserter)->call('isNotEqualTo')->withArguments($expectedValue)->once

            ->if($this->method($integerAsserter)->isNotEqualTo->isFluent())
            ->then
                ->object($asserter->isNotEqualTo(3))->isIdenticalTo($asserter)
                ->mock($integerAsserter)->call('isNotEqualTo')->withArguments(3)->once
        ;
    }

    public function testIsIdenticalTo()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setDiff($diff = new \mock\atoum\tools\diffs\variable())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
                    ->setGenerator($generator = new \mock\atoum\asserter\generator())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isIdenticalTo(new \mock\phpObject());
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if($asserter->setWith([$object = new \mock\phpObject(), 2]))
            ->then
                ->object($asserter->isIdenticalTo([$object, 2]))->isIdenticalTo($asserter)

            ->if(
                $this->calling($locale)->_ = $localizedMessage = uniqid(),
                $this->calling($diff)->__toString = $diffValue = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, & $notIdenticalValue) {
                    $asserter->isIdenticalTo($notIdenticalValue = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($localizedMessage . PHP_EOL . $diffValue)
                ->mock($locale)->call('_')->withArguments('%s is not identical to %s', $asserter, $type)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments($notIdenticalValue)->once

            ->given(
                $this->calling($generator)->__get = $integerAsserter = new \mock\atoum\asserters\integer(),
                $this->calling($integerAsserter)->isIdenticalTo->throw = $exception = new \exception(uniqid())
            )

            ->if($asserter->integer)
            ->then
                ->object($asserter->isIdenticalTo([$object, 2]))->isIdenticalTo($asserter)

                ->exception(function () use ($asserter, & $notIdenticalValue) {
                    $asserter->isIdenticalTo($notIdenticalValue = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($localizedMessage . PHP_EOL . $diffValue)
                ->mock($locale)->call('_')->withArguments('%s is not identical to %s', $asserter, $type)->twice
                ->mock($analyzer)->call('getTypeOf')->withArguments($notIdenticalValue)->once

            ->if($asserter->integer[1])
            ->then
                ->exception(function () use ($asserter, & $expectedValue) {
                    $asserter->isIdenticalTo($expectedValue = rand(4, PHP_INT_MAX));
                })
                    ->isIdenticalTo($exception)
                ->mock($integerAsserter)->call('isIdenticalTo')->withArguments($expectedValue)->once

            ->if($this->method($integerAsserter)->isIdenticalTo->isFluent())
            ->then
                ->object($asserter->isEqualTo(2))->isIdenticalTo($asserter)
                ->mock($integerAsserter)->call('isIdenticalTo')->withArguments($expectedValue)->once
        ;
    }

    public function testIsNotIdenticalTo()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
                    ->setGenerator($generator = new \mock\atoum\asserter\generator())
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isNotIdenticalTo(new \mock\phpObject());
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Array is undefined')

            ->if($asserter->setWith($array = [1, 2]))
            ->then
                ->object($asserter->isNotIdenticalTo(['1', 2]))->isIdenticalTo($asserter)

            ->if(
                $this->calling($locale)->_ = $localizedMessage = uniqid(),
                $this->calling($analyzer)->getTypeOf = $type = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, $array) {
                    $asserter->isNotIdenticalTo($array);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($localizedMessage)
                ->mock($locale)->call('_')->withArguments('%s is identical to %s', $asserter, $type)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments($array)->once

            ->given(
                $this->calling($generator)->__get = $integerAsserter = new \mock\atoum\asserters\integer(),
                $this->calling($integerAsserter)->isNotIdenticalTo->throw = $exception = new \exception(uniqid())
            )

            ->if($asserter->integer)
            ->then
                ->object($asserter->isNotIdenticalTo(['1', 2]))->isIdenticalTo($asserter)
                ->mock($integerAsserter)->call('isNotIdenticalTo')->never

            ->if($asserter->integer[1])
            ->then
                ->exception(function () use ($asserter, & $expectedValue) {
                    $asserter->isNotIdenticalTo($expectedValue = rand(4, PHP_INT_MAX));
                })
                    ->isIdenticalTo($exception)
                ->mock($integerAsserter)->call('isNotIdenticalTo')->withArguments($expectedValue)->once

            ->if($this->method($integerAsserter)->isNotIdenticalTo->isFluent())
            ->then
                ->object($asserter->isNotIdenticalTo(3))->isIdenticalTo($asserter)
                ->mock($integerAsserter)->call('isNotIdenticalTo')->withArguments(3)->once
        ;
    }
}
