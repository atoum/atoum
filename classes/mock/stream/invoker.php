<?php

namespace atoum\atoum\mock\stream;

use atoum\atoum\test\adapter;

class invoker extends adapter\invoker
{
    protected $methodName = '';

    public function __construct($methodName)
    {
        $this->methodName = strtolower($methodName);
    }

    public function getMethodName()
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
