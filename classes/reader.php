<?php

namespace atoum\atoum;

abstract class reader
{
    protected ?adapter $adapter = null;

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

    abstract public function read(?int $length = null): string|false;
}
