<?php

namespace atoum\atoum\attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class TestMethodPrefix
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
