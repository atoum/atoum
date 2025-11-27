<?php

namespace atoum\atoum\mock;

use atoum\atoum\exceptions;
use atoum\atoum\mock;
use atoum\atoum\test;
use atoum\atoum\test\adapter\call\decorators;

class controller extends test\adapter
{
    protected ?string $mockClass = null;
    protected array $mockMethods = [];
    protected ?controller\iterator $iterator = null;
    protected bool $autoBind = true;

    protected static ?controller\linker $linker = null;
    protected static ?self $controlNextNewMock = null;
    protected static bool $autoBindForNewMock = true;

    private bool $disableMethodChecking = false;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setIterator()
            ->controlNextNewMock()
        ;

        if (self::$autoBindForNewMock === true) {
            $this->enableAutoBind();
        } else {
            $this->disableAutoBind();
        }
    }

    public function __set(string $method, mixed $mixed): void
    {
        $this->checkMethod($method);

        parent::__set($method, $mixed);
    }

    public function __get(string $method): test\adapter\invoker
    {
        $this->checkMethod($method);

        return parent::__get($method);
    }

    public function __isset(string $method): bool
    {
        $this->checkMethod($method);

        return parent::__isset($method);
    }

    public function __unset(string $method): void
    {
        $this->checkMethod($method);

        parent::__unset($method);
    }

    public function setIterator(?controller\iterator $iterator = null): static
    {
        $this->iterator = $iterator ?: new controller\iterator();

        $this->iterator->setMockController($this);

        return $this;
    }

    public function getIterator(): controller\iterator
    {
        return $this->iterator;
    }

    public function disableMethodChecking(): static
    {
        $this->disableMethodChecking = true;

        return $this;
    }

    public function getMockClass(): ?string
    {
        return $this->mockClass;
    }

    public function getMethods(): array
    {
        return $this->mockMethods;
    }

    public function methods(?\Closure $filter = null): controller\iterator
    {
        $this->iterator->resetFilters();

        if ($filter !== null) {
            $this->iterator->addFilter($filter);
        }

        return $this->iterator;
    }

    public function methodsMatching(string $regex): controller\iterator
    {
        return $this->iterator->resetFilters()->addFilter(function ($name) use ($regex) {
            return preg_match($regex, $name);
        });
    }

    public function getCalls(?test\adapter\call $call = null, bool $identical = false): test\adapter\calls
    {
        if ($call !== null) {
            $this->checkMethod($call->getFunction());
        }

        return parent::getCalls($call, $identical);
    }

    public function control(mock\aggregator $mock): static
    {
        $currentMockController = self::$linker->getController($mock);

        if ($currentMockController !== null && $currentMockController !== $this) {
            $currentMockController->reset();
        }

        if ($currentMockController === null || $currentMockController !== $this) {
            self::$linker->link($this, $mock);
        }

        $this->mockClass = get_class($mock);
        $this->mockMethods = $mock->getMockedMethods();

        foreach (array_keys($this->invokers) as $method) {
            $this->checkMethod($method);
        }

        foreach ($this->mockMethods as $method) {
            $this->{$method}->setMock($mock);

            if ($this->autoBind === true) {
                $this->{$method}->bindTo($mock);
            }
        }

        return $this
            ->resetCalls()
            ->notControlNextNewMock()
        ;
    }

    public function controlNextNewMock(): static
    {
        self::$controlNextNewMock = $this;

        return $this;
    }

    public function notControlNextNewMock(): static
    {
        if (self::$controlNextNewMock === $this) {
            self::$controlNextNewMock = null;
        }

        return $this;
    }

    public function enableAutoBind(): static
    {
        $this->autoBind = true;

        foreach ($this->invokers as $invoker) {
            $invoker->bindTo($this->getMock());
        }

        return $this;
    }

    public function disableAutoBind(): static
    {
        $this->autoBind = false;

        return $this->reset();
    }

    public function autoBindIsEnabled(): bool
    {
        return ($this->autoBind === true);
    }

    public function reset(): static
    {
        self::$linker->unlink($this);

        $this->mockClass = null;
        $this->mockMethods = [];

        return parent::reset();
    }

    public function getMock(): ?mock\aggregator
    {
        return self::$linker->getMock($this);
    }

    public function invoke(string $method, array $arguments = []): mixed
    {
        $this->checkMethod($method);

        if (isset($this->{$method}) === false) {
            throw new exceptions\logic('Method ' . $method . '() is not under control');
        }

        return parent::invoke($method, $arguments);
    }

    public static function enableAutoBindForNewMock(): void
    {
        self::$autoBindForNewMock = true;
    }

    public static function disableAutoBindForNewMock(): void
    {
        self::$autoBindForNewMock = false;
    }

    public static function get(bool $unset = true): ?self
    {
        $instance = self::$controlNextNewMock;

        if ($instance !== null && $unset === true) {
            self::$controlNextNewMock = null;
        }

        return $instance;
    }

    public static function setLinker(?controller\linker $linker = null): void
    {
        self::$linker = $linker ?: new controller\linker();
    }

    public static function getForMock(aggregator $mock): ?self
    {
        return self::$linker->getController($mock);
    }

    protected function checkMethod(string $method): static
    {
        if ($this->mockClass !== null && $this->disableMethodChecking === false && in_array(strtolower($method), $this->mockMethods) === false) {
            if (in_array('__call', $this->mockMethods) === false) {
                throw new exceptions\logic('Method \'' . $this->getMockClass() . '::' . $method . '()\' does not exist');
            }

            if (isset($this->__call) === false) {
                $controller = $this;

                parent::__set('__call', function ($method, $arguments) use ($controller) {
                    return $controller->invoke($method, $arguments);
                });
            }
        }

        return $this;
    }

    protected function buildInvoker(string $methodName, ?\Closure $factory = null): mock\controller\invoker
    {
        if ($factory === null) {
            $factory = function ($methodName, $mock) {
                return new mock\controller\invoker($methodName, $mock);
            };
        }

        return $factory($methodName, $this->getMock());
    }

    protected function setInvoker(string $methodName, ?\Closure $factory = null): mock\controller\invoker
    {
        $invoker = parent::setInvoker($methodName, $factory);

        $mock = $this->getMock();

        if ($mock !== null) {
            $invoker->setMock($this->getMock());
        }

        if ($this->autoBind === true) {
            $invoker->bindTo($mock);
        }

        return $invoker;
    }

    protected function buildCall(string $function, array $arguments): test\adapter\call
    {
        $call = parent::buildCall($function, $arguments);

        if ($this->mockClass !== null) {
            $call->setDecorator(new decorators\addClass($this->mockClass));
        }

        return $call;
    }
}

controller::setLinker();
