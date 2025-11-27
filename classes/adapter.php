<?php

namespace atoum\atoum;

class adapter implements adapter\definition
{
    public function __call(string $functionName, array $arguments): mixed
    {
        return $this->invoke($functionName, $arguments);
    }

    public function invoke(string $functionName, array $arguments = []): mixed
    {
        return call_user_func_array($functionName, $arguments);
    }
}
