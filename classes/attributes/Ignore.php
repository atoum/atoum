<?php

namespace atoum\atoum\attributes;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Ignore
{
    public bool $value;

    public function __construct(bool $value = true)
    {
        $this->value = $value;
    }
}
