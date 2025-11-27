<?php

namespace atoum\atoum;

use atoum\atoum\writer\decorator;

abstract class writer
{
    protected ?adapter $adapter = null;
    protected array $decorators = [];

    public function __construct(?adapter $adapter = null)
    {
        $this->setAdapter($adapter);
    }

    public function setAdapter(?adapter $adapter = null): static
    {
        $this->adapter = $adapter ?: new adapter();

        return $this;
    }

    public function getAdapter(): adapter
    {
        return $this->adapter;
    }

    public function reset(): static
    {
        return $this;
    }

    public function addDecorator(decorator $decorator): static
    {
        $this->decorators[] = $decorator;

        return $this;
    }

    public function getDecorators(): array
    {
        return $this->decorators;
    }

    public function removeDecorators(): static
    {
        $this->decorators = [];

        return $this;
    }

    public function write(string $string): static
    {
        foreach ($this->decorators as $decorator) {
            $string = $decorator->decorate($string);
        }

        $this->doWrite($string);

        return $this;
    }

    abstract public function clear(): static;

    abstract protected function doWrite(string $string): void;
}
