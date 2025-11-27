<?php

namespace atoum\atoum\writer\decorators;

use atoum\atoum\writer;

class rtrim implements writer\decorator
{
    public function decorate(string $message): string
    {
        return rtrim($message);
    }
}
