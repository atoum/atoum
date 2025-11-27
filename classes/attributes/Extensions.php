<?php

namespace atoum\atoum\attributes;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Extensions
{
    /** @var string[] */
    public array $extensions;

    public function __construct(string ...$extensions)
    {
        $this->extensions = $extensions;
    }
}
