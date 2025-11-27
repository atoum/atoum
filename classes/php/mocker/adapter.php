<?php

namespace atoum\atoum\php\mocker;

use atoum\atoum\php\mocker;
use atoum\atoum\test;

class adapter extends test\adapter
{
    protected function setInvoker(string $functionName, ?\Closure $factory = null): test\adapter\invoker
    {
        if ($factory === null) {
            $factory = function ($functionName) {
                return new mocker\adapter\invoker($functionName);
            };
        }

        return parent::setInvoker($functionName, $factory);
    }
}
