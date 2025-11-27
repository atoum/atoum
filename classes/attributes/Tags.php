<?php

namespace atoum\atoum\attributes;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Tags
{
    /** @var string[] */
    public array $tags;

    public function __construct(string ...$tags)
    {
        $this->tags = $tags;
    }
}
