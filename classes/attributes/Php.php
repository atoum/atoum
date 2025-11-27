<?php

namespace atoum\atoum\attributes;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Php
{
    public string $version;
    public string $operator;

    public function __construct(string $version, string $operator = '>=')
    {
        $this->version = $version;
        $this->operator = $operator;
    }
}
