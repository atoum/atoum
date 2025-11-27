<?php

namespace atoum\atoum\test;

use atoum\atoum;
use atoum\atoum\exceptions;
use atoum\atoum\test\adapter\invoker;

class adapter extends atoum\adapter
{
    protected ?adapter\calls $calls = null;
    protected array $invokers = [];

    private static ?adapter\storage $storage = null;

    public function __construct()
    {
        $this->setCalls();

        if (self::$storage !== null) {
            self::$storage->add($this);
        }
    }

    public function __clone()
    {
        $this->calls = clone $this->calls;

        if (self::$storage !== null) {
            self::$storage->add($this);
        }
    }

    public function __set(string $functionName, mixed $mixed): void
    {
        $this->{$functionName}->return = $mixed;
    }

    public function __get(string $functionName): adapter\invoker
    {
        return $this->setInvoker($functionName);
    }

    public function __isset(string $functionName): bool
    {
        return $this->nextCallIsOverloaded($functionName);
    }

    public function __unset(string $functionName): void
    {
        if (isset($this->{$functionName}) === true) {
            $functionName = static::getKey($functionName);

            unset($this->invokers[$functionName]);
            unset($this->calls[$functionName]);
        }
    }

    public function __sleep(): array
    {
        return [];
    }

    public function __toString(): string
    {
        return (string) $this->calls;
    }

    public function getInvokers(): array
    {
        return $this->invokers;
    }

    public function setCalls(?adapter\calls $calls = null): static
    {
        $this->calls = $calls ?: new adapter\calls();

        return $this->resetCalls();
    }

    public function getCalls(?adapter\call $call = null, bool $identical = false): adapter\calls
    {
        return ($call === null ? $this->calls : $this->calls->get($call, $identical));
    }

    public function getCallsNumber(?adapter\call $call = null, bool $identical = false): int
    {
        return count($this->getCalls($call, $identical));
    }

    public function getCallsEqualTo(adapter\call $call): adapter\calls
    {
        return $this->calls->getEqualTo($call);
    }

    public function getCallsNumberEqualTo(adapter\call $call): int
    {
        return count($this->calls->getEqualTo($call));
    }

    public function getCallsIdenticalTo(adapter\call $call): adapter\calls
    {
        return $this->calls->getIdenticalTo($call);
    }

    public function getPreviousCalls(adapter\call $call, int $position, bool $identical = false): adapter\calls
    {
        return $this->calls->getPrevious($call, $position, $identical);
    }

    public function hasPreviousCalls(adapter\call $call, int $position, bool $identical = false): bool
    {
        return $this->calls->hasPrevious($call, $position, $identical);
    }

    public function getAfterCalls(adapter\call $call, int $position, bool $identical = false): adapter\calls
    {
        return $this->calls->getAfter($call, $position, $identical);
    }

    public function hasAfterCalls(adapter\call $call, int $position, bool $identical = false): bool
    {
        return $this->calls->hasAfter($call, $position, $identical);
    }

    public function getCallNumber(?adapter\call $call = null, bool $identical = false): int
    {
        return count($this->getCalls($call, $identical));
    }

    public function getTimeline(?adapter\call $call = null, bool $identical = false): array
    {
        return $this->calls->getTimeline($call, $identical);
    }

    public function resetCalls(?string $functionName = null): static
    {
        if ($functionName === null) {
            $this->calls->reset();
        } else {
            unset($this->calls[$functionName]);
        }

        return $this;
    }

    public function reset(): static
    {
        $this->invokers = [];

        return $this->resetCalls();
    }

    public function addCall(string $functionName, array $arguments = []): static
    {
        $unreferencedArguments = [];

        foreach ($arguments as $argument) {
            $unreferencedArguments[] = $argument;
        }

        $this->calls[] = $this->buildCall($functionName, $unreferencedArguments);

        return $this;
    }

    public function invoke(string $functionName, array $arguments = []): mixed
    {
        if (self::isLanguageConstruct($functionName) || (function_exists($functionName) === true && is_callable($functionName) === false)) {
            throw new exceptions\logic\invalidArgument('Function \'' . $functionName . '()\' is not invokable by an adapter');
        }

        $call = count($this->addCall($functionName, $arguments)->getCallsEqualTo(new adapter\call($functionName)));

        try {
            return ($this->callIsOverloaded($functionName, $call) === false ? parent::invoke($functionName, $arguments) : $this->{$functionName}->invoke($arguments, $call));
        } catch (exceptions\logic\invalidArgument $exception) {
            throw new exceptions\logic('There is no return value defined for \'' . $functionName . '() at call ' . $call);
        }
    }

    public static function setStorage(?adapter\storage $storage = null): void
    {
        self::$storage = $storage ?: new adapter\storage();
    }

    protected function buildInvoker(string $functionName, ?\Closure $factory = null): adapter\invoker
    {
        if ($factory === null) {
            $factory = function ($functionName) {
                return new invoker($functionName);
            };
        }

        return $factory($functionName);
    }

    protected function setInvoker(string $functionName, ?\Closure $factory = null): adapter\invoker
    {
        $key = static::getKey($functionName);

        if (isset($this->invokers[$key]) === false) {
            $this->invokers[$key] = $this->buildInvoker($functionName, $factory);
        }

        return $this->invokers[$key];
    }

    protected function callIsOverloaded(string $functionName, int $call): bool
    {
        $functionName = static::getKey($functionName);

        return (isset($this->invokers[$functionName]) === true && $this->invokers[$functionName]->closureIsSetForCall($call) === true);
    }

    protected function nextCallIsOverloaded(string $functionName): bool
    {
        return ($this->callIsOverloaded($functionName, $this->getCallNumber(new adapter\call($functionName)) + 1) === true);
    }

    protected function buildCall(string $function, array $arguments): adapter\call
    {
        return new adapter\call($function, $arguments);
    }

    protected static function getKey(string $functionName): string
    {
        return strtolower($functionName);
    }

    protected static function isLanguageConstruct(string $functionName): bool
    {
        switch (strtolower($functionName)) {
            case 'array':
            case 'declare':
            case 'echo':
            case 'empty':
            case 'eval':
            case 'exit':
            case 'die':
            case 'isset':
            case 'list':
            case 'print':
            case 'unset':
            case 'require':
            case 'require_once':
            case 'include':
            case 'include_once':
                return true;

            default:
                return false;
        }
    }

    protected static function getArgumentsFilter(mixed $arguments, bool $identicalArguments): ?\Closure
    {
        $filter = null;

        if (is_array($arguments) === true) {
            if ($arguments === []) {
                $filter = function ($callArguments) use ($arguments) {
                    return ($arguments === $callArguments);
                };
            } else {
                $callback = function ($a, $b) {
                    return ($a == $b ? 0 : -1);
                };

                if ($identicalArguments === false) {
                    $filter = function ($callArguments) use ($arguments, $callback) {
                        return ($arguments == array_uintersect_uassoc($callArguments, $arguments, $callback, $callback));
                    };
                } else {
                    $filter = function ($callArguments) use ($arguments, $callback) {
                        return ($arguments === array_uintersect_uassoc($callArguments, $arguments, $callback, $callback));
                    };
                }
            }
        }

        return $filter;
    }
}
