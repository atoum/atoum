<?php

namespace atoum\atoum\test\data\provider;

use atoum\atoum\test\data\provider;

class aggregator implements provider, \countable
{
    protected array $providers = [];

    public function __invoke(): array
    {
        return $this->generate();
    }

    public function __toString(): string
    {
        $types = array_map(
            function (provider $provider) {
                return $provider->__toString();
            },
            $this->providers
        );

        return __CLASS__ . '<' . implode(', ', $types) . '>';
    }

    public function generate(): array
    {
        $data = [];

        foreach ($this->providers as $provider) {
            $data[] = $provider->generate();
        }

        return $data;
    }

    public function addProvider(provider $provider): static
    {
        $this->providers[] = $provider;

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function count(): int
    {
        return count($this->providers);
    }
}
