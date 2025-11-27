<?php

namespace atoum\atoum\adapter;

interface definition
{
    public function __call(string $functionName, array $arguments): mixed;

    public function invoke(string $functionName, array $arguments = []): mixed;
}
