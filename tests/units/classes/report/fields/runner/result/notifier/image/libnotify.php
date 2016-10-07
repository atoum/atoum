<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\result\notifier\image;

use mageekguy\atoum;
use mageekguy\atoum\locale;
use mageekguy\atoum\report\fields\runner\result\notifier\image\libnotify as testedClass;
use mageekguy\atoum\test\adapter;

require_once __DIR__ . '/../../../../../../runner.php';

class libnotify extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends('mageekguy\atoum\report\fields\runner\result\notifier\image')
        ;
    }

    public function test__construct()
    {
        $this
            ->if($field = new testedClass())
            ->then
                ->object($field->getLocale())->isEqualTo(new locale())
                ->variable($field->getTestNumber())->isNull()
                ->variable($field->getTestMethodNumber())->isNull()
                ->variable($field->getFailNumber())->isNull()
                ->variable($field->getErrorNumber())->isNull()
                ->variable($field->getExceptionNumber())->isNull()
                ->array($field->getEvents())->isEqualTo([atoum\runner::runStop])
        ;
    }

    public function testHandleEvent()
    {
        $this
            ->if($score = new \mock\mageekguy\atoum\runner\score())
            ->and($this->calling($score)->getAssertionNumber = $assertionNumber = rand(1, PHP_INT_MAX))
            ->and($this->calling($score)->getFailNumber = $failNumber = rand(1, PHP_INT_MAX))
            ->and($this->calling($score)->getErrorNumber = $errorNumber = rand(1, PHP_INT_MAX))
            ->and($this->calling($score)->getExceptionNumber = $exceptionNumber = rand(1, PHP_INT_MAX))
            ->and($runner = new \mock\mageekguy\atoum\runner())
            ->and($runner->setScore($score))
            ->and($this->calling($runner)->getTestNumber = $testNumber = rand(1, PHP_INT_MAX))
            ->and($this->calling($runner)->getTestMethodNumber = $testMethodNumber = rand(1, PHP_INT_MAX))
            ->and($field = new testedClass())
            ->then
                ->boolean($field->handleEvent(atoum\runner::runStart, $runner))->isFalse()
                ->variable($field->getSuccessImage())->isNull()
                ->variable($field->getFailureImage())->isNull()
                ->variable($field->getTestNumber())->isNull()
                ->variable($field->getTestMethodNumber())->isNull()
                ->variable($field->getAssertionNumber())->isNull()
                ->variable($field->getFailNumber())->isNull()
                ->variable($field->getErrorNumber())->isNull()
                ->variable($field->getExceptionNumber())->isNull()
                ->boolean($field->handleEvent(atoum\runner::runStop, $runner))->isTrue()
                ->integer($field->getTestNumber())->isEqualTo($testNumber)
                ->integer($field->getTestMethodNumber())->isEqualTo($testMethodNumber)
                ->integer($field->getAssertionNumber())->isEqualTo($assertionNumber)
                ->integer($field->getFailNumber())->isEqualTo($failNumber)
                ->integer($field->getErrorNumber())->isEqualTo($errorNumber)
                ->integer($field->getExceptionNumber())->isEqualTo($exceptionNumber)
        ;
    }

    public function testNotify()
    {
        $this
            ->if($adapter = new adapter())
            ->and($adapter->system = function () {
            })
            ->and($adapter->file_exists = true)
            ->and($score = new \mock\mageekguy\atoum\score())
            ->and($runner = new \mock\mageekguy\atoum\runner())
            ->and($this->calling($runner)->getScore = $score)
            ->and($locale = new \mock\mageekguy\atoum\locale())
            ->and($this->calling($locale)->_ = function ($string) use (& $noTestRunningString, & $successString, & $failureString) {
                switch ($string) {
                        case '%s %s %s %s %s':
                            return $successString = uniqid();

                        case '%s %s %s %s %s %s %s %s':
                            return $failureString = uniqid();

                        default:
                            return uniqid();
                    }
            }
            )
            ->and($this->calling($locale)->__ = function ($singularString, $pluralString, $number) use (& $testString, & $testMethodString, & $testVoidMethodString, & $testSkippedMethodString, & $assertionString, & $errorString, & $exceptionString) {
                switch ($singularString) {
                        case '%s test':
                            return $testString = uniqid();

                        case '%s method':
                            return $testMethodString = uniqid();

                        case '%s void method':
                            return $testVoidMethodString = uniqid();

                        case '%s skipped method':
                            return $testSkippedMethodString = uniqid();

                        case '%s assertion':
                            return $assertionString = uniqid();

                        case '%s error':
                            return $errorString = uniqid();

                        case '%s exception':
                            return $exceptionString = uniqid();

                        default:
                            return uniqid();
                    }
            }
            )
            ->assert('Success with one test, one method and one assertion, no fail, no error, no exception')
            ->and($this->calling($runner)->getTestNumber = 1)
            ->and($this->calling($runner)->getTestMethodNumber = 1)
            ->and($this->calling($score)->getAssertionNumber = 1)
            ->and($this->calling($score)->getFailNumber = 0)
            ->and($this->calling($score)->getErrorNumber = 0)
            ->and($this->calling($score)->getExceptionNumber = 0)
            ->and($field = new testedClass($adapter))
            ->and($field->setLocale($locale))
            ->and($field->setSuccessImage($image = uniqid()))
            ->and($field->handleEvent(atoum\runner::runStop, $runner))
            ->then
                ->castToString($field)->isEmpty()
                ->mock($locale)
                    ->call('_')->withArguments('%s %s %s %s %s')
                    ->call('__')->withArguments('%s test', '%s tests', 1)->once()
                    ->call('__')->withArguments('%s/%s method', '%s/%s methods', 1)->once()
                    ->call('__')->withArguments('%s skipped method', '%s skipped methods', 0)->once()
                    ->call('__')->withArguments('%s assertion', '%s assertions', 1)->once()
                ->adapter($adapter)
                    ->call('system')->withArguments(sprintf('notify-send -i %3$s %1$s %2$s', escapeshellarg('Success!'), escapeshellarg($successString), escapeshellarg($image)))->once()
            ->assert('Success with several tests, several methods and several assertions,  no fail, no error, no exception')
            ->if($this->calling($runner)->getTestNumber = $testNumber = rand(2, PHP_INT_MAX))
            ->and($this->calling($runner)->getTestMethodNumber = $testMethodNumber = rand(2, PHP_INT_MAX))
            ->and($this->calling($score)->getAssertionNumber = $assertionNumber = rand(2, PHP_INT_MAX))
            ->and($field = new testedClass($adapter))
            ->and($field->setLocale($locale))
            ->and($field->setSuccessImage($image = uniqid()))
            ->and($field->handleEvent(atoum\runner::runStop, $runner))
            ->then
                ->castToString($field)->isEmpty()
                ->mock($locale)
                    ->call('_')->withArguments('%s %s %s %s %s')->once()
                    ->call('__')->withArguments('%s test', '%s tests', $testNumber)->once()
                    ->call('__')->withArguments('%s/%s method', '%s/%s methods', $testMethodNumber)->once()
                    ->call('__')->withArguments('%s void method', '%s void methods', 0)->once()
                    ->call('__')->withArguments('%s skipped method', '%s skipped methods', 0)->once()
                    ->call('__')->withArguments('%s assertion', '%s assertions', $assertionNumber)->once()
                ->adapter($adapter)
                    ->call('system')->withArguments(sprintf('notify-send -i %3$s %1$s %2$s', escapeshellarg('Success!'), escapeshellarg($successString), escapeshellarg($image)))->once()
            ->assert('Failure with several tests, several methods and several assertions, one fail, one error, one exception')
            ->if($this->calling($score)->getFailNumber = 1)
            ->and($this->calling($score)->getErrorNumber = 1)
            ->and($this->calling($score)->getExceptionNumber = 1)
            ->and($this->calling($score)->getUncompletedMethodNumber = 1)
            ->and($field = new testedClass($adapter))
            ->and($field->setLocale($locale))
            ->and($field->setFailureImage($image = uniqid()))
            ->and($field->handleEvent(atoum\runner::runStop, $runner))
            ->then
                ->castToString($field)->isEmpty()
                ->mock($locale)
                    ->call('_')->withArguments('%s %s %s %s %s %s %s %s')
                    ->call('__')->withArguments('%s test', '%s tests', $testNumber)->once()
                    ->call('__')->withArguments('%s/%s method', '%s/%s methods', $testMethodNumber)->once()
                    ->call('__')->withArguments('%s skipped method', '%s skipped methods', 0)->once()
                    ->call('__')->withArguments('%s uncompleted method', '%s uncompleted methods', 1)->once()
                    ->call('__')->withArguments('%s failure', '%s failures', 1)->once()
                    ->call('__')->withArguments('%s error', '%s errors', 1)->once()
                    ->call('__')->withArguments('%s exception', '%s exceptions', 1)->once()
                ->adapter($adapter)
                    ->call('system')->withArguments(sprintf('notify-send -i %3$s %1$s %2$s', escapeshellarg('Failure!'), escapeshellarg($failureString), escapeshellarg($image)))->once()
            ->assert('Failure with several tests, several methods and several assertions, several fails, several errors, several exceptions')
            ->if($this->calling($score)->getFailNumber = $failNumber = rand(2, PHP_INT_MAX))
            ->and($this->calling($score)->getErrorNumber = $errorNumber = rand(2, PHP_INT_MAX))
            ->and($this->calling($score)->getExceptionNumber = $exceptionNumber = rand(2, PHP_INT_MAX))
            ->and($this->calling($score)->getUncompletedMethodNumber = $uncompletedTestNumber = rand(2, PHP_INT_MAX))
            ->and($field = new testedClass($adapter))
            ->and($field->setLocale($locale))
            ->and($field->setFailureImage($image = uniqid()))
            ->and($field->handleEvent(atoum\runner::runStop, $runner))
            ->then
                ->castToString($field)->isEmpty()
                ->mock($locale)
                    ->call('_')->withArguments('%s %s %s %s %s %s %s %s')
                    ->call('__')->withArguments('%s test', '%s tests', $testNumber)->once()
                    ->call('__')->withArguments('%s/%s method', '%s/%s methods', $testMethodNumber)->once()
                    ->call('__')->withArguments('%s failure', '%s failures', $failNumber)->once()
                    ->call('__')->withArguments('%s error', '%s errors', $errorNumber)->once()
                    ->call('__')->withArguments('%s exception', '%s exceptions', $exceptionNumber)->once()
                ->adapter($adapter)
                    ->call('system')->withArguments(sprintf('notify-send -i %3$s %1$s %2$s', escapeshellarg('Failure!'), escapeshellarg($failureString), escapeshellarg($image)))->once()
        ;
    }
}
