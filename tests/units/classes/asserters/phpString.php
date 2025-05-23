<?php

namespace atoum\atoum\tests\units\asserters;

use atoum\atoum;
use atoum\atoum\asserter;
use atoum\atoum\tools\variable;

require_once __DIR__ . '/../../runner.php';

class phpString extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\asserters\variable::class);
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
                ->variable($this->testedInstance->getCharlist())->isNull()
                ->boolean($this->testedInstance->wasSet())->isFalse()

            ->if($this->newTestedInstance($generator = new asserter\generator(), $analyzer = new variable\analyzer(), $locale = new atoum\locale()))
            ->then
                ->object($this->testedInstance->getGenerator())->isIdenticalTo($generator)
                ->object($this->testedInstance->getAnalyzer())->isIdenticalTo($analyzer)
                ->object($this->testedInstance->getLocale())->isIdenticalTo($locale)
                ->variable($this->testedInstance->getValue())->isNull()
                ->variable($this->testedInstance->getCharlist())->isNull()
                ->boolean($this->testedInstance->wasSet())->isFalse()
        ;
    }

    public function test__toString()
    {
        $this
            ->given($this->newTestedInstance->setLocale($locale = new \mock\atoum\atoum\locale()))
            ->if(
                $this->testedInstance->setWith($value = uniqid()),
                $this->calling($locale)->_ = $string = uniqid()
            )
            ->then
                ->castToString($this->testedInstance)->isEqualTo($string)
                ->mock($locale)->call('_')->withArguments('string(%s) \'%s\'', strlen($value), addcslashes($value, ''))->once

            ->if($this->testedInstance->setWith($value = "\010" . uniqid() . "\010", $charlist = "\010"))
            ->then
                ->castToString($this->testedInstance)->isEqualTo($string)
                ->mock($locale)->call('_')->withArguments('string(%s) \'%s\'', strlen($value), addcslashes($value, "\010"))->once
        ;
    }

    public function testSetWith()
    {
        $this
            ->given(
                $asserter = $this->newTestedInstance
                    ->setLocale($locale = new \mock\atoum\atoum\locale())
                    ->setAnalyzer($analyzer = new \mock\atoum\atoum\tools\variable\analyzer())
            )
            ->if(
                $this->calling($locale)->_ = $notString = uniqid(),
                $this->calling($analyzer)->isString = false,
                $this->calling($analyzer)->getTypeOf = $type = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, & $value) {
                    $asserter->setWith($value = rand(- PHP_INT_MAX, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notString)
                ->mock($locale)->call('_')->withArguments('%s is not a string', $type)->once
                ->mock($analyzer)->call('getTypeOf')->withArguments($value)->once
                ->integer($asserter->getValue())->isEqualTo($value)
                ->variable($asserter->getCharlist())->isNull()

            ->if(
                $this->calling($analyzer)->isString = true
            )
            ->then
                ->object($asserter->setWith($value = uniqid()))->isIdenticalTo($asserter)
                ->string($asserter->getValue())->isEqualTo($value)
                ->variable($asserter->getCharlist())->isNull()

                ->object($asserter->setWith($value = uniqid(), $charlist = "\010"))->isIdenticalTo($asserter)
                ->string($asserter->getValue())->isEqualTo($value)
                ->string($asserter->getCharlist())->isEqualTo($charlist)
        ;
    }

    public function testIsEqualTo()
    {
        $this
            ->given($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isEqualTo(uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is undefined')

            ->if(
                $asserter
                    ->setWith($firstString = uniqid())
                    ->setLocale($locale = new \mock\atoum\atoum\locale())
                    ->setDiff($diff = new \mock\atoum\atoum\tools\diffs\variable()),
                $this->calling($locale)->_ = $notEqual = uniqid(),
                $this->calling($diff)->__toString = $diffValue = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, & $secondString) {
                    $asserter->isEqualTo($secondString = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notEqual . PHP_EOL . $diffValue)
                ->mock($locale)->call('_')->withArguments('strings are not equal')->once
                ->mock($diff)
                    ->call('setExpected')->withArguments($secondString)->once
                    ->call('setActual')->withArguments($firstString)->once

                ->exception(function () use ($asserter, & $secondString, & $failMessage) {
                    $asserter->isEqualTo($secondString = uniqid(), $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage . PHP_EOL . $diffValue)
                ->mock($diff)
                    ->call('setExpected')->withArguments($secondString)->once
                    ->call('setActual')->withArguments($firstString)->twice

                ->object($asserter->isEqualTo($firstString))->isIdenticalTo($asserter)
        ;
    }

    public function testIsEqualToFileContents()
    {
        $this
            ->if($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isEqualToContentsOfFile(uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is undefined')

            ->if(
                $asserter
                    ->setWith($firstString = uniqid())
                    ->setLocale($locale = new \mock\atoum\atoum\locale())
                    ->setDiff($diff = new \mock\atoum\atoum\tools\diffs\variable()),
                $this->calling($locale)->_ = $unableToGetContents = uniqid(),
                $this->function->file_get_contents = false
            )
            ->then
                ->exception(function () use ($asserter, & $path) {
                    $asserter->isEqualToContentsOfFile($path = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($unableToGetContents)
                ->mock($locale)->call('_')->withArguments('Unable to get contents of file %s', $path)->once

            ->if(
                $this->calling($diff)->__toString = $diffValue = uniqid(),
                $this->function->file_get_contents = $fileContents = uniqid(),
                $this->calling($locale)->_ = $notEqual = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, $path) {
                    $asserter->isEqualToContentsOfFile($path);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notEqual . PHP_EOL . $diffValue)
                ->mock($locale)->call('_')->withArguments('string is not equal to contents of file %s', $path)->once
                ->mock($diff)
                    ->call('setExpected')->withArguments($fileContents)->once
                    ->call('setActual')->withArguments($firstString)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->isEqualToContentsOfFile(uniqid(), $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage . PHP_EOL . $diffValue)

            ->if($this->function->file_get_contents = $firstString)
            ->then
                ->object($asserter->isEqualToContentsOfFile($path))->isIdenticalTo($asserter)
        ;
    }

    public function testIsEmpty()
    {
        $this
            ->if($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isEmpty();
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is undefined')

            ->if(
                $asserter
                    ->setWith($firstString = uniqid())
                    ->setLocale($locale = new \mock\atoum\atoum\locale())
                    ->setDiff($diff = new \mock\atoum\atoum\tools\diffs\variable()),
                $this->calling($locale)->_ = $notEmpty = uniqid(),
                $this->calling($diff)->__toString = $diffValue = uniqid()
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isEmpty();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notEmpty . PHP_EOL . $diffValue)
                ->mock($locale)->call('_')->withArguments('string is not empty')->once
                ->mock($diff)
                    ->call('setExpected')->withArguments('')->once
                    ->call('setActual')->withArguments($firstString)->once

                ->exception(function () use ($asserter) {
                    $asserter->isEmpty;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notEmpty . PHP_EOL . $diffValue)
                ->mock($locale)->call('_')->withArguments('string is not empty')->twice
                ->mock($diff)
                    ->call('setExpected')->withArguments('')->twice
                    ->call('setActual')->withArguments($firstString)->twice

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->isEmpty($failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage . PHP_EOL . $diffValue)

            ->if($asserter->setWith(''))
            ->then
                ->object($asserter->isEmpty())->isIdenticalTo($asserter)
        ;
    }

    public function testIsNotEmpty()
    {
        $this
            ->if($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isNotEmpty();
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is undefined')

            ->if(
                $asserter
                    ->setWith('')
                    ->setLocale($locale = new \mock\atoum\atoum\locale()),
                $this->calling($locale)->_ = $isEmpty = uniqid()
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->isNotEmpty();
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($isEmpty)
                ->mock($locale)->call('_')->withArguments('string is empty')->once

                ->exception(function () use ($asserter) {
                    $asserter->isNotEmpty;
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($isEmpty)
                ->mock($locale)->call('_')->withArguments('string is empty')->twice

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->isNotEmpty($failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->if($asserter->setWith(uniqid()))
            ->then
                ->object($asserter->isNotEmpty())->isIdenticalTo($asserter)
        ;
    }

    public function testHasLength()
    {
        $this
            ->if($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasLength(rand(0, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is undefined')

            ->if(
                $asserter
                    ->setWith('')
                    ->setLocale($locale = new \mock\atoum\atoum\locale()),
                $this->calling($locale)->_ = $hasNotLength = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, & $requiredLength) {
                    $asserter->hasLength($requiredLength = rand(1, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($hasNotLength)
                ->mock($locale)->call('_')->withArguments('length of %s is not %d', $asserter, $requiredLength)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->hasLength(rand(1, PHP_INT_MAX), $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->if($asserter->setWith($string = uniqid()))
            ->then
                ->object($asserter->hasLength(strlen($string)))->isIdenticalTo($asserter)
        ;
    }

    public function testHasLengthGreaterThan()
    {
        $this
            ->if($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasLengthGreaterThan(rand(0, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is undefined')

            ->if(
                $asserter
                    ->setWith('Chuck Norris')
                    ->setLocale($locale = new \mock\atoum\atoum\locale()),
                $this->calling($locale)->_ = $lengthNotGreater = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, & $requiredLength) {
                    $asserter->hasLengthGreaterThan($requiredLength = rand(1, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($lengthNotGreater)
                ->mock($locale)->call('_')->withArguments('length of %s is not greater than %d', $asserter, $requiredLength)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->hasLengthGreaterThan(rand(1, PHP_INT_MAX), $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->if($asserter->setWith($string = uniqid()))
            ->then
                ->object($asserter->hasLengthGreaterThan(strlen($string) - 1))->isIdenticalTo($asserter)
        ;
    }

    public function testHasLengthLessThan()
    {
        $this
            ->if($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->hasLengthLessThan(rand(0, PHP_INT_MAX));
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is undefined')

            ->if(
                $asserter
                    ->setWith('Chuck Norris')
                    ->setLocale($locale = new \mock\atoum\atoum\locale()),
                $this->calling($locale)->_ = $lengthNotLess = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, & $requiredLength) {
                    $asserter->hasLengthLessThan($requiredLength = 10);
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($lengthNotLess)
                ->mock($locale)->call('_')->withArguments('length of %s is not less than %d', $asserter, $requiredLength)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->hasLengthLessThan(10, $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->if($asserter->setWith($string = uniqid()))
            ->then
                ->object($asserter->hasLengthLessThan(strlen($string) + 1))->isIdenticalTo($asserter)
        ;
    }

    public function testContains()
    {
        $this
            ->if($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->contains(uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is undefined')

            ->if(
                $asserter
                    ->setWith($string = 'Chuck Norris')
                    ->setLocale($locale = new \mock\atoum\atoum\locale()),
                $this->calling($locale)->_ = $notContains = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, & $fragment) {
                    $asserter->contains($fragment = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notContains)
                ->mock($locale)->call('_')->withArguments('%s does not contain %s', $asserter, $fragment)->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->contains(uniqid(), $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

            ->if($asserter->setWith(uniqid() . $string . uniqid()))
            ->then
                ->object($asserter->contains($string))->isIdenticalTo($asserter)

                ->exception(function () use ($asserter, $string, & $fragment) {
                    $asserter->contains($fragment = strtoupper($string));
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notContains)
                ->mock($locale)->call('_')->withArguments('%s does not contain %s', $asserter, $fragment)->once
        ;
    }

    public function testNotContains(variable\analyzer $analyzer)
    {
        $this
            ->if($asserter = $this->newTestedInstance(null, $analyzer))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->notContains(uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is undefined')

            ->if(
                $asserter
                    ->setWith($string = 'FreeAgent scans the field')
                    ->setLocale($locale = new \mock\atoum\atoum\locale()),
                $this->calling($locale)->_ = $contains = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, & $fragment) {
                    $asserter->notContains($fragment = 'Agent');
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($contains)
                ->mock($locale)->call('_')->withArguments('%s contains %s', $asserter, $analyzer->getTypeOf($fragment))->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->notContains('Agent', $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->object($asserter->notContains('agent'))->isIdenticalTo($asserter)
                ->object($asserter->notContains(uniqid()))->isIdenticalTo($asserter)
        ;
    }

    public function testStartWith(variable\analyzer $analyzer)
    {
        $this
            ->if($asserter = $this->newTestedInstance(null, $analyzer))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->startWith(uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is undefined')

            ->if(
                $asserter
                    ->setWith('FreeAgent scans the field')
                    ->setLocale($locale = new \mock\atoum\atoum\locale()),
                $this->calling($locale)->_ = $notStartWith = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, & $fragment) {
                    $asserter->startWith($fragment = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notStartWith)
                ->mock($locale)->call('_')->withArguments('%s does not start with %s', $asserter, $analyzer->getTypeOf($fragment))->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->startWith(uniqid(), $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->exception(function () use ($asserter, & $fragment) {
                    $asserter->startWith($fragment = 'free');
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notStartWith)
                ->mock($locale)->call('_')->withArguments('%s does not start with %s', $asserter, $analyzer->getTypeOf($fragment))->once

                ->exception(function () use ($asserter, & $fragment) {
                    $asserter->startWith($fragment = 'Free' . uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notStartWith)
                ->mock($locale)->call('_')->withArguments('%s does not start with %s', $asserter, $analyzer->getTypeOf($fragment))->once

                ->exception(function () use ($asserter, & $fragment) {
                    $asserter->startWith('field');
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notStartWith)
                ->mock($locale)->call('_')->withArguments('%s does not start with %s', $asserter, $analyzer->getTypeOf('field'))->once

                ->object($asserter->startWith('Free'))->isIdenticalTo($asserter)
        ;
    }

    public function testNotStartWith(variable\analyzer $analyzer)
    {
        $this
            ->if($asserter = $this->newTestedInstance(null, $analyzer))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->notStartWith(uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is undefined')

            ->if(
                $asserter
                    ->setWith('FreeAgent scans the field')
                    ->setLocale($locale = new \mock\atoum\atoum\locale()),
                $this->calling($locale)->_ = $startWith = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, & $fragment) {
                    $asserter->notStartWith($fragment = 'FreeA');
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($startWith)
                ->mock($locale)->call('_')->withArguments('%s start with %s', $asserter, $analyzer->getTypeOf($fragment))->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->notStartWith('FreeAgent ', $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->object($asserter->notStartWith('free'))->isIdenticalTo($asserter)
                ->object($asserter->notStartWith(uniqid()))->isIdenticalTo($asserter)
        ;
    }

    public function testEndWith(variable\analyzer $analyzer)
    {
        $this
            ->if($asserter = $this->newTestedInstance(null, $analyzer))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->endWith(uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is undefined')

            ->if(
                $asserter
                    ->setWith('FreeAgent scans the field')
                    ->setLocale($locale = new \mock\atoum\atoum\locale()),
                $this->calling($locale)->_ = $notEndWith = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, & $fragment) {
                    $asserter->endWith($fragment = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notEndWith)
                ->mock($locale)->call('_')->withArguments('%s does not end with %s', $asserter, $analyzer->getTypeOf($fragment))->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->endWith(uniqid(), $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->endWith('FIELd');
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notEndWith)
                ->mock($locale)->call('_')->withArguments('%s does not end with %s', $asserter, $analyzer->getTypeOf('FIELd'))->once

                ->exception(function () use ($asserter, & $fragment) {
                    $asserter->endWith($fragment = uniqid() . ' field');
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($notEndWith)
                ->mock($locale)->call('_')->withArguments('%s does not end with %s', $asserter, $analyzer->getTypeOf($fragment))->once

                ->object($asserter->endWith('field'))->isIdenticalTo($asserter)
        ;
    }

    public function testNotEndWith(variable\analyzer $analyzer)
    {
        $this
            ->if($asserter = $this->newTestedInstance(null, $analyzer))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->notEndWith(uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is undefined')

            ->if(
                $asserter
                    ->setWith('FreeAgent scans the field')
                    ->setLocale($locale = new \mock\atoum\atoum\locale()),
                $this->calling($locale)->_ = $endWith = uniqid()
            )
            ->then
                ->exception(function () use ($asserter, & $fragment) {
                    $asserter->notEndWith($fragment = ' the field');
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($endWith)
                ->mock($locale)->call('_')->withArguments('%s end with %s', $asserter, $analyzer->getTypeOf($fragment))->once

                ->exception(function () use ($asserter, & $failMessage) {
                    $asserter->notEndWith(' the field', $failMessage = uniqid());
                })
                    ->isInstanceOf(atoum\asserter\exception::class)
                    ->hasMessage($failMessage)

                ->object($asserter->notEndWith(' THE FIELD'))->isIdenticalTo($asserter)
                ->object($asserter->notEndWith(uniqid()))->isIdenticalTo($asserter)
        ;
    }

    public function testLength()
    {
        $this
            ->if($asserter = $this->newTestedInstance)
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->length;
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is undefined')

            ->if($asserter->setWith(''))
            ->then
                ->object($integer = $asserter->length)
                    ->isInstanceOf(atoum\asserters\integer::class)
                ->integer($integer->getValue())
                    ->isEqualTo(0)

            ->if($asserter->setWith($string = uniqid()))
            ->then
                ->object($integer = $asserter->length)
                    ->isInstanceOf(atoum\asserters\integer::class)
                ->integer($integer->getValue())
                    ->isEqualTo(strlen($string))
        ;
    }

    public function testMatch()
    {
        $this
            ->if($asserter = $this->newTestedInstance)
            ->and($asserter->setWith(uniqid('foo', true)))
            ->and($failMessage = uniqid())
            ->then
                ->exception(function () use ($asserter, $failMessage) {
                    $asserter->match('/' . uniqid('bar', true) . '/', $failMessage);
                })
                ->isInstanceOf(atoum\asserter\exception::class)
                ->hasMessage($failMessage)
        ;
    }

    public function testNotMatches()
    {
        $this
            ->if($asserter = $this->newTestedInstance)
            ->and($asserter->setWith(uniqid('foo', true)))
            ->and($failMessage = uniqid())
            ->then
                ->exception(function () use ($asserter, $failMessage) {
                    $asserter->notMatches('/foo/', $failMessage);
                })
                ->isInstanceOf(atoum\asserter\exception::class)
                ->hasMessage($failMessage)
        ;
    }

    public function testToArray()
    {
        $this
            ->if($asserter = $this->newTestedInstance(new asserter\generator()))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->toArray();
                })
                    ->isInstanceOf(atoum\exceptions\logic::class)
                    ->hasMessage('Value is undefined')
            ->if($asserter->setWith(uniqid()))
            ->then
                ->object($asserter->toArray())->isInstanceOf(atoum\asserters\castToArray::class)
        ;
    }
}
