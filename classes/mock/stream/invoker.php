<?php

namespace atoum\atoum\mock\stream;

use atoum\atoum\test\adapter;

class invoker extends adapter\invoker
{
    protected string $methodName = '';

    public function __construct(string $methodName)
    {
        $this->methodName = strtolower($methodName);
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($call = null, $mixed = null)
    {
        if ($this->methodName == 'dir_readdir' && $mixed instanceof \atoum\atoum\mock\stream\controller) {
            $mixed = $mixed->getBasename();
        }

        parent::offsetSet($call, $mixed);
    }
}
