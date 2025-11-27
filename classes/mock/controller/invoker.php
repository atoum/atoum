<?php

namespace atoum\atoum\mock\controller;

use atoum\atoum\mock;
use atoum\atoum\test\adapter;

class invoker extends adapter\invoker
{
    protected ?mock\aggregator $mock = null;

    public function __construct(string $method, ?mock\aggregator $mock = null)
    {
        parent::__construct($method);

        $this->mock = $mock;
    }

    public function __get(string $property): mixed
    {
        switch (strtolower($property)) {
            case 'isfluent':
                return $this->isFluent();

            default:
                return parent::__get($property);
        }
    }

    public function setMock(mock\aggregator $mock): static
    {
        $this->mock = $mock;

        return $this;
    }

    public function getMock(): ?mock\aggregator
    {
        return $this->mock;
    }

    public function isFluent(): static
    {
        $mock = $this->mock;

        return $this->setClosure(function () use ($mock) {
            return $mock;
        });
    }
}
