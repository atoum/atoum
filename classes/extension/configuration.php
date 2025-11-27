<?php

namespace atoum\atoum\extension;

interface configuration
{
    public function serialize(): array;

    public static function unserialize(array $configuration): static;
}
