<?php

namespace atoum\atoum\writer\decorators;

use atoum\atoum\writer;

class trim implements writer\decorator
{
    public function decorate(string $message): string
    {
        return trim($message);
    }
}
