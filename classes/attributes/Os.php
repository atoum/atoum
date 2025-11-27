<?php

namespace atoum\atoum\attributes;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Os
{
    /** @var string[] */
    public array $os;

    public function __construct(string ...$os)
    {
        $this->os = $os;
    }
}
