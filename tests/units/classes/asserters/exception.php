<?php

namespace
{
    if (version_compare(PHP_VERSION, '7.0.0') < 0) {
        class throwable
        {
        }
    } else {
        interface throwableExtended extends throwable
        {
        }

        class exceptionExtended extends exception implements throwableExtended
        {
        }
    }
}

namespace mageekguy\atoum\tests\units\asserters
{
    use mageekguy\atoum;
    use mageekguy\atoum\asserter;
    use mageekguy\atoum\asserters;
    use mageekguy\atoum\tools\variable;

    require_once __DIR__ . '/../../runner.php';

    class exception extends atoum\test
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

        public function testSetWith()
        {
            $this
                ->given(
                    $this->newTestedInstance
                        ->setLocale($locale = new \mock\atoum\locale())
                        ->setAnalyzer($analyzer = new \mock\atoum\tools\variable\analyzer())
                )
                ->then
                    ->object($this->testedInstance->setWith($value = new \exception()))->isTestedInstance
                    ->exception($this->testedInstance->getValue())->isIdenticalTo($value)

                ->if(
                    $this->calling($locale)->_ = $notAnException = uniqid(),
                    $this->calling($analyzer)->getTypeOf = $type = uniqid()
                )
                ->then
                    ->exception(function () use (& $line, & $value) {
                        $line = __LINE__;
                        $this->testedInstance->setWith($value = uniqid());
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($notAnException)
                    ->mock($locale)->call('_')->withArguments('%s is not an exception', $type)->once
                    ->mock($analyzer)->call('getTypeOf')->withArguments($value)->once
                    ->string($this->testedInstance->getValue())->isEqualTo($value)
            ;
        }

        /** @php < 7.0.0 */
        public function testSetWithPhpLt7(atoum\locale $locale, atoum\tools\variable\analyzer $analyzer)
        {
            $this
                ->given(
                    $this->newTestedInstance
                        ->setLocale($locale)
                        ->setAnalyzer($analyzer),
                    $this->calling($locale)->_ = $notAnException = uniqid(),
                    $this->calling($analyzer)->getTypeOf = $type = uniqid()
                )
                ->then
                    ->exception(function () use (& $line, & $value) {
                        $line = __LINE__;
                        $this->testedInstance->setWith($value = new \throwable);
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($notAnException)
                    ->mock($locale)->call('_')->withArguments('%s is not an exception', $type)->once
                    ->mock($analyzer)->call('getTypeOf')->withArguments($value)->once
                    ->object($this->testedInstance->getValue())->isEqualTo($value)
            ;
        }

        /** @php >= 7.0.0 */
        public function testSetWithPhpGte7()
        {
            $this
                ->given($this->newTestedInstance)
                ->then
                    ->object($this->testedInstance->setWith($value = new \error()))->isTestedInstance
                    ->exception($this->testedInstance->getValue())->isIdenticalTo($value)
            ;
        }

        public function testIsInstanceOf()
        {
            $this
                ->given($this->newTestedInstance->setLocale($locale = new \mock\atoum\locale()))
                ->then
                    ->exception(function () {
                        $this->testedInstance->hasSize(rand(0, PHP_INT_MAX));
                    })
                        ->isInstanceOf(atoum\exceptions\logic::class)
                        ->hasMessage('Exception is undefined')

                ->if($this->testedInstance->setWith(new \exception()))
                ->then
                    ->object($this->testedInstance->isInstanceOf('\Exception'))->isTestedInstance
                    ->object($this->testedInstance->isInstanceOf('Exception'))->isTestedInstance
                    ->object($this->testedInstance->isInstanceOf('\exception'))->isTestedInstance
                    ->object($this->testedInstance->isInstanceOf('exception'))->isTestedInstance
                    ->object($this->testedInstance->isInstanceOf(\Exception::class))->isTestedInstance

                    ->exception(function () {
                        $this->testedInstance->isInstanceOf(uniqid());
                    })
                        ->isInstanceOf(atoum\exceptions\logic\invalidArgument::class)
                        ->hasMessage('Argument of mageekguy\atoum\asserters\exception::isInstanceOf() must be a \exception instance or an exception class name')

                ->if($this->calling($locale)->_ = $isNotAnInstance = uniqid())
                ->then
                    ->exception(function () {
                        $this->testedInstance->isInstanceOf(atoum\exceptions\runtime::class);
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($isNotAnInstance)
                    ->mock($locale)->call('_')->withArguments('%s is not an instance of %s', $this->testedInstance)->once

                    ->exception(function () use (& $failMessage) {
                        $this->testedInstance->isInstanceOf(atoum\exceptions\runtime::class, $failMessage = uniqid());
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($failMessage)
            ;
        }

        /** @php >= 7.0.0 */
        public function testIsInstanceOfPhpGte7()
        {
            $this
                ->given($this->newTestedInstance->setLocale($locale = new \mock\atoum\locale()))
                ->if($this->testedInstance->setWith(new \exception()))
                ->then
                    ->object($this->testedInstance->isInstanceOf(\throwable::class))
                ->if($this->testedInstance->setWith(new \exceptionExtended()))
                    ->object($this->testedInstance->isInstanceOf(\throwableExtended::class))
            ;
        }

        public function testHasCode()
        {
            $this
                ->given($this->newTestedInstance->setLocale($locale = new \mock\atoum\locale()))
                ->then
                    ->exception(function () {
                        $this->testedInstance->hasCode(rand(- PHP_INT_MAX, PHP_INT_MAX));
                    })
                        ->isInstanceOf(\logicException::class)
                        ->hasMessage('Exception is undefined')

                ->if($this->testedInstance->setWith(new \exception(uniqid(), $code = rand(2, PHP_INT_MAX))))
                ->then
                    ->object($this->testedInstance->hasCode($code))->isTestedInstance

                ->if($this->calling($locale)->_ = $hasNotCode = uniqid())
                ->then
                    ->exception(function () use (& $badCode) {
                        $this->testedInstance->hasCode($badCode = 1);
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($hasNotCode)
                    ->mock($locale)->call('_')->withArguments('code is %s instead of %s', $code, $badCode)->once

                    ->exception(function () use (& $failMessage) {
                        $this->testedInstance->hasCode(rand(1, PHP_INT_MAX), $failMessage = uniqid());
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($failMessage)
            ;
        }

        public function testHasDefaultCode()
        {
            $this
                ->given($this->newTestedInstance->setLocale($locale = new \mock\atoum\locale()))
                ->then
                    ->exception(function () {
                        $this->testedInstance->hasDefaultCode();
                    })
                        ->isInstanceOf(\logicException::class)
                        ->hasMessage('Exception is undefined')

                    ->exception(function () {
                        $this->testedInstance->hasDefaultCode;
                    })
                        ->isInstanceOf(\logicException::class)
                        ->hasMessage('Exception is undefined')

                ->if($this->testedInstance->setWith(new \exception(uniqid())))
                ->then
                    ->object($this->testedInstance->hasDefaultCode())->isTestedInstance
                    ->object($this->testedInstance->hasDefaultCode())->isTestedInstance

                ->if(
                    $this->testedInstance->setWith(new \exception(uniqid(), $code = rand(1, PHP_INT_MAX))),
                    $this->calling($locale)->_ = $hasNotDefaultCode = uniqid()
                )
                ->then
                    ->exception(function () {
                        $this->testedInstance->hasDefaultCode();
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($hasNotDefaultCode)
                    ->mock($locale)->call('_')->withArguments('code is %s instead of 0', $code)->once

                    ->exception(function () {
                        $this->testedInstance->hasDefaultCode;
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($hasNotDefaultCode)
                    ->mock($locale)->call('_')->withArguments('code is %s instead of 0', $code)->twice

                    ->exception(function () use (& $failMessage) {
                        $this->testedInstance->hasDefaultCode($failMessage = uniqid());
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($failMessage)
            ;
        }

        public function testHasMessage()
        {
            $this
                ->given($this->newTestedInstance->setLocale($locale = new \mock\atoum\locale()))
                ->then
                    ->exception(function () {
                        $this->testedInstance->hasMessage(uniqid());
                    })
                        ->isInstanceOf(\logicException::class)
                        ->hasMessage('Exception is undefined')

                ->if($this->testedInstance->setWith(new \exception($message = uniqid())))
                ->then
                    ->object($this->testedInstance->hasMessage($message))->isTestedInstance

                ->if($this->calling($locale)->_ = $hasNotMessage = uniqid())
                ->then
                    ->exception(function () use (& $badMessage) {
                        $this->testedInstance->hasMessage($badMessage = uniqid());
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($hasNotMessage)
                    ->mock($locale)->call('_')->withArguments('message \'%s\' is not identical to \'%s\'', $message, $badMessage)->once

                    ->exception(function () use (& $failMessage) {
                        $this->testedInstance->hasMessage(uniqid(), $failMessage = uniqid());
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($failMessage)
            ;
        }

        public function testHasNestedException()
        {
            $this
                ->if($this->newTestedInstance)
                ->then
                    ->exception(function () {
                        $this->testedInstance->hasNestedException();
                    })
                        ->isInstanceOf(\logicException::class)
                        ->hasMessage('Exception is undefined')

                ->if(
                    $this->testedInstance
                        ->setWith(new \exception())
                        ->setLocale($locale = new \mock\atoum\locale()),
                    $this->calling($locale)->_ = $hasNoNestedException = uniqid()
                )
                ->then
                    ->exception(function () {
                        $this->testedInstance->hasNestedException();
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($hasNoNestedException)
                    ->mock($locale)->call('_')->withArguments('exception does not contain any nested exception')->once

                    ->exception(function () {
                        $this->testedInstance->hasNestedException;
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($hasNoNestedException)
                    ->mock($locale)->call('_')->withArguments('exception does not contain any nested exception')->twice

                    ->exception(function () use (& $failMessage) {
                        $this->testedInstance->hasNestedException(null, $failMessage = uniqid());
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($failMessage)

                    ->exception(function () {
                        $this->testedInstance->hasNestedException(new \exception());
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($hasNoNestedException)
                    ->mock($locale)->call('_')->withArguments('exception does not contain this nested exception')->once

                ->if($this->testedInstance->setWith(new \exception(uniqid(), rand(1, PHP_INT_MAX), $nestedException = new \exception())))
                ->then
                    ->object($this->testedInstance->hasNestedException())->isTestedInstance

                    ->object($this->testedInstance->hasNestedException($nestedException))->isTestedInstance

                    ->exception(function () {
                        $this->testedInstance->hasNestedException(new \exception());
                    })
                        ->isInstanceOf(atoum\asserter\exception::class)
                        ->hasMessage($hasNoNestedException)
                    ->mock($locale)->call('_')->withArguments('exception does not contain this nested exception')->twice
            ;
        }

        public function testMessage()
        {
            $this
                ->if($this->newTestedInstance)
                ->then
                    ->exception(function () {
                        $this->testedInstance->message;
                    })
                        ->isInstanceOf(atoum\exceptions\logic::class)
                        ->hasMessage('Exception is undefined')

                    ->exception(function () {
                        $this->testedInstance->mESSAGe;
                    })
                        ->isInstanceOf(atoum\exceptions\logic::class)
                        ->hasMessage('Exception is undefined')

                ->if($this->testedInstance->setWith(new \exception('')))
                ->then
                    ->object($string = $this->testedInstance->message)->isInstanceOf(atoum\asserters\phpString::class)
                    ->string($string->getValue())->isEqualTo('')

                    ->object($string = $this->testedInstance->MesSAge)->isInstanceOf(atoum\asserters\phpString::class)
                    ->string($string->getValue())->isEqualTo('')

                ->if($this->testedInstance->setWith(new \exception($message = uniqid())))
                ->then
                    ->object($string = $this->testedInstance->message)->isInstanceOf(atoum\asserters\phpString::class)
                    ->string($string->getValue())->isEqualTo($message)

                    ->object($string = $this->testedInstance->meSSAGe)->isInstanceOf(atoum\asserters\phpString::class)
                    ->string($string->getValue())->isEqualTo($message)
            ;
        }

        public function testGetLastValue()
        {
            $this
                ->variable(asserters\exception::getLastValue())->isNull()

                ->if(
                    $this->newTestedInstance->setWith(function () use (& $exception) {
                        $exception = new \exception();
                        throw $exception;
                    })
                )
                ->then
                    ->object(asserters\exception::getLastValue())->isIdenticalTo($exception)

                ->if($this->testedInstance->setWith(function () use (& $otherException) {
                    $otherException = new \exception();
                    throw $otherException;
                }))
                ->then
                    ->object(asserters\exception::getLastValue())->isIdenticalTo($otherException)
            ;
        }

        public function test__get()
        {
            $this
                ->given(
                    $generator = new \mock\atoum\asserter\generator(),
                    $this->newTestedInstance($generator)
                )
                ->if($this->calling($generator)->__get = $asserterInstance = new \mock\atoum\asserter())
                ->then
                    ->object($this->testedInstance->{$asserterClass = uniqid()})->isIdenticalTo($asserterInstance)
                    ->mock($generator)->call('__get')->withArguments($asserterClass)->once
            ;
        }
    }
}
