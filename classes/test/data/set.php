<?php

namespace atoum\atoum\test\data;

class set extends provider\aggregator
{
    protected provider $provider;
    protected int $size;

    public function __construct(provider $provider, ?int $size = null)
    {
        $this->provider = $provider;
        $this->size = $size ?: 1;
    }

    public function __invoke(): array
    {
        return $this->generate();
    }

    public function __toString(): string
    {
        return $this->provider->__toString();
    }

    public function generate(): array
    {
        $provider = $this->provider;

        return array_map(
            function () use ($provider) {
                return $provider->generate();
            },
            range(0, $this->size - 1)
        );
    }

    public function count(): int
    {
        return $this->size;
    }
}
