<?php

namespace mageekguy\atoum\tests\units\php\mocker;

require_once __DIR__ . '/../../../runner.php';

use mageekguy\atoum;
use mageekguy\atoum\php;

class constant extends atoum\test
{
    public function test__set()
    {
        $this
            ->given(
                $this->newTestedInstance,
                $adapter = new atoum\test\adapter(),
                php\mocker\constant::setAdapter($adapter)
            )

            ->if(
                $adapter->define = true,
                $this->testedInstance->setDefaultNameSpace($namespace = uniqid())
            )
            ->then
                ->string($this->testedInstance->{$constant = uniqid()} = $value = uniqid())->isEqualTo($value)
                ->adapter($adapter)
                    ->call('define')->withArguments($namespace . '\\' . $constant, $value)->once

            ->if($adapter->define = false)
            ->then
                ->exception(function () use (& $constant, & $value) {
                    $this->testedInstance->{$constant = uniqid('a')} = $value = uniqid();
                })
                    ->isInstanceOf(atoum\php\mocker\exceptions\constant::class)
                    ->hasMessage('Could not mock constant \'' . $constant . '\' in namespace \'' . $namespace . '\'')
                ->adapter($adapter)
                    ->call('define')->withArguments($namespace . '\\' . $constant, $value)->once
        ;
    }

    public function test__get()
    {
        $this
            ->given(
                $this->newTestedInstance,
                $adapter = new atoum\test\adapter(),
                php\mocker\constant::setAdapter($adapter)
            )

            ->if(
                $adapter->defined = false,
                $this->testedInstance->setDefaultNameSpace($namespace = uniqid())
            )
            ->then
                ->exception(function () use (& $constant) {
                    $this->testedInstance->{$constant = uniqid()};
                })
                    ->isInstanceOf(atoum\php\mocker\exceptions\constant::class)
                    ->hasMessage('Constant \'' . $constant . '\' is not defined in namespace \'' . $namespace . '\'')
                ->adapter($adapter)
                    ->call('defined')->withArguments($namespace . '\\' . $constant)->once

            ->if(
                $adapter->defined = true,
                $adapter->constant = $value = uniqid()
            )
            ->then
                ->string($this->testedInstance->{$constant = uniqid()})->isEqualTo($value)
                ->adapter($adapter)
                    ->call('defined')->withArguments($namespace . '\\' . $constant)->once
                    ->call('constant')->withArguments($namespace . '\\' . $constant)->once
        ;
    }

    public function test__isset()
    {
        $this
            ->given(
                $this->newTestedInstance,
                $adapter = new atoum\test\adapter(),
                php\mocker\constant::setAdapter($adapter)
            )

            ->if(
                $adapter->defined = false,
                $this->testedInstance->setDefaultNameSpace($namespace = uniqid())
            )
            ->then
                ->boolean(isset($this->testedInstance->{$constant = uniqid()}))->isFalse
                ->adapter($adapter)
                    ->call('defined')->withArguments($namespace . '\\' . $constant)->once

            ->if($adapter->defined = true)
            ->then
                ->boolean(isset($this->testedInstance->{$constant = uniqid()}))->isTrue
                ->adapter($adapter)
                    ->call('defined')->withArguments($namespace . '\\' . $constant)->once
        ;
    }

    public function test__unset()
    {
        $this
            ->given(
                $this->newTestedInstance,
                $adapter = new atoum\test\adapter()
            )

            ->if($this->testedInstance->setDefaultNameSpace($namespace = uniqid()))
            ->then
                ->exception(function () use (& $constant, & $value) {
                    unset($this->testedInstance->{$constant = uniqid()});
                })
                    ->isInstanceOf(atoum\php\mocker\exceptions\constant::class)
                    ->hasMessage('Could not unset constant \'' . $constant . '\' in namespace \'' . $namespace . '\'')
        ;
    }
}
