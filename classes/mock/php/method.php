<?php

namespace atoum\atoum\mock\php;

use atoum\atoum\exceptions;

class method
{
    protected bool $returnReference = false;
    protected string $name = '';
    protected bool $isConstructor = false;
    protected array $arguments = [];

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->isConstructor = ($name == __FUNCTION__);
    }

    public function __toString(): string
    {
        $string = 'public function ';

        if ($this->returnReference === true) {
            $string .= '& ';
        }

        $string .= $this->name . '(' . $this->getArgumentsAsString() . ')';

        return $string;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isConstructor(): bool
    {
        return $this->isConstructor;
    }

    public function returnReference(): static
    {
        if ($this->isConstructor === true) {
            throw new exceptions\logic('Constructor can not return a reference');
        }

        $this->returnReference = true;

        return $this;
    }

    public function addArgument(method\argument $argument): static
    {
        $this->arguments[] = $argument;

        return $this;
    }

    public function getArgumentsAsString(): string
    {
        $arguments = $this->arguments;

        array_walk($arguments, function (& $value) {
            $value = (string) $value;
        });

        return implode(', ', $arguments);
    }

    public static function get(string $name): static
    {
        return new static($name);
    }
}
