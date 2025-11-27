<?php

namespace atoum\atoum\attributes;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Engine
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
