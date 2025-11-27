<?php

namespace atoum\atoum\writer\decorators;

use atoum\atoum\writer;

class eol implements writer\decorator
{
    public function decorate(string $message): string
    {
        return $message . PHP_EOL;
    }
}
