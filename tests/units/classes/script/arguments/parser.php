<?php

namespace mageekguy\atoum\tests\units\script\arguments;

use mageekguy\atoum;
use mageekguy\atoum\script;

require_once __DIR__ . '/../../../runner.php';

class parser extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->hasInterface(\iteratorAggregate::class);
    }

    public function test__construct()
    {
        $this
            ->if($parser = new script\arguments\parser())
            ->then
                ->object($parser->getSuperGlobals())->isEqualTo(new atoum\superglobals())
                ->array($parser->getValues())->isEmpty()
                ->array($parser->getHandlers())->isEmpty()
                ->variable($parser->getDefaultHandler())->isNull()
                ->array($parser->getPriorities())->isEmpty()
                ->object($parser->getIterator())->isEmpty()
                ->boolean($parser->hasFoundArguments())->isFalse()
            ->if($parser = new script\arguments\parser($superglobals = new atoum\superglobals()))
            ->then
                ->object($parser->getSuperGlobals())->isIdenticalTo($superglobals)
                ->array($parser->getValues())->isEmpty()
                ->array($parser->getHandlers())->isEmpty()
                ->variable($parser->getDefaultHandler())->isNull()
                ->array($parser->getPriorities())->isEmpty()
                ->object($parser->getIterator())->isEmpty()
                ->boolean($parser->hasFoundArguments())->isFalse()
        ;
    }

    public function test__toString()
    {
        $this
            ->if($parser = new script\arguments\parser())
            ->then
                ->castToString($parser)->isEmpty()
            ->if($parser->parse(new \mock\mageekguy\atoum\script(uniqid()), []))
            ->then
                ->castToString($parser)->isEmpty()
            ->if($parser->addHandler(function ($script, $argument, $values) {
            }, ['-a']))
            ->and($parser->parse(new \mock\mageekguy\atoum\script(uniqid()), []))
            ->then
                ->castToString($parser)->isEmpty()
            ->if($parser->parse(new \mock\mageekguy\atoum\script(uniqid()), ['-a']))
            ->then
                ->castToString($parser)->isEqualTo('-a')
            ->if($parser->parse(new \mock\mageekguy\atoum\script(uniqid()), ['-a', 'A']))
            ->then
                ->castToString($parser)->isEqualTo('-a A')
            ->if($parser->parse(new \mock\mageekguy\atoum\script(uniqid()), ['-a', 'A', 'B', 'C']))
            ->then
                ->castToString($parser)->isEqualTo('-a A B C')
            ->if($parser->addHandler(function ($script, $argument, $values) {
            }, ['--b']))
            ->and($parser->parse(new \mock\mageekguy\atoum\script(uniqid()), ['-a', 'A', 'B', 'C']))
            ->then
                ->castToString($parser)->isEqualTo('-a A B C')
            ->and($parser->parse(new \mock\mageekguy\atoum\script(uniqid()), ['-a', 'A', 'B', 'C', '--b']))
            ->then
                ->castToString($parser)->isEqualTo('-a A B C --b')
        ;
    }

    public function testSetSuperglobals()
    {
        $this
            ->if($parser = new script\arguments\parser())
            ->then
                ->object($parser->setSuperglobals($superglobals = new atoum\superglobals()))->isIdenticalTo($parser)
                ->object($parser->getSuperGlobals())->isIdenticalTo($superglobals)
        ;
    }

    public function testGetValues()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\script(uniqid()))
            ->and($parser = new script\arguments\parser())
            ->then
                ->array($parser->getValues())->isEmpty()
                ->variable($parser->getValues(uniqid()))->isNull()
            ->if($parser->addHandler(function ($script, $argument, $value) {
            }, ['-a'])->parse($script, ['-a']))
            ->then
                ->array($parser->getValues())->isEqualTo(['-a' => []])
                ->array($parser->getValues('-a'))->isEmpty()
                ->variable($parser->getValues(uniqid()))->isNull()
            ->if($parser->parse($script, ['-a', 'a1', 'a2']))
            ->then
                ->array($parser->getValues())->isEqualTo(['-a' => ['a1', 'a2']])
                ->array($parser->getValues('-a'))->isEqualTo(['a1', 'a2'])
                ->variable($parser->getValues(uniqid()))->isNull()
            ->if($parser->parse($script, ['-a', 'a1', '-a', 'a2']))
            ->then
                ->array($parser->getValues())->isEqualTo(['-a' => ['a1', 'a2']])
                ->array($parser->getValues('-a'))->isEqualTo(['a1', 'a2'])
                ->variable($parser->getValues(uniqid()))->isNull()
            ->if($parser->addHandler(function ($script, $argument, $value) {
            }, ['-b'])->parse($script, ['-a', 'a1', 'a2', '-b']))
            ->then
                ->array($parser->getValues())->isEqualTo(['-a' => ['a1', 'a2'], '-b' => []])
                ->array($parser->getValues('-a'))->isEqualTo(['a1', 'a2'])
                ->array($parser->getValues('-b'))->isEmpty()
                ->variable($parser->getValues(uniqid()))->isNull()
        ;
    }

    public function testGetIterator()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\script(uniqid()))
            ->and($parser = new script\arguments\parser())
            ->and($parser->parse($script, []))
            ->then
                ->object($parser->getIterator())
                    ->isInstanceOf(\arrayIterator::class)
                    ->isEmpty()
            ->if(
                $parser
                    ->addHandler(function ($script, $argument, $value) {
                    }, ['-a'])
                    ->addHandler(function ($script, $argument, $value) {
                    }, ['-b'])
                    ->parse($script, ['-a', 'a1', 'a2', '-b'])
            )
            ->then
                ->object($parser->getIterator())
                    ->isInstanceOf(\arrayIterator::class)
                    ->isEqualTo(new \arrayIterator($parser->getValues()))
        ;
    }

    public function testParse()
    {
        $this
            ->assert('when using $_SERVER')
                ->if($script = new \mock\mageekguy\atoum\script(uniqid()))
                ->and($superglobals = new atoum\superglobals())
                ->and($superglobals->_SERVER['argv'] = [])
                ->and($parser = new script\arguments\parser($superglobals))
                ->then
                    ->object($parser->parse($script))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEmpty()
                    ->boolean($parser->hasFoundArguments())->isFalse()
                ->if($superglobals->_SERVER['argv'] = ['scriptName'])
                ->then
                    ->object($parser->parse($script))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEmpty()
                    ->boolean($parser->hasFoundArguments())->isFalse()
                ->if($superglobals->_SERVER['argv'] = ['scriptName', '-a'])
                ->and($parser->addHandler(function ($script, $argument, $value) use (& $invokeA) {
                    $invokeA++;
                }, ['-a']))
                ->then
                    ->object($parser->parse($script))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-a' => []])
                    ->integer($invokeA)->isEqualTo(1)
                    ->boolean($parser->hasFoundArguments())->isTrue()
                ->if($superglobals->_SERVER['argv'] = ['scriptName', '-a', '-b'])
                ->and($parser->addHandler(function ($script, $argument, $value) use (& $invokeB) {
                    $invokeB++;
                }, ['-b']))
                ->then
                    ->object($parser->parse($script))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-a' => [], '-b' => []])
                    ->integer($invokeA)->isEqualTo(2)
                    ->integer($invokeB)->isEqualTo(1)
                    ->boolean($parser->hasFoundArguments())->isTrue()
                ->if($superglobals->_SERVER['argv'] = ['scriptName', '-a', 'a1'])
                ->then
                    ->object($parser->parse($script))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-a' => ['a1']])
                    ->integer($invokeA)->isEqualTo(3)
                    ->integer($invokeB)->isEqualTo(1)
                    ->boolean($parser->hasFoundArguments())->isTrue()
                ->if($superglobals->_SERVER['argv'] = ['scriptName', '-a', 'a1', 'a2'])
                ->then
                    ->object($parser->parse($script))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-a' => ['a1', 'a2']])
                    ->integer($invokeA)->isEqualTo(4)
                    ->integer($invokeB)->isEqualTo(1)
                    ->boolean($parser->hasFoundArguments())->isTrue()
                ->if($superglobals->_SERVER['argv'] = ['scriptName', '-a', 'a1', '-a', 'a2'])
                ->then
                    ->object($parser->parse($script))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-a' => ['a1', 'a2']])
                    ->integer($invokeA)->isEqualTo(5)
                    ->integer($invokeB)->isEqualTo(1)
                    ->boolean($parser->hasFoundArguments())->isTrue()
                ->if($superglobals->_SERVER['argv'] = ['scriptName', '-a', 'a1', 'a2', '-b'])
                ->then
                    ->object($parser->parse($script))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-a' => ['a1', 'a2'], '-b' => []])
                    ->integer($invokeA)->isEqualTo(6)
                    ->integer($invokeB)->isEqualTo(2)
                    ->boolean($parser->hasFoundArguments())->isTrue()
                ->if($superglobals->_SERVER['argv'] = ['scriptName', '-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3'])
                ->then
                    ->object($parser->parse($script))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-a' => ['a1', 'a2'], '-b' => ['b1', 'b2', 'b3']])
                    ->integer($invokeA)->isEqualTo(7)
                    ->integer($invokeB)->isEqualTo(3)
                    ->boolean($parser->hasFoundArguments())->isTrue()
                ->if($superglobals->_SERVER['argv'] = ['scriptName', '-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3', '--c'])
                ->and($parser->addHandler(function ($script, $argument, $value) use (& $invokeC) {
                    $invokeC++;
                }, ['--c']))
                ->then
                    ->object($parser->parse($script))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-a' => ['a1', 'a2'], '-b' => ['b1', 'b2', 'b3'], '--c' => []])
                    ->integer($invokeA)->isEqualTo(8)
                    ->integer($invokeB)->isEqualTo(4)
                    ->integer($invokeC)->isEqualTo(1)
                    ->boolean($parser->hasFoundArguments())->isTrue()
                ->if($superglobals->_SERVER['argv'] = ['scriptName', '-ac'])
                ->then
                    ->exception(function () use ($parser, $script) {
                        $parser->parse($script);
                    })
                        ->isInstanceOf(atoum\exceptions\runtime\unexpectedValue::class)
                        ->hasMessage('Argument \'-ac\' is unknown, did you mean \'--c\'?')
                ->if($superglobals->_SERVER['argv'] = ['scriptName', '-unknownArgument'])
                ->then
                    ->exception(function () use ($parser, $script) {
                        $parser->parse($script);
                    })
                        ->isInstanceOf(atoum\exceptions\runtime\unexpectedValue::class)
                        ->hasMessage('Argument \'-unknownArgument\' is unknown')
                ->if($parser->setDefaultHandler(function ($script, $argument) use (& $defaultHandlerScript, & $defaultHandlerArgument) {
                    $defaultHandlerScript = $script;
                    $defaultHandlerArgument = $argument;

                    return ($argument == '-unknownArgument');
                }))
                ->then
                    ->object($parser->parse($script))->isIdenticalTo($parser)
                    ->object($defaultHandlerScript)->isIdenticalTo($script)
                    ->string($defaultHandlerArgument)->isEqualTo('-unknownArgument')
                    ->boolean($parser->hasFoundArguments())->isTrue()
            ->assert('when using argument')
                ->if($superglobals->_SERVER['argv'] = [])
                ->then
                    ->object($parser->parse($script, []))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEmpty()
                    ->integer($invokeA)->isEqualTo(8)
                    ->integer($invokeB)->isEqualTo(4)
                    ->integer($invokeC)->isEqualTo(1)
                    ->boolean($parser->hasFoundArguments())->isFalse()
                    ->object($parser->parse($script, ['-a']))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-a' => []])
                    ->integer($invokeA)->isEqualTo(9)
                    ->integer($invokeB)->isEqualTo(4)
                    ->integer($invokeC)->isEqualTo(1)
                    ->boolean($parser->hasFoundArguments())->isTrue()
                    ->object($parser->parse($script, ['-a', '-a']))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-a' => []])
                    ->integer($invokeA)->isEqualTo(10)
                    ->integer($invokeB)->isEqualTo(4)
                    ->integer($invokeC)->isEqualTo(1)
                    ->boolean($parser->hasFoundArguments())->isTrue()
                    ->object($parser->parse($script, ['-a', '-b']))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-a' => [], '-b' => []])
                    ->integer($invokeA)->isEqualTo(11)
                    ->integer($invokeB)->isEqualTo(5)
                    ->integer($invokeC)->isEqualTo(1)
                    ->boolean($parser->hasFoundArguments())->isTrue()
                    ->object($parser->parse($script, ['-a', 'a1']))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-a' => ['a1']])
                    ->integer($invokeA)->isEqualTo(12)
                    ->integer($invokeB)->isEqualTo(5)
                    ->integer($invokeC)->isEqualTo(1)
                    ->boolean($parser->hasFoundArguments())->isTrue()
                    ->object($parser->parse($script, ['-a', 'a1', 'a2']))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-a' => ['a1', 'a2']])
                    ->integer($invokeA)->isEqualTo(13)
                    ->integer($invokeB)->isEqualTo(5)
                    ->integer($invokeC)->isEqualTo(1)
                    ->boolean($parser->hasFoundArguments())->isTrue()
                    ->object($parser->parse($script, ['-a', 'a1', 'a2', '-b']))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-a' => ['a1', 'a2'], '-b' => []])
                    ->integer($invokeA)->isEqualTo(14)
                    ->integer($invokeB)->isEqualTo(6)
                    ->integer($invokeC)->isEqualTo(1)
                    ->boolean($parser->hasFoundArguments())->isTrue()
                    ->object($parser->parse($script, ['-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3']))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-a' => ['a1', 'a2'], '-b' => ['b1', 'b2', 'b3']])
                    ->integer($invokeA)->isEqualTo(15)
                    ->integer($invokeB)->isEqualTo(7)
                    ->integer($invokeC)->isEqualTo(1)
                    ->boolean($parser->hasFoundArguments())->isTrue()
                    ->object($parser->parse($script, ['-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3', '--c']))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-a' => ['a1', 'a2'], '-b' => ['b1', 'b2', 'b3'], '--c' => []])
                    ->integer($invokeA)->isEqualTo(16)
                    ->integer($invokeB)->isEqualTo(8)
                    ->integer($invokeC)->isEqualTo(2)
                    ->boolean($parser->hasFoundArguments())->isTrue()
                    ->exception(function () use ($parser, $script) {
                        $parser->parse($script, ['b']);
                    })
                        ->isInstanceOf(atoum\exceptions\runtime\unexpectedValue::class)
                        ->hasMessage('Argument \'b\' is unknown')
                ->if($superglobals->_SERVER['argv'] = ['scriptName', '-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3', '-d', 'd1', 'd2', '--c'])
                ->and($parser->addHandler(function ($script, $argument, $value) {
                }, ['-d'], PHP_INT_MAX))
                ->then
                    ->object($parser->parse($script))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-d' => ['d1', 'd2'], '-a' => ['a1', 'a2'], '-b' => ['b1', 'b2', 'b3'], '--c' => []])
                    ->boolean($parser->hasFoundArguments())->isTrue()
                ->if($superglobals->_SERVER['argv'] = ['scriptName', '-d', 'd1', 'd2', '-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3', '--c'])
                ->then
                    ->object($parser->parse($script))->isIdenticalTo($parser)
                    ->array($parser->getValues())->isEqualTo(['-d' => ['d1', 'd2'], '-a' => ['a1', 'a2'], '-b' => ['b1', 'b2', 'b3'], '--c' => []])
                    ->boolean($parser->hasFoundArguments())->isTrue()
        ;
    }

    public function testIsArgument()
    {
        $this
            ->boolean(script\arguments\parser::isArgument(uniqid()))->isFalse()
            ->boolean(script\arguments\parser::isArgument('+' . rand(0, 9)))->isFalse()
            ->boolean(script\arguments\parser::isArgument('-' . rand(0, 9)))->isFalse()
            ->boolean(script\arguments\parser::isArgument('--' . rand(0, 9)))->isFalse()
            ->boolean(script\arguments\parser::isArgument('+_'))->isFalse()
            ->boolean(script\arguments\parser::isArgument('-_'))->isFalse()
            ->boolean(script\arguments\parser::isArgument('--_'))->isFalse()
            ->boolean(script\arguments\parser::isArgument('+-'))->isFalse()
            ->boolean(script\arguments\parser::isArgument('---'))->isFalse()
            ->boolean(script\arguments\parser::isArgument('+a'))->isTrue()
            ->boolean(script\arguments\parser::isArgument('-a'))->isTrue()
            ->boolean(script\arguments\parser::isArgument('--a'))->isTrue()
            ->boolean(script\arguments\parser::isArgument('+a' . rand(0, 9)))->isTrue()
            ->boolean(script\arguments\parser::isArgument('-a' . rand(0, 9)))->isTrue()
            ->boolean(script\arguments\parser::isArgument('--a' . rand(0, 9)))->isTrue()
            ->boolean(script\arguments\parser::isArgument('+aa'))->isTrue()
            ->boolean(script\arguments\parser::isArgument('++aa'))->isTrue()
            ->boolean(script\arguments\parser::isArgument('++-aa'))->isFalse()
            ->boolean(script\arguments\parser::isArgument('+++aa'))->isTrue()
            ->boolean(script\arguments\parser::isArgument('-aa'))->isTrue()
            ->boolean(script\arguments\parser::isArgument('--aa'))->isTrue()
            ->boolean(script\arguments\parser::isArgument('---aa'))->isTrue()
            ->boolean(script\arguments\parser::isArgument('--+aa'))->isFalse()
        ;
    }

    public function testAddHandler()
    {
        $this
            ->if($parser = new script\arguments\parser())
            ->then
                ->object($parser->addHandler($handler = function ($script, $argument, $values) {
                }, $arguments = [$argument = '-a']))->isIdenticalTo($parser)
                ->array($parser->getHandlers())->isEqualTo([$argument => [$handler]])
                ->array($parser->getPriorities())->isEqualTo([$argument => 0])
                ->object($parser->addHandler($handler, $arguments))->isIdenticalTo($parser)
                ->array($parser->getHandlers())->isEqualTo([$argument => [$handler, $handler]])
                ->array($parser->getPriorities())->isEqualTo([$argument => 0])
                ->exception(function () use ($parser) {
                    $parser->addHandler(function () {
                    }, $argument = ['-b']);
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Handler must take three arguments')
                ->exception(function () use ($parser) {
                    $parser->addHandler(function ($script) {
                    }, ['-b']);
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Handler must take three arguments')
                ->exception(function () use ($parser) {
                    $parser->addHandler(function ($script, $argument) {
                    }, ['-b']);
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Handler must take three arguments')
                ->exception(function () use ($parser, & $badArgument) {
                    $parser->addHandler(function ($script, $argument, $values) {
                    }, [$badArgument = 'b']);
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Argument \'' . $badArgument . '\' is invalid')
                ->object($parser->addHandler($otherHandler = function ($script, $argument, $values) {
                }, [$otherArgument = '-b'], $priority = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($parser)
                ->array($parser->getHandlers())->isEqualTo([$argument => [$handler, $handler], $otherArgument => [$otherHandler]])
                ->array($parser->getPriorities())->isEqualTo([$argument => 0, $otherArgument => $priority])
        ;
    }

    public function testSetDefaultHandler()
    {
        $this
            ->if($parser = new script\arguments\parser())
            ->then
                ->object($parser->setDefaultHandler($defaultHandler = function ($script, $argument) {
                }))->isIdenticalTo($parser)
                ->object($parser->getDefaultHandler())->isIdenticalTo($defaultHandler)
                ->exception(function () use ($parser) {
                    $parser->setDefaultHandler(function () {
                    });
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Handler must take two arguments')
                ->exception(function () use ($parser) {
                    $parser->setDefaultHandler(function ($script) {
                    });
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Handler must take two arguments')
        ;
    }

    public function testArgumentHasHandler()
    {
        $this
            ->if($parser = new script\arguments\parser())
            ->then
                ->boolean($parser->argumentHasHandler('-' . uniqid()))->isFalse()
                ->boolean($parser->argumentHasHandler('--' . uniqid()))->isFalse()
                ->boolean($parser->argumentHasHandler(uniqid()))->isFalse()
            ->if($parser->addHandler(function ($script, $argument, $values) {
            }, ['--a-long-argument', '-a']))
            ->then
                ->boolean($parser->argumentHasHandler('-' . uniqid()))->isFalse()
                ->boolean($parser->argumentHasHandler('--' . uniqid()))->isFalse()
                ->boolean($parser->argumentHasHandler(uniqid()))->isFalse()
                ->boolean($parser->argumentHasHandler('-a'))->isTrue()
                ->boolean($parser->argumentHasHandler('--a-long-argument'))->isTrue()
            ->if($parser->setDefaultHandler(function ($script, $argument) {
            }))
            ->then
                ->boolean($parser->argumentHasHandler('-' . uniqid()))->isFalse()
                ->boolean($parser->argumentHasHandler('--' . uniqid()))->isFalse()
                ->boolean($parser->argumentHasHandler(uniqid()))->isFalse()
                ->boolean($parser->argumentHasHandler('-a'))->isTrue()
                ->boolean($parser->argumentHasHandler('--a-long-argument'))->isTrue()
        ;
    }

    public function testArgumentIsHandled()
    {
        $this
            ->if($parser = new script\arguments\parser())
            ->then
                ->boolean($parser->argumentIsHandled('-' . uniqid()))->isFalse()
                ->boolean($parser->argumentIsHandled('--' . uniqid()))->isFalse()
                ->boolean($parser->argumentIsHandled(uniqid()))->isFalse()
            ->if($parser->addHandler(function ($script, $argument, $values) {
            }, ['--a-long-argument', '-a']))
            ->then
                ->boolean($parser->argumentIsHandled('-' . uniqid()))->isFalse()
                ->boolean($parser->argumentIsHandled('--' . uniqid()))->isFalse()
                ->boolean($parser->argumentIsHandled(uniqid()))->isFalse()
                ->boolean($parser->argumentIsHandled('-a'))->isTrue()
                ->boolean($parser->argumentIsHandled('--a-long-argument'))->isTrue()
            ->if($parser->setDefaultHandler(function ($script, $argument) {
            }))
            ->then
                ->boolean($parser->argumentIsHandled('-' . uniqid()))->isTrue()
                ->boolean($parser->argumentIsHandled('--' . uniqid()))->isTrue()
                ->boolean($parser->argumentIsHandled(uniqid()))->isTrue()
                ->boolean($parser->argumentIsHandled('-a'))->isTrue()
                ->boolean($parser->argumentIsHandled('--a-long-argument'))->isTrue()
        ;
    }

    public function testResetHandlers()
    {
        $this
            ->if($parser = new script\arguments\parser())
            ->then
                ->object($parser->resetHandlers())->isIdenticalTo($parser)
                ->array($parser->getHandlers())->isEmpty()
                ->variable($parser->getDefaultHandler())->isNull()
                ->array($parser->getPriorities())->isEmpty()
            ->if($parser->addHandler(function ($script, $argument, $values) {
            }, ['-a']))
            ->and($parser->setDefaultHandler(function ($script, $argument) {
            }))
            ->then
                ->object($parser->resetHandlers())->isIdenticalTo($parser)
                ->array($parser->getHandlers())->isEmpty()
                ->variable($parser->getDefaultHandler())->isNull()
                ->array($parser->getPriorities())->isEmpty()
        ;
    }

    public function testGetClosestArgument()
    {
        $this
            ->given(
                $this->newTestedInstance,
                $min = null
            )
            ->if($this->testedInstance->addHandler(function ($script, $argument, $values) {
            }, ['--an-argument']))
            ->then
                ->variable($this->testedInstance->getClosestArgument(uniqid('--'), $min))->isNull
                ->variable($min)->isNull
            ->given($min = null)
            ->then
                ->string($this->testedInstance->getClosestArgument('--a-argument', $min))->isEqualTo('--an-argument')
                ->integer($min)->isGreaterThan(0)
            ->given(
                $this->newTestedInstance,
                $min = null
            )
            ->if(
                $this->testedInstance->addHandler(function ($script, $argument, $values) {
                }, ['--leave']),
                $this->testedInstance->addHandler(function ($script, $argument, $values) {
                }, ['--live'])
            )
            ->then
                ->variable($this->testedInstance->getClosestArgument('--leeve', $min))->isEqualTo('--leave')
                ->integer($min)->isZero
            ->given(
                $this->newTestedInstance,
                $min = null
            )
            ->if(
                $this->testedInstance->addHandler(function ($script, $argument, $values) {
                }, ['--live'], 2),
                $this->testedInstance->addHandler(function ($script, $argument, $values) {
                }, ['--leave'], 1)
            )
            ->then
                ->variable($this->testedInstance->getClosestArgument('--leeve', $min))->isEqualTo('--leave')
                ->integer($min)->isZero
        ;
    }
}
