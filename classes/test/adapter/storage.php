<?php

namespace atoum\atoum\test\adapter;

use atoum\atoum\test\adapter;

class storage implements \countable, \iteratorAggregate
{
    protected $adapters = null;

    public function __construct()
    {
        $this->reset();
    }

    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->adapters);
    }

    public function add(adapter $adapter)
    {
        if ($this->contains($adapter) === false) {
            $this->adapters->offsetSet($adapter);
        }

        return $this;
    }

    public function contains(adapter $adapter)
    {
        return $this->adapters->offsetExists($adapter);
    }

    public function reset()
    {
        $this->adapters = new \splObjectStorage();

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        $adapters = [];

        foreach ($this->adapters as $instance) {
            $adapters[] = $instance;
        }

        return new \arrayIterator($adapters);
    }

    public function resetCalls()
    {
        foreach ($this->adapters as $adapter) {
            $adapter->resetCalls();
        }

        return $this;
    }
}
