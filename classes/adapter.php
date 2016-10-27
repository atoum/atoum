<?php

namespace mageekguy\atoum;

class adapter implements adapter\definition
{
    public function __call($functionName, $arguments)
    {
        return $this->invoke($functionName, $arguments);
    }

    public function invoke($functionName, array $arguments = [])
    {
        return call_user_func_array($functionName, $arguments);
    }
}
