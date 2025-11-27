<?php

namespace atoum\atoum\test\assertion;

use atoum\atoum\test\assertion;

class manager
{
    protected ?assertion\aliaser $aliaser = null;
    protected array $propertyHandlers = [];
    protected array $methodHandlers = [];
    protected ?\Closure $defaultHandler = null;

    public function __construct(?assertion\aliaser $aliaser = null)
    {
        $this->setAliaser($aliaser);
    }

    public function __set(string $event, \Closure $handler): void
    {
        $this->setHandler($event, $handler);
    }

    public function __get(string $event): mixed
    {
        return $this->invokePropertyHandler($event);
    }

    public function __call(string $event, array $arguments): mixed
    {
        return $this->invokeMethodHandler($event, $arguments);
    }

    public function setAliaser(?assertion\aliaser $aliaser = null): static
    {
        $this->aliaser = $aliaser ?: new assertion\aliaser();

        return $this;
    }

    public function getAliaser(): assertion\aliaser
    {
        return $this->aliaser;
    }

    public function setAlias(string $alias, string $keyword): static
    {
        $this->aliaser->aliasKeyword($keyword, $alias);

        return $this;
    }

    public function setMethodHandler(string $event, \Closure $handler): static
    {
        return $this->setHandlerIn($this->methodHandlers, $event, $handler);
    }

    public function setPropertyHandler(string $event, \Closure $handler): static
    {
        return $this->setHandlerIn($this->propertyHandlers, $event, $handler);
    }

    public function setHandler(string $event, \Closure $handler): static
    {
        return $this
            ->setPropertyHandler($event, $handler)
            ->setMethodHandler($event, $handler)
        ;
    }

    public function setDefaultHandler(\Closure $handler): static
    {
        $this->defaultHandler = $handler;

        return $this;
    }

    public function invokePropertyHandler(string $event): mixed
    {
        return $this->invokeHandlerFrom($this->propertyHandlers, $event);
    }

    public function invokeMethodHandler($event, array $arguments = [])
    {
        return $this->invokeHandlerFrom($this->methodHandlers, $event, $arguments);
    }

    private function setHandlerIn(array & $handlers, $event, \Closure $handler)
    {
        $handlers[strtolower($event)] = $handler;

        return $this;
    }

    private function invokeHandlerFrom(array $handlers, $event, array $arguments = [])
    {
        $handler = null;

        $realEvent = strtolower($event);

        if (isset($handlers[$realEvent]) === false) {
            $realEvent = $this->aliaser->resolveAlias($event);
        }

        if (isset($handlers[$realEvent]) === true) {
            $handler = $handlers[$realEvent];
        }

        switch (true) {
            case $handler === null && $this->defaultHandler === null:
                throw new assertion\manager\exception('There is no handler defined for \'' . $event . '\'');

            case $handler !== null:
                return call_user_func_array($handler, $arguments);

            default:
                return call_user_func_array($this->defaultHandler, [$realEvent, $arguments]);
        }
    }
}
