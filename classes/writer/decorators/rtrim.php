<?php

namespace atoum\atoum\writer\decorators;

use atoum\atoum\writer;

class rtrim implements writer\decorator
{
    public function decorate($message)
    {
        return rtrim($message);
    }
}
