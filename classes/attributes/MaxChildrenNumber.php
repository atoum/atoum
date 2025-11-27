<?php

namespace atoum\atoum\attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class MaxChildrenNumber
{
    public int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }
}
