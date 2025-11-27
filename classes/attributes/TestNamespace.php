<?php

namespace atoum\atoum\attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class TestNamespace
{
    public ?string $value;

    public function __construct(?string $value = null)
    {
        $this->value = $value;
    }
}
