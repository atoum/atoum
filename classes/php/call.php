<?php

namespace atoum\atoum\php;

use atoum\atoum\test\adapter;

class call
{
    protected string $function = '';
    protected ?array $arguments = null;
    protected bool $identical = false;
    protected mixed $object = null;
    protected ?adapter\call\decorator $decorator = null;

    public function __construct(string $function, ?array $arguments = null, mixed $object = null)
    {
        $this->function = (string) $function;
        $this->arguments = $arguments;
        $this->object = $object;

        $this->setDecorator();
    }

    public function __toString(): string
    {
        return $this->decorator->decorate($this);
    }

    public function identical(): static
    {
        $this->identical = true;

        return $this;
    }

    public function notIdentical(): static
    {
        $this->identical = false;

        return $this;
    }

    public function isIdentical(): bool
    {
        return ($this->identical === true);
    }

    public function setFunction(string $function): static
    {
        $this->function = $function;

        return $this;
    }

    public function getFunction(): string
    {
        return $this->function;
    }

    public function setArguments(array $arguments): static
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function getArguments(): ?array
    {
        return $this->arguments;
    }

    public function unsetArguments(): static
    {
        $this->arguments = null;

        return $this;
    }

    public function setObject(mixed $object): static
    {
        $this->object = $object;

        return $this;
    }

    public function getObject(): mixed
    {
        return $this->object;
    }

    public function setDecorator(?adapter\call\decorator $decorator = null): static
    {
        $this->decorator = $decorator ?: new adapter\call\decorator();

        return $this;
    }

    public function getDecorator(): adapter\call\decorator
    {
        return $this->decorator;
    }
}
