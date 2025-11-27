<?php

namespace atoum\atoum\attributes;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class DataProvider
{
    public ?string $value;

    public function __construct(?string $value = null)
    {
        $this->value = $value;
    }
}
