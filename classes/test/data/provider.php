<?php

namespace atoum\atoum\test\data;

interface provider
{
    public function __invoke(): mixed;

    public function __toString(): string;

    public function generate(): mixed;
}
