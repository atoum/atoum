<?php

namespace atoum\atoum\writer;

interface decorator
{
    public function decorate(string $message): string;
}
